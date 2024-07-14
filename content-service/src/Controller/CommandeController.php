<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/commande')]
class CommandeController extends AbstractController
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('/', name: 'create_commande', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['product_id'], $data['customer_email'], $data['quantity'], $data['total_price'])) {
            return $this->json(['status' => 'Invalid data'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $commande = new Commande();
        $commande->setProductId($data['product_id']);
        $commande->setCustomerEmail($data['customer_email']);
        $commande->setQuantity($data['quantity']);
        $commande->setTotalPrice($data['total_price']);

        $entityManager->persist($commande);
        $entityManager->flush();

        try {
            $response = $this->httpClient->request('POST', 'http://localhost:8001/facture/', [
                'json' => [
                    'customer_email' => $data['customer_email'],
                    'amount' => $data['total_price'],
                    'due_date' => (new \DateTime('+30 days'))->format('m/d/Y'),
                ],
            ]);

            if ($response->getStatusCode() !== 201) {
                return $this->json(['error' => 'Failed to create invoice with external API'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            }

            $invoice = $response->toArray();
        } catch (TransportExceptionInterface $e) {
            return $this->json(['error' => 'Could not reach the billing service. Please try again later.'], JsonResponse::HTTP_SERVICE_UNAVAILABLE);
        } catch (ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface $e) {
            return $this->json(['error' => 'An error occurred while communicating with the billing API.'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['status' => 'Commande created and invoice created!'], JsonResponse::HTTP_CREATED);
    }

    #[Route('/', name: 'api_facture_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): JsonResponse
    {
        $commandes = $commandeRepository->findAll();
        return $this->json($commandes);
    }

    #[Route('/{id}', name: 'read_commande', methods: ['GET'])]
    public function read(int $id, CommandeRepository $commandeRepository): JsonResponse
    {
        $commande = $commandeRepository->find($id);

        if (!$commande) {
            return $this->json(['status' => 'Commande not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->json($commande);
    }

    #[Route('/{id}', name: 'update_commande', methods: ['PUT'])]
    public function update(int $id, Request $request, CommandeRepository $commandeRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $commande = $commandeRepository->find($id);

        if (!$commande) {
            return $this->json(['status' => 'Commande not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['product_id'], $data['customer_email'], $data['quantity'], $data['total_price'])) {
            return $this->json(['status' => 'Invalid data'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $commande->setProductId($data['product_id']);
        $commande->setCustomerEmail($data['customer_email']);
        $commande->setQuantity($data['quantity']);
        $commande->setTotalPrice($data['total_price']);

        $entityManager->flush();

        return $this->json(['status' => 'Commande updated!']);
    }

    #[Route('/{id}', name: 'delete_commande', methods: ['DELETE'])]
    public function delete(int $id, CommandeRepository $commandeRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $commande = $commandeRepository->find($id);

        if (!$commande) {
            return $this->json(['status' => 'Commande not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $entityManager->remove($commande);
        $entityManager->flush();

        return $this->json(['status' => 'Commande deleted!']);
    }
}
