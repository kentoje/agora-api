<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\LevelRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/api/users", name="api_get_users", methods={"GET"})
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function index(UserRepository $userRepository): JsonResponse
    {
        return $this->json($userRepository->findAll(), Response::HTTP_OK, [], ['groups' => 'user:read']);
    }

    /**
     * @Route("/api/user/{id}", name="api_get_user", methods={"GET"})
     * @param User $user
     * @return JsonResponse
     */
    public function oneUser(User $user): JsonResponse
    {
        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'user:read']);
    }

    /**
     * @Route("/api/signup", name="api_signup_user", methods={"POST"})
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     * @param ValidatorInterface $validator
     * @param LevelRepository $levelRepository
     * @return JsonResponse
     */
    public function createUser(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator, LevelRepository $levelRepository): JsonResponse
    {
        $json = $request->getContent();

        $level = $levelRepository->findOneBy(['levelNumber' => 0]);

        try {
            $user = $serializer->deserialize($json, User::class, 'json');
            $user
                ->setPassword(password_hash($user->getPassword(), 'argon2id'))
                ->setRegistrationDate(new DateTime())
                ->setLevel($level)
            ;

            $errors = $validator->validate($user);

            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            $em->persist($user);
            $em->flush();

            return $this->json($user, Response::HTTP_CREATED, [], ['groups' => 'user:create']);

        } catch (NotEncodableValueException $e) {
            return $this->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
