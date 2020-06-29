<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\DateRepository;
use App\Repository\LevelRepository;
use App\Repository\UserRepository;
use App\Service\ErrorJsonHelper;
use App\Service\UserHelper;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use OpenApi\Annotations as OA;
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
     * @OA\Get(
     *     path="/api/users",
     *     security={"bearer"},
     *     tags={"User"},
     *     description="Get users",
     *     @OA\Response(
     *         response="200",
     *         description="List of all users",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User")),
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="No users found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", description="HTTP Code status"),
     *             @OA\Property(property="message", type="string", description="Returned message"),
     *         ),
     *     ),
     * )
     * @Route("/api/users", name="api_get_users", methods={"GET"})
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function index(UserRepository $userRepository): JsonResponse
    {
        $result = $userRepository->findAll();

        if (!$result) {
            return $this->json(
                ErrorJsonHelper::errorMessage(Response::HTTP_NOT_FOUND, 'No users found.'),
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json($result, Response::HTTP_OK, [], ['groups' => 'user:read']);
    }

    /**
     * @OA\Get(
     *     path="/api/user/{id}",
     *     security={"bearer"},
     *     tags={"User"},
     *     description="Get user",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of User",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="List of all users",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User")),
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="User does not exist",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", description="HTTP Code status"),
     *             @OA\Property(property="message", type="string", description="Returned message"),
     *         ),
     *     ),
     * )
     * @Route("/api/user/{id}", name="api_get_user", methods={"GET"})
     * @param UserRepository $userRepository
     * @param int $id
     * @return JsonResponse
     */
    public function oneUser(UserRepository $userRepository, int $id): JsonResponse
    {
        $result = $userRepository->findOneUser($id);

        if (!$result) {
            return $this->json(
                ErrorJsonHelper::errorMessage(Response::HTTP_NOT_FOUND, 'This user does not exist.'),
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json($result, Response::HTTP_OK, [], ['groups' => 'user:read']);
    }

    /**
     * @OA\Post(
     *     path="/api/signup",
     *     tags={"User"},
     *     description="Create user",
     *     @OA\RequestBody(ref="#/components/requestBodies/UserSignup"),
     *     @OA\Response(
     *         response="200",
     *         description="User created",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User")),
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="User sign up error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", description="HTTP Code status"),
     *             @OA\Property(property="message", type="string", description="Returned message"),
     *         ),
     *     ),
     * )
     * @Route("/api/signup", name="api_signup_user", methods={"POST"})
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     * @param ValidatorInterface $validator
     * @param LevelRepository $levelRepository
     * @param UserHelper $userHelper
     * @param DateRepository $dateRepository
     * @return JsonResponse
     * @throws Exception
     */
    public function createUser(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator, LevelRepository $levelRepository, UserHelper $userHelper, DateRepository $dateRepository): JsonResponse
    {
        $json = $request->getContent();

        $level = $levelRepository->findOneBy(['levelNumber' => 0]);

        // SELECT date FROM date ORDER BY date DESC LIMIT 1
        $currentDate = $dateRepository->findBy(
            array(),                    // $where
            array('date' => 'DESC'),    // $orderBy
            1                      // $limit
        );

        try {
            $user = $serializer->deserialize($json, User::class, 'json');

            if (!preg_match('/[0-3]\d{12}/', $user->getNifNumber())) {
                return $this->json(
                    ErrorJsonHelper::errorMessage(Response::HTTP_BAD_REQUEST, 'Wrong NIF number format.'),
                    Response::HTTP_BAD_REQUEST
                );
            }

            $user
                ->setPassword(password_hash($user->getPassword(), 'argon2id'))
                ->setRegistrationDate(new DateTime())
                ->setLevel($level)
                ->setSavingWater(0)
                ->setSavingWaste(0)
                ->setSavingElectricity(0)
                ->setSavingGas(0)
                ->setRoles(['ROLE_USER'])
            ;

            $userHelper->setAverageUserData($user);
            $userHelper->createUserTask($user, $currentDate[0], $em);

            $errors = $validator->validate($user);

            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            $em->persist($user);
            $em->flush();

            return $this->json($user, Response::HTTP_CREATED, [], ['groups' => 'user:create']);

        } catch (NotEncodableValueException $e) {
            return $this->json(
                ErrorJsonHelper::errorMessage(Response::HTTP_BAD_REQUEST, $e->getMessage()),
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"User"},
     *     description="Login as user. Property username is the email of the User.",
     *     @OA\RequestBody(ref="#/components/requestBodies/UserLogin"),
     *     @OA\Response(
     *         response="200",
     *         description="Connect to an account",
     *         @OA\JsonContent(
     *             properties={
     *                 @OA\Property(property="user", type="array", @OA\Items(ref="#/components/schemas/User")),
     *                 @OA\Property(property="data", type="array", @OA\Items(
     *                     @OA\Property(property="refresh_token", type="string"),
     *                     @OA\Property(property="token", type="string"))
     *                 ),
     *             },
     *         ),
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="User login error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", description="HTTP Code status"),
     *             @OA\Property(property="message", type="string", description="Returned message"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", description="HTTP Code status"),
     *             @OA\Property(property="message", type="string", description="Returned message"),
     *         ),
     *     ),
     * )
     * @Route("/api/login", name="api_login_user", methods={"POST"})
     * @param UserRepository $userRepository
     * @param Request $request
     * @return JsonResponse
     */
    public function login(UserRepository $userRepository, Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);

        $user = $userRepository->findOneBy(['email' => $content['username']]);
        $data = $userRepository->getUserDatas($user->getId());

        if (!$user) {
            return $this->json(
                ErrorJsonHelper::errorMessage(Response::HTTP_NOT_FOUND, 'This user does not exist.'),
                Response::HTTP_NOT_FOUND
            );
        }

        $errorMessage = '';

        $ch = curl_init();

        $options = [
            CURLOPT_POST  => 1,
            CURLOPT_URL => sprintf('http://%s/api/login_check', $_SERVER['HTTP_HOST']),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($content),
            CURLOPT_FAILONERROR => true,
        ];

        curl_setopt_array($ch, $options);

        $result = json_decode(curl_exec($ch), true);

        if (curl_errno($ch)) {
            $errorMessage = curl_error($ch);
        }

        curl_close($ch);

        if ($errorMessage !== '') {
            return $this->json(
                ErrorJsonHelper::errorMessage(Response::HTTP_BAD_REQUEST, $errorMessage),
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->json([
            'user' => $user,
            'tokens' => $result,
            'additionalDatas' => $data,
        ], Response::HTTP_OK, [], ['groups' => 'user:create']);
    }
}
