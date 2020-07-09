<?php

namespace App\Command;

use App\Entity\Date;
use App\Entity\Mesure;
use App\Entity\User;
use App\Repository\LevelRepository;
use App\Repository\UserRepository;
use App\Service\UserHelper;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewMonthCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:newMonth';

    private $em;
    private $userHelper;
    private $userRepo;
    private $levelRepo;

    public function __construct(EntityManagerInterface $em, UserHelper $userHelper, UserRepository $userRepo, LevelRepository $levelRepo)
    {
        parent::__construct();
        $this->em = $em;
        $this->userHelper = $userHelper;
        $this->userRepo = $userRepo;
        $this->levelRepo = $levelRepo;
    }

    protected function configure()
    {
        // ...
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userRepo = $this->em->getRepository(User::class);
        $dateRepo = $this->em->getRepository(Date::class);
        $mesureRepo = $this->em->getRepository(Mesure::class);

        $currentDay = new DateTime();
        $mostRecentDateInDb = $dateRepo->findBy(
            array(),
            array('date' => 'DESC'),
            1
        );

        if ( $currentDay->format('m') > $mostRecentDateInDb[0]->getDate()->format('m') || $currentDay->format('Y') > $mostRecentDateInDb[0]->getDate()->format('Y') ) {
            $newDate = new Date();
            $newDate->setDate($currentDay);
            $this->em->persist($newDate);

            $users = $userRepo->findAll();
            foreach ($users as $user) {

                $levels = $this->levelRepo->findAll();
                $this->userRepo->newUserLevel($user, $levels);

                $userMesures = $mesureRepo->findBy(array("date" => $mostRecentDateInDb[0], "toMesure" => $user));

                $user->setSavingElectricity(
                    $user->getElectricityAverageConsumption() > $userMesures[0]->getElectricity() ?
                    $user->getSavingElectricity() + ($user->getElectricityAverageConsumption() - $userMesures[0]->getElectricity())
                        : $user->getSavingElectricity()
                );

                $user->setSavingGas(
                    $user->getGasAverageConsumption() > $userMesures[0]->getGas() ?
                    $user->getSavingGas() + ($user->getGasAverageConsumption() - $userMesures[0]->getGas())
                        : $user->getSavingGas()
                );

                $user->setSavingWaste(
                    $user->getWasteAverageConsumption() > $userMesures[0]->getWaste() ?
                    $user->getSavingWaste() + ($user->getWasteAverageConsumption()- $userMesures[0]->getWaste())
                        : $user->getSavingWaste()
                );

                $user->setSavingWater(
                    $user->getWaterAverageConsumption() > $userMesures[0]->getWater() ?
                    $user->getSavingWater() + ($user->getWaterAverageConsumption() - $userMesures[0]->getWater())
                        : $user->getSavingWater()
                );

                $user->setSavingTransport(
                    $user->getSavingTransport() + $userMesures[0]->getNavigoSubscription()
                );

                $this->userHelper->createUserTask($user, $newDate, $this->em);
            }
            $this->em->flush();
            return 1;
        }
        exit;
    }
}