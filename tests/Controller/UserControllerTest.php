<?php

namespace App\Tests\Controller;

use App\Entity\Mesure;
use App\Entity\Task;
use App\Entity\User;
use App\Tests\Service\TestUserDatas;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{

    /**
     * Create a client with a default Authorization header.
     *
     * @param string $username
     * @param string $password
     *
     * @return KernelBrowser
     */
    protected function createAuthenticatedClient(string $username = 'aymeric.mayeux@hetic.net', string $password = 'azerty'): KernelBrowser
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => $username,
                'password' => $password,
            ])
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['tokens']['token']));

        return $client;
    }

    public function testUserWithBadCredentials(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => 'hehe',
                'password' => 'hoho',
            ])
        );

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testSignUpWithRequiredInformations(): void
    {
        $kernel = self::bootKernel();

        $userRepository = $kernel->getContainer()
            ->get('doctrine')
            ->getRepository(User::class)
        ;
        $user = $userRepository->findOneBy(['email' => 'john.doe2@doe.com']);

        if ($user) {
            $mesureRepository = $kernel->getContainer()
                ->get('doctrine')
                ->getRepository(Mesure::class)
            ;
            $mesure = $mesureRepository->findOneBy(['toMesure' => $user->getId()]);

            $taskRepository = $kernel->getContainer()
                ->get('doctrine')
                ->getRepository(Task::class)
            ;
            $tasks = $taskRepository->findBy(['user' => $user->getId()]);

            $em = $kernel->getContainer()->get('doctrine')->getManager();

            foreach ($tasks as $task) {
                $em->remove($task);
            }

            $em->remove($mesure);
            $em->remove($user);
            $em->flush();
        }

        /* Shutdown the previous kernel to ensure that we can create a client. */
        self::ensureKernelShutdown();

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/signup',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'firstName' => 'John',
                'lastName' => 'Doe',
                'password' => password_hash('test', 'argon2id'),
                'email' => 'john.doe2@doe.com',
                'nbResident' => 5,
                'livingArea' => 50.0,
                'gas' => true,
                'insulation' => false,
                'agoraNumber' => '12345678',
                'nifNumber' => '1123456789013',
                'navigoNumber' => '01234567',
            ])
        );

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testUsersPageIsAuth(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/api/admin/users');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testUsersPageIsRestricted(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/admin/users');

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testSingleUserPageIsAuth(): void
    {
        $client = $this->createAuthenticatedClient();
        TestUserDatas::testUserDatas($client, '/api/admin/user/%s');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testHomepageRedirection(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testUserUpdatableData(): void
    {
        $client = $this->createAuthenticatedClient();
        TestUserDatas::testUserDatas($client, '/api/user/update/%s');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testUserUpdatableDataIsNotSameId(): void
    {
        $client = $this->createAuthenticatedClient();
        TestUserDatas::testUserDatas($client, '/api/user/update/%s', -1);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testUserTasksData(): void
    {
        $client = $this->createAuthenticatedClient();
        TestUserDatas::testUserDatas($client, '/api/user/tasks/%s/2020');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testUserTasksDataIsNotSameId(): void
    {
        $client = $this->createAuthenticatedClient();
        TestUserDatas::testUserDatas($client, '/api/user/tasks/%s/2020', -1);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testUserAnalyticsData(): void
    {
        $client = $this->createAuthenticatedClient();
        TestUserDatas::testUserDatas($client, '/api/user/analytics/%s');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testUserAnalyticsDataIsNotSameId(): void
    {
        $client = $this->createAuthenticatedClient();
        TestUserDatas::testUserDatas($client, '/api/user/analytics/%s', -1);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}