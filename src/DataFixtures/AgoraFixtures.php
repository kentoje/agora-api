<?php

namespace App\DataFixtures;

use App\Entity\Level;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AgoraFixtures extends Fixture
{
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
                ->setPassword(password_hash($faker->password, PASSWORD_ARGON2ID))
                ->setAgoraNumber($faker->unique()->randomNumber(8))
                ->setNbResident($faker->randomElement($array = [1, 2, 3, 4, 5, 6]))
                ->setLivingArea($faker->numberBetween($min = 15, $max = 300))
                ->setGas($faker->boolean)
                ->setInsulation($faker->boolean)
                ->setSocialSecurityNumber($faker->regexify('[1-2]{1} [0-9]{2} (0[1-9]|1[0-2]) [0-9]{2} [0-9]{3} [0-9]{3} [0-9]{2}'))
                ->setRoles($faker->randomElement($array = [['ROLE_USER'], ['ROLE_ADMIN']]))
                ->setGasAverageConsumption($faker->randomFloat(2, 0, 2750))
                ->setElectricityAverageConsumption($faker->randomFloat(2, 0, 2783))
                ->setWaterAverageConsumption($faker->randomFloat(2, 0, 3.3))
                ->setWasteAverageConsumption($faker->randomFloat(0, 0, 93.5))
                ->setRegistrationDate($faker->dateTime)
                ->setNavigoNumber($faker->unique()->randomNumber(8))
                ->setLevel($faker->randomElement($array = $dbLevel))
            ;
            $manager->persist($user);
        }

        $manager->flush();
    }
}
