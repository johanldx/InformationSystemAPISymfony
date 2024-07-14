<?php

namespace App\Controller;

use App\Entity\Facture;
use App\Repository\FactureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/facture')]
class FactureController extends AbstractController
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('/', name: 'api_facture_index', methods: ['GET'])]
    public function index(FactureRepository $factureRepository): JsonResponse
    {
        $factures = $factureRepository->findAll();
        return $this->json($factures);
    }

    #[Route('/', name: 'api_facture_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['amount']) || !isset($data['due_date']) || !isset($data['customer_email'])) {
            return $this->json(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        $facture = new Facture();
        $facture->setAmount($data['amount']);
        $facture->setDueDate(new \DateTime($data['due_date']));
        $facture->setCustomerEmail($data['customer_email']);

        $em->persist($facture);
        $em->flush();

        $sujet = "Confirmation de votre commande";
        
        $message = "Votre commande a été passée avec succès.<br>Montant de " . $facture->getAmount() . " €<br>Date d'échéance : " . $facture->getDueDate()->format('d/m/Y');

        try {
            $response = $this->httpClient->request('POST', 'http://localhost:8002/notification/', [
                'json' => [
                    'email_recipient' => $facture->getCustomerEmail(),
                    'message' => $message,
                    'sujet' => $sujet
                ],
            ]);

            if ($response->getStatusCode() !== 201) {
                return $this->json(['error' => 'Failed to notify external API'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (TransportExceptionInterface $e) {
            return $this->json(['error' => 'Could not reach the notification service. Please try again later.'], Response::HTTP_SERVICE_UNAVAILABLE);
        } catch (ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface $e) {
            return $this->json(['error' => 'An error occurred while notifying the external API.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($facture, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_facture_show', methods: ['GET'])]
    public function show(int $id, FactureRepository $factureRepository): JsonResponse
    {
        $facture = $factureRepository->find($id);

        if (!$facture) {
            return $this->json(['error' => 'Facture not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($facture);
    }

    #[Route('/{id}', name: 'api_facture_edit', methods: ['PUT'])]
    public function edit(int $id, Request $request, FactureRepository $factureRepository, EntityManagerInterface $em): JsonResponse
    {
        $facture = $factureRepository->find($id);

        if (!$facture) {
            return $this->json(['error' => 'Facture not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['amount']) || !isset($data['due_date']) || !isset($data['customer_email'])) {
            return $this->json(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        $facture->setAmount($data['amount']);
        $facture->setDueDate(new \DateTime($data['due_date']));
        $facture->setCustomerEmail($data['customer_email']);

        $em->flush();

        return $this->json($facture);
    }

    #[Route('/{id}', name: 'api_facture_delete', methods: ['DELETE'])]
    public function delete(int $id, FactureRepository $factureRepository, EntityManagerInterface $em): JsonResponse
    {
        $facture = $factureRepository->find($id);

        if (!$facture) {
            return $this->json(['error' => 'Facture not found'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($facture);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
