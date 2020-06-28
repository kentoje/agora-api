<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class UserTest extends KernelTestCase
{
    public function getEntity(): User
    {
        return (new User())
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setPassword(password_hash('test', 'argon2id'))
            ->setEmail('john.doe@test.com')
            ->setNbResident(5)
            ->setLivingArea(50.0)
            ->setGas(true)
            ->setInsulation(false)
            ->setAgoraNumber(12345678)
            ->setNifNumber('1123456789014')
            ->setNavigoNumber(01234567)
        ;
    }

    public function assertHasErrors(User $user, int $number = 0): void
    {
        $messages = [];

        self::bootKernel();
        $errors = self::$container->get('validator')->validate($user);

        /**
         * @var ConstraintViolation $error
         */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }

        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    public function testValidEntity(): void
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    public function testInvalidEntity(): void
    {
        $user = $this->getEntity();
        $user
            ->setAgoraNumber('123456789')
            ->setNifNumber('1123456789012a')
        ;

        $this->assertHasErrors($user, 2);
    }
}