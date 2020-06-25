<?php

namespace App\Command;

use App\Entity\Date;
use App\Entity\User;
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

    public function __construct(EntityManagerInterface $em, UserHelper $userHelper)
    {
        parent::__construct();
        $this->em = $em;
        $this->userHelper = $userHelper;
    }

    protected function configure()
    {
        // ...
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userRepo = $this->em->getRepository(User::class);
        $dateRepo = $this->em->getRepository(Date::class);

        $currentDay = new DateTime();
        $mostRecentDateInDb = $dateRepo->findBy(
            array(),
            array('date' => 'DESC'),
            1
        );

        if ( $currentDay->format('m') > $mostRecentDateInDb[0]->getDate()->format('m') or $currentDay->format('Y') > $mostRecentDateInDb[0]->getDate()->format('Y')) {
            $newDate = new Date();
            $newDate->setDate($currentDay);
            $this->em->persist($newDate);

            $users = $userRepo->findAll();
            foreach ($users as $user) {
                $this->userHelper->createUserTask($user, $newDate, $this->em);
            }
            $this->em->flush();
            return 1;
        }
        exit;
    }
}