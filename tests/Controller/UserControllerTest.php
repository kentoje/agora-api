<?php

namespace App\Tests\Controller;

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
    protected function createAuthenticatedClient($username = 'aymeric.mayeux@hetic.net', $password = 'azerty'): KernelBrowser
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => $username,
                'password' => $password,
            ])
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }

    public function testUserWithBadCredentials(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => 'hehe',
                'password' => 'hoho',
            ])
        );

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    // Persist the new User in Database which trigger errors if we run the test twice...
    /*public function testSignUpWithRequiredInformations(): void
    {
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
                'agoraNumber' => 12345678,
                'nifNumber' => '1123456789013',
                'navigoNumber' => 01234567,
            ])
        );

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }*/

    public function testUsersPageIsAuth(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/api/users');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testUsersPageIsRestricted(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/users');

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}