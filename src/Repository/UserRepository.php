<?php

namespace App\Repository;

use App\Entity\Level;
use App\Entity\User;
use App\Service\QueryHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     * @param UserInterface $user
     * @param string $newEncodedPassword
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function findOneUser(int $id): array
    {
        return $this->createQueryBuilder('user')
            ->where('user.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    public function getUserDatas(int $id): array
    {
        $response = array();

        $conn = $this->getEntityManager()->getConnection();

        $sqlQueries = [
            'data' => QueryHelper::getQueryUserAdditionalData($id),
            'tasks'=> QueryHelper::getQueryUserCurrentTasks($id)
        ];

        foreach ($sqlQueries as $key => $query) {
            try {
                $stmt = $conn->prepare($query);
                $stmt->execute(['userId' => $id]);
                $result = $stmt->fetchAll();
                $response[$key] = $key === "data" ? $result[0] : $result;
            } catch (\Throwable $e) {
                echo $e->getMessage();
            }
        }


        return $response;
    }

    public function getAllUserTasks(int $id, int $year): array
    {
        $response = array();

        $conn = $this->getEntityManager()->getConnection();

        $sql = QueryHelper::getQueryAllUserTasks($id, $year);

        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute(['userId' => $id]);
            $result = $stmt->fetchAll();
            $response = $result;
        } catch (\Throwable $e) {
            echo $e->getMessage();
        }

        return $response;
    }

    public function newUserLevel(User $user, array $levels): void
    {
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

        $conn = $this->getEntityManager()->getConnection();

        $sql = "select count(task.id) as nbTask 
                from task 
                inner join date 
                on task.date_id = date.id 
                where task.user_id = :id 
                and task.validate = 1 
                and date.date >= DATE_FORMAT(NOW() ,'%Y-01-01') 
                and date.date < DATE_FORMAT(NOW() ,'%Y-%m-01')";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute(['id' => $user->getId()]);
            $result = $stmt->fetchAll();
            $countValidateTask = $result[0]["nbTask"];
        } catch (\Throwable $e) {
            echo $e->getMessage();
        }

        if ($countValidateTask < 5) {
            $user->setLevel($levels[0]);
        } elseif ($countValidateTask >= 5 && $countValidateTask < 10) {
            $user->setLevel($levels[1]);
        } elseif ($countValidateTask >= 10 && $countValidateTask < 15) {
            $user->setLevel($levels[2]);
        } elseif ($countValidateTask >= 15 && $countValidateTask < 20) {
            $user->setLevel($levels[3]);
        } elseif ($countValidateTask >= 20 && $countValidateTask < 25) {
            $user->setLevel($levels[4]);
        } elseif ($countValidateTask >= 25 && $countValidateTask < 30) {
            $user->setLevel($levels[5]);
        } elseif ($countValidateTask >= 30 && $countValidateTask < 35) {
            $user->setLevel($levels[6]);
        } elseif ($countValidateTask >= 35 && $countValidateTask < 40) {
            $user->setLevel($levels[7]);
        } elseif ($countValidateTask >= 40 && $countValidateTask < 45) {
            $user->setLevel($levels[8]);
        } elseif ($countValidateTask >= 45 && $countValidateTask < 50) {
            $user->setLevel($levels[9]);
        } elseif ($countValidateTask >= 50 && $countValidateTask < 55) {
            $user->setLevel($levels[10]);
        } elseif ($countValidateTask >= 55 && $countValidateTask < 60) {
            $user->setLevel($levels[11]);
        } else {
            $user->setLevel($levels[12]);
        }
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function getAllDataAnalytics(): array
    {
        $response = array();

        $conn = $this->getEntityManager()->getConnection();

        $sqlQueries = [
            'allYears' => QueryHelper::getAllUserAndValidateTask(),
            'thisYear'=> QueryHelper::getAllStatForAlltaskType()
        ];

        foreach ($sqlQueries as $key => $query) {
            try {
                $stmt = $conn->prepare($query);
                $stmt->execute();
                $result = $stmt->fetchAll();
                $response[$key] = $key === "data" ? $result[0] : $result;
            } catch (\Throwable $e) {
                echo $e->getMessage();
            }
        }


        return $response;
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
