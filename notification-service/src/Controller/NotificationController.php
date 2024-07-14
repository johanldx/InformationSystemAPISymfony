<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use App\Service\PHPMailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/notification')]
class NotificationController extends AbstractController
{
    #[Route('/', name: 'api_notification_index', methods: ['GET'])]
    public function index(NotificationRepository $notificationRepository): JsonResponse
    {
        $notifications = $notificationRepository->findAll();
        return $this->json($notifications);
    }

    #[Route('/{id}', name: 'api_notification_show', methods: ['GET'])]
    public function show(int $id, NotificationRepository $notificationRepository): JsonResponse
    {
        $notification = $notificationRepository->find($id);

        if (!$notification) {
            return $this->json(['error' => 'Notification not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($notification);
    }

    #[Route('/', name: 'api_notification_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $em, PHPMailerService $mailer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email_recipient']) || !isset($data['message']) || !isset($data['sujet'])) {
            return $this->json(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        $notification = new Notification();
        $notification->setEmailRecipient($data['email_recipient']);
        $notification->setMessage($data['message']);
        $notification->setSujet($data['sujet']);

        $em->persist($notification);
        $em->flush();

        try {
            $mailer->sendEmail(
                $notification->getEmailRecipient(),
                $notification->getSujet(),
                $notification->getMessage()
            );
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($notification, Response::HTTP_CREATED);
    }
}
