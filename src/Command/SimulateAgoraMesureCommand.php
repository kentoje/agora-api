<?php

namespace App\Command;

use App\Repository\MesureRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Service\UserHelper;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SimulateAgoraMesureCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:simulateMesure';

    private $em;
    private $container;
    private $mesureRepo;
    private $userRepo;
    private $userHelper;
    private $taskRepo;


    public function __construct(EntityManagerInterface $em, ContainerInterface $container, MesureRepository $mesureRepo, UserRepository $userRepo, TaskRepository $taskRepo, UserHelper $userHelper)
    {
        parent::__construct();
        $this->em = $em;
        $this->container = $container;
        $this->mesureRepo = $mesureRepo;
        $this->userRepo = $userRepo;
        $this->userHelper = $userHelper;
        $this->taskRepo = $taskRepo;

    }

    protected function configure()
    {
        // ...
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $conn = $this->container->get('doctrine')->getConnection();
        $currentDate = new DateTime();

        $sqlQueries = "select mesure.*, 
                              date.date, 
                              user.waste_average_consumption, 
                              user.electricity_average_consumption, 
                              user.water_average_consumption, 
                              user.gas_average_consumption,
                              user.id as user_id 
                        from user 
                        inner join mesure 
                            on user.id = mesure.to_mesure_id 
                        inner join date 
                            on mesure.date_id = date.id  
                        inner join task 
                            on date.id = task.date_id 
                        where MONTH(date.date) = MONTH(:date)
                        AND YEAR(date.date) = YEAR(:date)
                        group by mesure.id
        ";

        $stmt = $conn->prepare($sqlQueries);
        $stmt->execute(array(':date' => $currentDate->format('Y-m-d')));
        $mesures = $stmt->fetchAll();

        $toHighAverage = 1.10;
        $toLowAverage = 0.90;
        $multiplied = 10000;

        foreach ($mesures as $mesure) {

            $highAverageWaste = $this->userHelper->getAverage($mesure, 'waste_average_consumption', $toHighAverage);
            $highAverageElectricity = $this->userHelper->getAverage($mesure, 'electricity_average_consumption', $toHighAverage);
            $highAverageWater = $this->userHelper->getAverage($mesure, 'water_average_consumption', $toHighAverage);
            $highAverageGas = $this->userHelper->getAverage($mesure, 'gas_average_consumption', $toHighAverage);

            $lowAverageWaste = $this->userHelper->getAverage($mesure, 'waste_average_consumption', $toLowAverage);
            $lowAverageElectricity = $this->userHelper->getAverage($mesure, 'electricity_average_consumption', $toLowAverage);
            $lowAverageWater = $this->userHelper->getAverage($mesure, 'water_average_consumption', $toLowAverage);
            $lowAverageGas = $this->userHelper->getAverage($mesure, 'gas_average_consumption', $toLowAverage);

            $mesureObject = $this->mesureRepo->findOneBy(['id' => $mesure['id']]);
            $mesureObject and $mesureObject->setWaste($mesureObject->getWaste() + random_int($lowAverageWaste * $multiplied, $highAverageWaste * $multiplied) / $multiplied);
            $mesureObject and $mesureObject->setElectricity($mesureObject->getElectricity() + random_int($lowAverageElectricity * $multiplied, $highAverageElectricity * $multiplied) / $multiplied);
            $mesureObject and $mesureObject->setWater($mesureObject->getWater() + random_int($lowAverageWater * $multiplied, $highAverageWater * $multiplied) / $multiplied);
            $mesureObject and $mesureObject->setGas($mesureObject->getGas() + random_int($lowAverageGas * $multiplied, $highAverageGas * $multiplied) / $multiplied);
            $this->em->persist($mesureObject);

            $user = $this->userRepo->findOneBy(['id' => $mesure['user_id']]);
            $user && $tasks = $this->taskRepo->findBy(['user' => $user->getId()]);
            $currentDate = DateTime::createFromFormat('Y-m-d', $mesure['date']);

            foreach ($tasks as $task) {

                if ($currentDate->format('m') === $task->getDate()->getDate()->format('m') and $currentDate->format('Y') === $task->getDate()->getDate()->format('Y')) {

                    if ($task->getName() === 'Electricité' and $mesureObject->getElectricity() >= $user->getElectricityAverageConsumption()) {
                        $task->setValidate(0);
                        $this->em->persist($task);
                    } else if ($task->getName() === 'Eau' and $mesureObject->getWater() >= $user->getWaterAverageConsumption()) {
                        $task->setValidate(0);
                        $this->em->persist($task);
                    } else if ($task->getName() === 'Déchets' and $mesureObject->getWaste() >= $user->getWasteAverageConsumption()) {
                        $task->setValidate(0);
                        $this->em->persist($task);
                    } else if ($task->getName() === 'Gaz' and $mesureObject->getGas() >= $user->getGasAverageConsumption()) {
                        $task->setValidate(0);
                        $this->em->persist($task);
                    }
                }
            }
        }

        $this->em->flush();
        return 1;
    }
}