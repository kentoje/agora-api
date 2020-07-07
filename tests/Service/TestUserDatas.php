<?php

namespace App\Tests\Service;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class TestUserDatas
{
    public static function testUserDatas(KernelBrowser $kernelBrowser, string $route, int $userIdModifier = 0): void
    {
        $data = json_decode($kernelBrowser->getResponse()->getContent(), true);
        $userId = $data['user']['id'];

        $kernelBrowser->request('GET', sprintf($route, $userId + ($userIdModifier)));
    }
}
