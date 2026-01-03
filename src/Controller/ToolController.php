<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\TamponEvent;
use App\Repository\TamponEventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tools', name: 'app_tool_')]
class ToolController extends AbstractController
{
    #[Route('/cross-product', name: 'cross_product')]
    public function crossProduct(): Response
    {
        return $this->render('tool/cross_product.html.twig');
    }

    #[Route('/tampon-tracker', name: 'tampon_tracker')]
    public function tamponTracker(TamponEventRepository $repository): Response
    {
        $user = $this->getUser();

        $lastEvent = $repository->findOneBy([
            'user' => $user,
        ], [
            'id' => 'DESC',
        ]);

        $currentStatus = null;
        if ($lastEvent !== null) {
            $currentStatus = $lastEvent->getAction() === 'inserted' ? 'inserted' : 'removed';
        }

        $events = $repository->findBy([
            'user' => $user,
        ], [
            'id' => 'DESC',
        ], 50);

        return $this->render('tool/tampon_tracker.html.twig', [
            'currentStatus' => $currentStatus,
            'events' => $events,
        ]);
    }

    #[Route('/tampon-tracker/toggle', name: 'tampon_tracker_toggle', methods: ['POST'])]
    public function tamponTrackerToggle(
        Request $request,
        TamponEventRepository $repository,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $action = $request->request->get('action');

        if (! in_array($action, ['inserted', 'removed'], true)) {
            throw $this->createNotFoundException('Invalid action');
        }

        $event = new TamponEvent();
        $event->setUser($user);
        $event->setAction($action);
        $event->setCreatedAt(new \DateTimeImmutable());

        $entityManager->persist($event);
        $entityManager->flush();

        return $this->redirectToRoute('app_tool_tampon_tracker');
    }
}
