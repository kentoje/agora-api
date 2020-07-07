<?php

namespace App\Service;

use App\Entity\Date;
use App\Entity\Mesure;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\HttpFoundation\Request;

#water consumption per month for one person
const WATER_CONSUMPTION_MONTH_ONE_PERSON = 130;
#Electricity consumption per month for one square meter without gas
const ELECTRICITY_CONSUMPTION_MONTH_ONE_M2_NO_GAS = 15.83;
#Electricity consumption per month for one square meter with gas
const ELECTRICITY_CONSUMPTION_MONTH_ONE_M2_WITH_GAS = 1.58;
#gas consumption per month for one square meter with good insulation
const GAS_CONSUMPTION_MONTH_ONE_M3_GOOD_INSULATION = 7.5;
#gas consumption per month for one square meter with bad insulation
const GAS_CONSUMPTION_MONTH_ONE_M3_BAD_INSULATION = 12.5;
#Waste produced in one month by one person
const WASTE_PRODUCED_MONTH = 29.5;

const TASK_NAMES = ['Eau', 'Electricité', 'Gaz', 'Transports', 'Déchets'];

class UserHelper
{
    public function setAverageUserData(User $user): User
    {
        #calculation for water
        $user->setWaterAverageConsumption(WATER_CONSUMPTION_MONTH_ONE_PERSON * $user->getNbResident());

        #calculation for Electricity
        if ($user->getGas()) {
            $user->setElectricityAverageConsumption(ELECTRICITY_CONSUMPTION_MONTH_ONE_M2_WITH_GAS * $user->getLivingArea());
        } else {
            $user->setElectricityAverageConsumption(ELECTRICITY_CONSUMPTION_MONTH_ONE_M2_NO_GAS * $user->getLivingArea());
        }

        #calculation for gas
        if ($user->getInsulation() and $user->getGas()) {
            $user->setGasAverageConsumption(GAS_CONSUMPTION_MONTH_ONE_M3_GOOD_INSULATION * $user->getLivingArea());
        } else if ($user->getGas() and !$user->getInsulation()) {
            $user->setGasAverageConsumption(GAS_CONSUMPTION_MONTH_ONE_M3_BAD_INSULATION * $user->getLivingArea());
        } else {
            $user->setGasAverageConsumption(0);
        }

        #calculation for waste
        $user->setWasteAverageConsumption(WASTE_PRODUCED_MONTH * $user->getNbResident());

        return $user;
    }

    public function getTaskValue(string $taskName, Mesure $mesure, User $user): array
    {
        switch ($taskName) {
            case "Eau":
                $taskArray = [
                    "type" => $taskName,
                    "unit" => "L",
                    "user_average" => $user->getWaterAverageConsumption(),
                    "mesure" => $mesure->getWater()
                ];
                break;
            case "Electricité":
                $taskArray = [
                    "type" => $taskName,
                    "unit" => "kW/h",
                    "user_average" => $user->getElectricityAverageConsumption(),
                    "mesure" => $mesure->getElectricity()
                ];
                break;
            case "Gaz":
                $taskArray = [
                    "type" => $taskName,
                    "unit" => "KW/h",
                    "user_average" => $user->getGasAverageConsumption(),
                    "mesure" => $mesure->getGas()
                ];
                break;
            case "Déchets":
                $taskArray = [
                    "type" => $taskName,
                    "unit" => "Kg",
                    "user_average" => $user->getWasteAverageConsumption(),
                    "mesure" => $mesure->getWaste()
                ];
                break;
            default:
                $taskArray = [
                    "type" => $taskName,
                    "unit" => "None",
                    "user_average" => $user->getNavigoNumber(),
                    "mesure" => $mesure->getNavigoSubscription()
                ];
        }
        return $taskArray;
    }


    public function createUserTask(User $user, Date $date, EntityManagerInterface $manager): void
    {
        $mesure = new Mesure();
        $mesure
            ->setWater(0)
            ->setElectricity(0)
            ->setGas(0)
            ->setWaste(0)
            ->setNavigoSubscription($user->getNavigoNumber() ? true : false)
            ->setToMesure($user)
            ->setDate($date);
        $manager->persist($mesure);

        foreach (TASK_NAMES as $taskName) {
            $taskValue = $this->getTaskValue($taskName, $mesure, $user);

            $task = new Task();
            $task
                ->setDate($date)
                ->setName($taskName)
                ->setUnit($taskValue["unit"])
                ->setValidate(
                    $taskValue["type"] === "Transports"
                        ? $taskValue["mesure"]
                        : $taskValue["user_average"] >= $taskValue["mesure"]
                )
                ->setUser($user);;
            $manager->persist($task);
        }
    }

    public function getAverage(array $mesure, string $type, float $average): float
    {
        return ($mesure[$type] * $average) / 3600;
    }

    public function checkUser(int $id, UserRepository $userRepository, Request $request, JWTEncoderInterface $JWTEncoder): array
    {

        $user = $userRepository->findOneBy(['id' => $id]);

        $authorization = $request->headers->get('authorization');
        $jwtToken = explode(' ', $authorization)[1];
        $payload = $JWTEncoder->decode($jwtToken);
        $username = $payload['username'];

        return ['user' => $user, 'username' => $username];

    }

    public function checkAdmin(Request $request, JWTEncoderInterface $JWTEncoder): bool
    {
        $authorization = $request->headers->get('authorization');
        $jwtToken = explode(' ', $authorization)[1];
        $payload = $JWTEncoder->decode($jwtToken);

        return in_array('ROLE_ADMIN', $payload['roles'], true);
    }
}