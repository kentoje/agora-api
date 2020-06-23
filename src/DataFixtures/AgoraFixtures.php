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
                    "unit" => "kW",
                    "user_average" => $userMesure->getToMesure()->getElectricityAverageConsumption(),
                    "mesure" => $userMesure->getElectricity()
                ];
                break;
            case "Gaz":
                $taskArray = [
                    "type" => $taskName,
                    "unit" => "m³",
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
                    "unit" => "Kg",
                    "user_average" => $userMesure->getToMesure()->getNavigoNumber(),
                    "mesure" => $userMesure->getNavigoSubscription()
                ];
        }
        return $taskArray;
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
                ->setGasAverageConsumption($faker->randomFloat(2, 0, 2750))
                ->setElectricityAverageConsumption($faker->randomFloat(2, 0, 2783))
                ->setWaterAverageConsumption($faker->randomFloat(2, 0, 3.3))
                ->setWasteAverageConsumption($faker->randomFloat(0, 0, 93.5))
                ->setRegistrationDate($faker->dateTime)
                ->setNavigoNumber($faker->unique()->randomNumber(8))
                ->setLevel($faker->randomElement($array = $dbLevel))
            ;
            $dbUser[] = $user;
            $manager->persist($user);
        }

        $aymeric = new User();
        $aymeric
            ->setFirstName("Aymeric")
            ->setLastName("Mayeux")
            ->setEmail("aymeric.mayeux@hetic.net")
            ->setPassword(password_hash('azerty', PASSWORD_ARGON2ID))
            ->setAgoraNumber($faker->unique()->randomNumber(8))
            ->setNbResident(3)
            ->setLivingArea(175)
            ->setGas(true)
            ->setInsulation(true)
            ->setSocialSecurityNumber($faker->regexify('[1-2]{1} [0-9]{2} (0[1-9]|1[0-2]) [0-9]{2} [0-9]{3} [0-9]{3} [0-9]{2}'))
            ->setRoles(['ROLE_ADMIN'])
            ->setGasAverageConsumption($faker->randomFloat(2, 0, 2750))
            ->setElectricityAverageConsumption($faker->randomFloat(2, 0, 2783))
            ->setWaterAverageConsumption($faker->randomFloat(2, 0, 3.3))
            ->setWasteAverageConsumption($faker->randomFloat(0, 0, 93.5))
            ->setRegistrationDate($faker->dateTime)
            ->setNavigoNumber($faker->unique()->randomNumber(8))
            ->setLevel($faker->randomElement($array = $dbLevel))
        ;
        $manager->persist($aymeric);
        $dbUser[] = $user;
        
        $date = new Date();
        $date->setDate(new DateTime());
        $manager->persist($date);
        $manager->flush();


        foreach($dbUser as $user) {
            $mesure = new Mesure();
            $mesure
                ->setWater($faker->randomFloat(2, 0, 3.3))
                ->setElectricity($faker->randomFloat(2, 0, 2783))
                ->setGas($faker->randomFloat(2, 0, 2750))
                ->setWaste($faker->randomFloat(0, 0, 93.5))
                ->setNavigoSubscription($user->getNavigoNumber() ? $faker->boolean() : false)
                ->setToMesure($user)
                ->setDate($date)
            ;
            $mesures[] = $mesure;
            $manager->persist($mesure);
        }

        foreach($dbUser as $user) {

            foreach ($taskNameArr as $taskName) {
                foreach ($mesures as $mesure) {
                    if ($mesure->getToMesure()->getId() === $user->getId()) {
                        $userMesure = $mesure;
                    }
                }

                $taskValue = $this->getTaskValue($taskName,$userMesure);

                $task = new Task();
                $task
                    ->setDate($date)
                    ->setName($taskName)
                    ->setUnit($taskValue["unit"])
                    ->setValidate(
                        $taskValue["type"] === "Transports"
                            ? $taskValue["user_average"]
                            : $taskValue["user_average"] >= $taskValue["mesure"]
                    )
                    ->addUser($userMesure->getToMesure());
                ;
                $manager->persist($task);
            }
        }

        $manager->flush();

    }
}
