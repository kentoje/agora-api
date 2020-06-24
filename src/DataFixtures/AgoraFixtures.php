<?php

namespace App\DataFixtures;

use App\Entity\Date;
use App\Entity\Level;
use App\Entity\Mesure;
use App\Entity\Task;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AgoraFixtures extends Fixture
{
    public function  getTaskValue(string $taskName, Mesure $userMesure): array
    {
        switch ($taskName) {
            case "Eau":
                $taskArray = [
                    "type" => $taskName,
                    "unit" => "L",
                    "user_average" => $userMesure->getToMesure()->getWaterAverageConsumption(),
                    "mesure" => $userMesure->getWater()
                ];
                break;

            case "Electricté":
                $taskArray = [
                    "type" => $taskName,
                    "unit" => "kW/h",
                    "user_average" => $userMesure->getToMesure()->getElectricityAverageConsumption(),
                    "mesure" => $userMesure->getElectricity()
                ];
                break;

            case "Gaz":
                $taskArray = [
                    "type" => $taskName,
                    "unit" => "KW/h",
                    "user_average" => $userMesure->getToMesure()->getGasAverageConsumption(),
                    "mesure" => $userMesure->getGas()
                ];
                break;

            case "Déchêts":
                $taskArray = [
                    "type" => $taskName,
                    "unit" => "Kg",
                    "user_average" => $userMesure->getToMesure()->getWasteAverageConsumption(),
                    "mesure" => $userMesure->getWaste()
                ];
                break;
                
            default:
                $taskArray = [
                    "type" => $taskName,
                    "unit" => "None",
                    "user_average" => $userMesure->getToMesure()->getNavigoNumber(),
                    "mesure" => $userMesure->getNavigoSubscription()
                ];
        }
        return $taskArray;
    }

    public function getUserLevel(int $countValidateTask, array $dbLevel): Level
    {
        if ($countValidateTask < 5) {
            $level = $dbLevel[0];
        }
        elseif ($countValidateTask >= 5 and $countValidateTask < 10) {
            $level = $dbLevel[1];
        }
        elseif ($countValidateTask >= 10 and $countValidateTask < 15) {
            $level = $dbLevel[2];
        }
        elseif ($countValidateTask >= 15 and $countValidateTask < 20) {
            $level = $dbLevel[3];
        }
        elseif ($countValidateTask >= 20 and $countValidateTask < 25) {
            $level = $dbLevel[4];
        }
        elseif ($countValidateTask >= 25 and $countValidateTask < 30) {
            $level = $dbLevel[5];
        }
        elseif ($countValidateTask >= 30 and $countValidateTask < 35) {
            $level = $dbLevel[6];
        }
        elseif ($countValidateTask >= 35 and $countValidateTask < 40) {
            $level = $dbLevel[7];
        }
        elseif ($countValidateTask >= 40 and $countValidateTask < 45) {
            $level = $dbLevel[8];
        }
        elseif ($countValidateTask >= 45 and $countValidateTask < 50) {
            $level = $dbLevel[9];
        }
        elseif ($countValidateTask >= 50 and $countValidateTask < 55) {
            $level = $dbLevel[10];
        }
        elseif ($countValidateTask >= 55 and $countValidateTask < 60) {
            $level = $dbLevel[11];
        }
        else {
            $level = $dbLevel[12];
        }
        return $level;
    }

    public function getSavingMesure(array $dbTask, User $user, array $mesures): User
    {
        foreach ($dbTask as $task) {
            foreach ($mesures as $mesure) {
                if (date_diff(new DateTime('first day of january'), $task->getDate()->getDate())->format('%R%a') >= 0
                    and $task->getValidate()
                    and $mesure->getToMesure() === $user
                    and $task->getDate() === $mesure->getDate()
                    and $task->getUser()[0] === $user
                ) {
                    switch ($task->getName()) {
                        case "Eau":
                            $user->setSavingWater($user->getSavingWater() + ($user->getWaterAverageConsumption() - $mesure->getWater()));
                            break;
                        case "Electricté":
                            $user->setSavingElectricity($user->getSavingElectricity() + ($user->getElectricityAverageConsumption() - $mesure->getElectricity()));
                            break;
                        case "Gaz":
                            $user->setSavingGas($user->getSavingGas() + ($user->getGasAverageConsumption() - $mesure->getGas()));
                            break;
                        case "Déchêts":
                            $user->setSavingWaste($user->getSavingWaste() + ($user->getWasteAverageConsumption() - $mesure->getWaste()));
                            break;
                    }

                }

            }

        }
        return $user;

    }

    public function load(ObjectManager $manager)
    {
        $limit = 25;

        $levelArr = [
            "0" => 0.6,
            "1" => 1.2,
            "2" => 1.8,
            "3" => 2.4,
            "4" => 3.0,
            "5" => 3.6,
            "6" => 4.2,
            "7" => 4.8,
            "8" => 5.4,
            "9" => 6.0,
            "10" => 6.6,
            "11" => 7.2,
            "12" => 8.0
        ];

        $taskNameArr = ['Eau', 'Electricté', 'Gaz', 'Transports', 'Déchêts'];

        $dbLevel = [];

        foreach ($levelArr as $key => $value) {
            $level = new Level();
            $level
                ->setLevelNumber($key)
                ->setReductionRate($value)
            ;

            $dbLevel[] = $level;

            $manager->persist($level);
        }

        $faker = Factory::create('fr_FR');
        for ($i = 1; $i <= $limit; $i++) {
            $user = new User();
            $user
                ->setFirstName($faker->firstName)
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
//                ->setPassword(password_hash($faker->password, PASSWORD_ARGON2ID))
                /* Test JWT Token with test password */
                ->setPassword(password_hash('test', 'argon2id'))
                ->setAgoraNumber($faker->unique()->randomNumber(8))
                ->setNbResident($faker->randomElement($array = [1, 2, 3, 4, 5, 6]))
                ->setLivingArea($faker->numberBetween($min = 15, $max = 300))
                ->setGas($faker->boolean)
                ->setInsulation($faker->boolean)
                ->setSocialSecurityNumber($faker->regexify('[1-2]{1}[0-9]{2}(0[1-9]|1[0-2])[0-9]{2}[0-9]{3}[0-9]{3}[0-9]{2}'))
                ->setRoles($faker->randomElement($array = [['ROLE_USER'], ['ROLE_ADMIN']]))
                ->setGasAverageConsumption($faker->randomFloat(2, 0, 6260))
                ->setElectricityAverageConsumption($faker->randomFloat(2, 0, 800))
                ->setWaterAverageConsumption($faker->randomFloat(2, 0, 1300))
                ->setWasteAverageConsumption($faker->randomFloat(0, 0, 300))
                ->setRegistrationDate($faker->dateTime)
                ->setNavigoNumber($faker->unique()->randomNumber(8))
                ->setLevel($faker->randomElement($array = $dbLevel))
                ->setSavingWater(0)
                ->setSavingWaste(0)
                ->setSavingElectricity(0)
                ->setSavingGas(0)
            ;
            $dbUser[] = $user;
            $manager->persist($user);
        }

        $aymeric = new User();
        $aymeric
            ->setFirstName("Aymeric")
            ->setLastName("Mayeux")
            ->setEmail("aymeric.mayeux@hetic.net")
            ->setPassword(password_hash('azerty', 'argon2id'))
            ->setAgoraNumber($faker->unique()->randomNumber(8))
            ->setNbResident(3)
            ->setLivingArea(175)
            ->setGas(true)
            ->setInsulation(true)
            ->setSocialSecurityNumber($faker->regexify('[1-2]{1}[0-9]{2}(0[1-9]|1[0-2])[0-9]{2}[0-9]{3}[0-9]{3}[0-9]{2}'))
            ->setRoles(['ROLE_ADMIN'])
            ->setGasAverageConsumption($faker->randomFloat(2, 0, 6260))
            ->setElectricityAverageConsumption($faker->randomFloat(2, 0, 800))
            ->setWaterAverageConsumption($faker->randomFloat(2, 0, 1300))
            ->setWasteAverageConsumption($faker->randomFloat(0, 0, 300))
            ->setRegistrationDate($faker->dateTime)
            ->setNavigoNumber($faker->unique()->randomNumber(8))
            ->setLevel($faker->randomElement($array = $dbLevel))
            ->setSavingWater(0)
            ->setSavingWaste(0)
            ->setSavingElectricity(0)
            ->setSavingGas(0)
        ;
        $manager->persist($aymeric);
        array_push($dbUser, $aymeric);

        for ($i = 0; $i <= 11; $i++) {
            $date = new Date();
            $newDate = new DateTime();
            $newDate->modify('-'.$i.' months');
            $date->setDate($newDate);
            $manager->persist($date);
            $dbDate[] = $date;
        }
        $manager->flush();


        foreach($dbUser as $user) {
            foreach($dbDate as $date) {
                $mesure = new Mesure();
                $mesure
                    ->setWater($faker->randomFloat(2, 0, 1300))
                    ->setElectricity($faker->randomFloat(2, 0, 800))
                    ->setGas($faker->randomFloat(2, 0, 6260))
                    ->setWaste($user->getGas() ? $faker->randomFloat(0, 0, 300) : 0)
                    ->setNavigoSubscription($user->getNavigoNumber() ? $faker->boolean() : false)
                    ->setToMesure($user)
                    ->setDate($date)
                ;
                $mesures[] = $mesure;
                $manager->persist($mesure);
            }
        }

        foreach ($dbUser as $user) {
            $countValidateTask = 0;

            foreach ($taskNameArr as $taskName) {
                foreach ($dbDate as $date) {
                    foreach ($mesures as $mesure) {
                        if ($mesure->getToMesure() === $user and $mesure->getdate() === $date) {
                            $userMesure = $mesure;
                        }
                    }
    
                    $taskValue = $this->getTaskValue($taskName, $userMesure);
    
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
                        ->addUser($userMesure->getToMesure())
                    ;
                    $manager->persist($task);

                    $dbTask[] = $task;

                    $dateDiff = date_diff(new DateTime('first day of january'), $task->getDate()->getDate())->format('%R%a');
                    if ($task->getValidate() and $dateDiff >= 0) {
                        $countValidateTask += 1;
                    }
                }
            }

            $user->setLevel($this->getUserLevel($countValidateTask, $dbLevel));
            $user = $this->getSavingMesure($dbTask, $user, $mesures);

            $manager->persist($user);

        }
        $manager->flush();
    }
}
