<?php

namespace App\Command;

use App\Entity\Level;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResetAllUserLevelCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:resetLevel';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure()
    {
        // ...
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $firstDayOfYear = new DateTime('first day of january');
        $currentDay = new DateTime();

        if ($firstDayOfYear->format('Y-m-d') === $currentDay->format('Y-m-d')) {
            $levelRepo = $this->em->getRepository(Level::class);
            $userRepo = $this->em->getRepository(User::class);

            $levelZero = $levelRepo->findOneBy(['levelNumber' => 0]);

            $users = $userRepo->findAll();
            foreach ($users as $user) {
                $user->setLevel($levelZero);
                $this->em->persist($user);
                $this->em->flush();
            }
            return 0;
        }
        exit;
    }
}
