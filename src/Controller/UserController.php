<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/api/users", name="users", methods={"GET"})
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function index(UserRepository $userRepository): JsonResponse
    {
        return $this->json($userRepository->findAll(), Response::HTTP_OK, [], ['groups' => 'user:read']);
    }

    /**
     * @Route("/api/user/{id}", name="user", methods={"GET"})
     * @param User $user
     * @return JsonResponse
     */
    public function oneUser(User $user): JsonResponse
    {
        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'user:read']);
    }
}
