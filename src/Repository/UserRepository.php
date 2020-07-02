<?php

namespace App\Repository;

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
