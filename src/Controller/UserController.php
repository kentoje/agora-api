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
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
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
     *     path="/api/admin/users",
     *     security={"bearer"},
     *     tags={"User"},
     *     description="Get users",
     *     @OA\Response(
     *         response="200",
     *         description="List of all users",
     *         @OA\JsonContent(type="array", @OA\Items(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="roles", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="firstName", type="string"),
     *             @OA\Property(property="lastName", type="string"),
     *             @OA\Property(property="image", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="agoraNumber", type="string"),
     *             @OA\Property(property="nbResident", type="integer"),
     *             @OA\Property(property="livingArea", type="integer"),
     *             @OA\Property(property="gas", type="boolean"),
     *             @OA\Property(property="insulation", type="boolean"),
     *             @OA\Property(property="gasAverageConsumption", type="number", format="float"),
     *             @OA\Property(property="waterAverageConsumption", type="number", format="float"),
     *             @OA\Property(property="electricityAverageConsumption", type="number", format="float"),
     *             @OA\Property(property="wasteAverageConsumption", type="number", format="float"),
     *             @OA\Property(property="registrationDate", type="string", format="date"),
     *             @OA\Property(property="navigoNumber", type="string"),
     *             @OA\Property(property="level", type="object", properties={
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="levelNumber", type="integer"),
     *                 @OA\Property(property="reductionRate", type="number", format="float"),
     *             }),
     *             @OA\Property(property="savingWater", type="integer"),
     *             @OA\Property(property="savingTransport", type="integer"),
     *             @OA\Property(property="savingElectricity", type="integer"),
     *             @OA\Property(property="savingGas", type="integer"),
     *             @OA\Property(property="savingWaste", type="integer"),
     *         )),
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="No users found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", description="HTTP Code status"),
     *             @OA\Property(property="message", type="string", description="Returned message"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="The user does not have the ROLE_ADMIN role.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", description="HTTP Code status"),
     *             @OA\Property(property="message", type="string", description="Returned message"),
     *         ),
     *     ),
     * )
     * @Route("/api/admin/users", name="api_admin_get_users", methods={"GET"})
     */
    public function index(UserHelper $userHelper, UserRepository $userRepository, Request $request, JWTEncoderInterface $JWTEncoder): JsonResponse
    {

        if (!$userHelper->checkAdmin($request, $JWTEncoder)) {
            return $this->json(
                ErrorJsonHelper::errorMessage(Response::HTTP_FORBIDDEN, 'ROLE_ADMIN is required.'),
                Response::HTTP_FORBIDDEN
            );
        }

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
     *     path="/api/admin/user/{id}",
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
     *         @OA\JsonContent(type="array", @OA\Items(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="roles", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="firstName", type="string"),
     *             @OA\Property(property="lastName", type="string"),
     *             @OA\Property(property="image", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="agoraNumber", type="string"),
     *             @OA\Property(property="nbResident", type="integer"),
     *             @OA\Property(property="livingArea", type="integer"),
     *             @OA\Property(property="gas", type="boolean"),
     *             @OA\Property(property="insulation", type="boolean"),
     *             @OA\Property(property="gasAverageConsumption", type="number", format="float"),
     *             @OA\Property(property="waterAverageConsumption", type="number", format="float"),
     *             @OA\Property(property="electricityAverageConsumption", type="number", format="float"),
     *             @OA\Property(property="wasteAverageConsumption", type="number", format="float"),
     *             @OA\Property(property="registrationDate", type="string", format="date"),
     *             @OA\Property(property="navigoNumber", type="string"),
     *             @OA\Property(property="level", type="object", properties={
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="levelNumber", type="integer"),
     *                 @OA\Property(property="reductionRate", type="number", format="float"),
     *             }),
     *             @OA\Property(property="savingWater", type="integer"),
     *             @OA\Property(property="savingTransport", type="integer"),
     *             @OA\Property(property="savingElectricity", type="integer"),
     *             @OA\Property(property="savingGas", type="integer"),
     *             @OA\Property(property="savingWaste", type="integer"),
     *         )),
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="User does not exist",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", description="HTTP Code status"),
     *             @OA\Property(property="message", type="string", description="Returned message"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="The user does not have the ROLE_ADMIN role.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", description="HTTP Code status"),
     *             @OA\Property(property="message", type="string", description="Returned message"),
     *         ),
     *     ),
     * )
     * @Route("/api/admin/user/{id}", name="api_admin_get_user", methods={"GET"})
     */
    public function oneUser(UserRepository $userRepository, UserHelper $userHelper, Request $request, JWTEncoderInterface $JWTEncoder, int $id ): JsonResponse
    {
        if (!$userHelper->checkAdmin($request, $JWTEncoder)) {
            return $this->json(
                ErrorJsonHelper::errorMessage(Response::HTTP_FORBIDDEN, 'ROLE_ADMIN is required.'),
                Response::HTTP_FORBIDDEN
            );
        }

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
     * @OA\Get(
     *     path="/api/user/update/{id}",
     *     tags={"User"},
     *     security={"bearer"},
     *     description="Get updatable user info",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of User",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="List all updatable users",
     *         @OA\JsonContent(
     *             @OA\Property(property="level", type="object", properties={
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="levelNumber", type="integer"),
     *                 @OA\Property(property="reductionRate", type="number", format="float"),
     *             }),
     *             @OA\Property(property="additionalDatas", type="object", properties={
     *                 @OA\Property(property="data", type="object", properties={
     *                     @OA\Property(property="userId", type="integer"),
     *                     @OA\Property(property="mesureGas", type="integer"),
     *                     @OA\Property(property="mesureWater", type="integer"),
     *                     @OA\Property(property="mesureWaste", type="integer"),
     *                     @OA\Property(property="mesureElectricity", type="integer"),
     *                     @OA\Property(property="saving_electricity", type="integer"),
     *                     @OA\Property(property="saving_waste", type="integer"),
     *                     @OA\Property(property="saving_gas", type="integer"),
     *                     @OA\Property(property="saving_water", type="integer"),
     *                     @OA\Property(property="saving_transport", type="integer"),
     *                     @OA\Property(property="nbMonthsRegistered", type="integer"),
     *                     @OA\Property(property="nbValidatedTaskWater", type="integer"),
     *                     @OA\Property(property="nbValidatedTaskGas", type="integer"),
     *                     @OA\Property(property="nbValidatedTaskWaste", type="integer"),
     *                     @OA\Property(property="nbValidatedTaskElec", type="integer"),
     *                     @OA\Property(property="nbValidateTaskInThisYear", type="integer" ),
     *                 }),
     *                 @OA\Property(property="tasks", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="date_id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="unit", type="string"),
     *                     @OA\Property(property="validate", type="integer"),
     *                 ))},
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="User does not exist",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", description="HTTP Code status"),
     *             @OA\Property(property="message", type="string", description="Returned message"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="The user does not match",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", description="HTTP Code status"),
     *             @OA\Property(property="message", type="string", description="Returned message"),
     *         ),
     *     ),
     * )
     * @Route("/api/user/update/{id}", name="api_update_user_data", methods={"GET"})
     */
    public function getUserUpdatableDatas(UserHelper $userHelper, UserRepository $userRepository, Request $request, JWTEncoderInterface $JWTEncoder, int $id): JsonResponse
    {
        $userInfo = $userHelper->checkUser($id,  $userRepository,  $request,  $JWTEncoder);

        if (!$userInfo['user']) {
            return $this->json(
                ErrorJsonHelper::errorMessage(Response::HTTP_NOT_FOUND, 'This user does not exist.'),
                Response::HTTP_NOT_FOUND
            );
        }

        if ($userInfo['user']->getUsername() !== $userInfo['username']) {
            return $this->json(
                ErrorJsonHelper::errorMessage(Response::HTTP_FORBIDDEN, 'The user does not match.'),
                Response::HTTP_FORBIDDEN
            );
        }

        $data = $userRepository->getUserDatas($userInfo['user']->getId());

        return $this->json([
            'level' => $userInfo['user']->getLevel(),
            'additionalDatas' => $data,
        ], Response::HTTP_OK, [], ['groups' => 'user:updatable']);
    }

    /**
     * @OA\Get(
     *     path="/api/user/tasks/{id}/{date}",
     *     tags={"User"},
     *     security={"bearer"},
     *     description="Get user's tasks by date",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of User",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="path",
     *         description="Year of tasks",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Get all tasks by specific year.",
     *         @OA\JsonContent(
     *              @OA\Property(property="date", type="object", properties={
     *                  @OA\Property(property="Déchets", type="object", properties={
     *                      @OA\Property(property="isValidate", type="string"),
     *                      @OA\Property(property="percent", type="string"),
     *                      @OA\Property(property="Average", type="string")
     *                  }),
     *                  @OA\Property(property="Eau", type="object", properties={
     *                      @OA\Property(property="isValidate", type="string"),
     *                      @OA\Property(property="percent", type="string"),
     *                      @OA\Property(property="Average", type="string")
     *                  }),
     *                  @OA\Property(property="Electricité", type="object", properties={
     *                      @OA\Property(property="isValidate", type="string"),
     *                      @OA\Property(property="percent", type="string"),
     *                      @OA\Property(property="Average", type="string")
     *                  }),
     *                  @OA\Property(property="Gaz", type="object", properties={
     *                      @OA\Property(property="isValidate", type="string"),
     *                      @OA\Property(property="percent", type="string"),
     *                      @OA\Property(property="Average", type="string")
     *                  }),
     *                  @OA\Property(property="transportsIsValidate", type="boolean")
     *               })
     *         ),
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="User does not exist",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", description="HTTP Code status"),
     *             @OA\Property(property="message", type="string", description="Returned message"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="The user does not match",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", description="HTTP Code status"),
     *             @OA\Property(property="message", type="string", description="Returned message"),
     *         ),
     *     ),
     * )
     * @Route("/api/user/tasks/{id}/{year}", name="api_all_user_tasks", methods={"GET"})
     */
    public function getAllUserTasks(UserHelper $userHelper, UserRepository $userRepository, Request $request, JWTEncoderInterface $JWTEncoder, int $id, int $year): JsonResponse
    {
        $userInfo = $userHelper->checkUser($id,  $userRepository,  $request,  $JWTEncoder);

        if (!$userInfo['user']) {
            return $this->json(
                ErrorJsonHelper::errorMessage(Response::HTTP_NOT_FOUND, 'This user does not exist.'),
                Response::HTTP_NOT_FOUND
            );
        }

        if ($userInfo['user']->getUsername() !== $userInfo['username']) {
            return $this->json(
                ErrorJsonHelper::errorMessage(Response::HTTP_FORBIDDEN, 'The user does not match.'),
                Response::HTTP_FORBIDDEN
            );
        }

        $tasksByDates = $userRepository->getAllUserTasks($userInfo['user']->getId(),$year);

        $response = [];
        foreach ($tasksByDates as $tasksByDates) {
            $validateTransports = $tasksByDates["userHaveNavigoNumber"] && $tasksByDates["navigo_subscription"];
            $response += [$tasksByDates["date"] => [
                "Déchets" => [
                    "isValidate" => $tasksByDates["wasteTaskValidate"],
                    "percent" => $tasksByDates["wastePercent"],
                    "Average" => $tasksByDates["waste_average_consumption"],
                ],
                "Eau" => [
                    "isValidate" => $tasksByDates["waterTaskValidate"],
                    "percent" => $tasksByDates["waterPercent"],
                    "Average" => $tasksByDates["water_average_consumption"],
                ],
                "Electricité" => [
                    "isValidate" => $tasksByDates["electricityTaskValidate"],
                    "percent" => $tasksByDates["electricityPercent"],
                    "Average" => $tasksByDates["electricity_average_consumption"],
                ],
                "Gaz" => [
                    "isValidate" => $tasksByDates["gasTaskValidate"],
                    "percent" => $tasksByDates["gasPercent"],
                    "Average" => $tasksByDates["gas_average_consumption"],
                ],
                "transportsIsValidate" => $validateTransports,
            ]];
        }

        return $this->json($response, Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/user/analytics/{id}",
     *     tags={"User"},
     *     security={"bearer"},
     *     description="Get all datas for analytics page",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of User",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="List all analytics datas",
     *         @OA\JsonContent(
     *             @OA\Property(property="thisYear", type="object", properties={
     *               @OA\Property(property="Déchets", type="object", properties={
     *                  @OA\Property(property="nbtaskValidate", type="integer"),
     *                  @OA\Property(property="allTasks", type="array", @OA\Items(
     *                     @OA\Property(property="nbValidateTaskByType", type="string"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="date", type="string"),
     *                  )),
     *               }),
     *               @OA\Property(property="Eau", type="object", properties={
     *                  @OA\Property(property="nbtaskValidate", type="integer"),
     *                  @OA\Property(property="allTasks", type="array", @OA\Items(
     *                     @OA\Property(property="nbValidateTaskByType", type="string"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="date", type="string"),
     *                  )),
     *               }),
     *               @OA\Property(property="Electricité", type="object", properties={
     *                  @OA\Property(property="nbtaskValidate", type="integer"),
     *                  @OA\Property(property="allTasks", type="array", @OA\Items(
     *                     @OA\Property(property="nbValidateTaskByType", type="string"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="date", type="string"),
     *                  )),
     *               }),
     *               @OA\Property(property="Gaz", type="object", properties={
     *                  @OA\Property(property="nbtaskValidate", type="integer"),
     *                  @OA\Property(property="allTasks", type="array", @OA\Items(
     *                     @OA\Property(property="nbValidateTaskByType", type="string"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="date", type="string"),
     *                  )),
     *               }),
     *               @OA\Property(property="Transports", type="object", properties={
     *               @OA\Property(property="nbtaskValidate", type="integer"),
     *                  @OA\Property(property="allTasks", type="array", @OA\Items(
     *                     @OA\Property(property="nbValidateTaskByType", type="string"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="date", type="string"),
     *                  )),
     *               }),
     *             }),
     *             @OA\Property(property="allYears", type="object", properties={
     *                   @OA\Property(property="nbUser", type="string"),
     *                   @OA\Property(property="nbValidateTask", type="string"),
     *               },
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="User does not exist",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", description="HTTP Code status"),
     *             @OA\Property(property="message", type="string", description="Returned message"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="The user does not match",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", description="HTTP Code status"),
     *             @OA\Property(property="message", type="string", description="Returned message"),
     *         ),
     *     ),
     * )
     * @Route("/api/user/analytics/{id}", name="api_get_analytics_data", methods={"GET"})
    */
    public function getDataAnalytics(UserHelper $userHelper, UserRepository $userRepository, Request $request, JWTEncoderInterface $JWTEncoder, int $id): JsonResponse
    {
        $userInfo = $userHelper->checkUser($id,  $userRepository,  $request,  $JWTEncoder);

        if (!$userInfo['user']) {
            return $this->json(
                ErrorJsonHelper::errorMessage(Response::HTTP_NOT_FOUND, 'This user does not exist.'),
                Response::HTTP_NOT_FOUND
            );
        }

        if ($userInfo['user']->getUsername() !== $userInfo['username']) {
            return $this->json(
                ErrorJsonHelper::errorMessage(Response::HTTP_FORBIDDEN, 'The user does not match.'),
                Response::HTTP_FORBIDDEN
            );
        }

        $datas = $userRepository->getAllDataAnalytics();

        $response = [
            'thisYear' => [
                "Déchets" => [
                    "nbtaskValidate" => 0,
                    "allTasks" => []
                ],
                "Eau" => [
                    "nbtaskValidate" => 0,
                    "allTasks" => []
                ],
                "Electricité" => [
                    "nbtaskValidate" => 0,
                    "allTasks" => []
                ],
                "Gaz" => [
                    "nbtaskValidate" => 0,
                    "allTasks" => []
                ],
                "Transports" => [
                    "nbtaskValidate" => 0,
                    "allTasks" => []
                ]],
            'allYears' => $datas['allYears'][0]
        ];

        foreach ($datas['thisYear'] as $data) {
            foreach ($response['thisYear'] as $key => $type) {
                if ($key === $data["name"]) {
                    $type["nbtaskValidate"] += $data['nbValidateTaskByType'];
                    array_push($type["allTasks"], $data);
                }
                $response['thisYear'][$key] = $type;
            }
        }

        return $this->json($response, Response::HTTP_OK);
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
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="roles", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="firstName", type="string"),
     *             @OA\Property(property="lastName", type="string"),
     *             @OA\Property(property="image", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="agoraNumber", type="string"),
     *             @OA\Property(property="nbResident", type="integer"),
     *             @OA\Property(property="livingArea", type="integer"),
     *             @OA\Property(property="gas", type="boolean"),
     *             @OA\Property(property="insulation", type="boolean"),
     *             @OA\Property(property="gasAverageConsumption", type="number", format="float"),
     *             @OA\Property(property="waterAverageConsumption", type="number", format="float"),
     *             @OA\Property(property="electricityAverageConsumption", type="number", format="float"),
     *             @OA\Property(property="wasteAverageConsumption", type="number", format="float"),
     *             @OA\Property(property="registrationDate", type="string", format="date"),
     *             @OA\Property(property="navigoNumber", type="string"),
     *             @OA\Property(property="level", type="object", properties={
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="levelNumber", type="integer"),
     *                 @OA\Property(property="reductionRate", type="number", format="float"),
     *             }),
     *             @OA\Property(property="savingWater", type="integer"),
     *             @OA\Property(property="savingTransport", type="integer"),
     *             @OA\Property(property="savingElectricity", type="integer"),
     *             @OA\Property(property="savingGas", type="integer"),
     *             @OA\Property(property="savingWaste", type="integer"),
     *         ),
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
                ->setSavingTransport(0)
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
     *                 @OA\Property(property="user", type="object", properties={
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="roles", type="array", @OA\Items(type="string")),
     *                     @OA\Property(property="firstName", type="string"),
     *                     @OA\Property(property="lastName", type="string"),
     *                     @OA\Property(property="image", type="string"),
     *                     @OA\Property(property="email", type="string", format="email"),
     *                     @OA\Property(property="agoraNumber", type="string"),
     *                     @OA\Property(property="nbResident", type="integer"),
     *                     @OA\Property(property="livingArea", type="integer"),
     *                     @OA\Property(property="gas", type="boolean"),
     *                     @OA\Property(property="insulation", type="boolean"),
     *                     @OA\Property(property="gasAverageConsumption", type="number", format="float"),
     *                     @OA\Property(property="waterAverageConsumption", type="number", format="float"),
     *                     @OA\Property(property="electricityAverageConsumption", type="number", format="float"),
     *                     @OA\Property(property="wasteAverageConsumption", type="number", format="float"),
     *                     @OA\Property(property="registrationDate", type="string", format="date"),
     *                     @OA\Property(property="navigoNumber", type="string"),
     *                 }),
     *                 @OA\Property(property="tokens", type="object", properties={
     *                     @OA\Property(property="token", type="string"),
     *                     @OA\Property(property="refresh_token", type="string"),
     *                 }),
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
     */
    public function login(UserRepository $userRepository, Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);

        $user = $userRepository->findOneBy(['email' => $content['username']]);

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
            CURLOPT_URL => $this->getParameter('kernel.environment') === 'test'
                ? 'http://localhost:8000/api/login_check'
                : sprintf('http://%s/api/login_check', $_SERVER['HTTP_HOST']),
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
        ], Response::HTTP_OK, [], ['groups' => 'user:login']);
    }
}
