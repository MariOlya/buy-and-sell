<?php

declare(strict_types=1);

namespace unit;

use Mockery;
use omarinina\domain\models\Users;
use yii\web\NotFoundHttpException;

class UserTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected \UnitTester $tester;
    
    protected function _before(): void
    {
    }

    protected function _after(): void
    {
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @throws \Exception
     */
    public function testAddVkId(): void
    {
        $user = $this->make(Users::class, [
            'name' => 'Mile',
            'lastName' => 'Doe',
            'email' => 'M.doe@google.com',
            'password' => '123456',
            'avatarSrc' => '/img/avatar01.jpg',
        ]);

        $newVkId = '09876543';

        $user->addVkId($newVkId);

        $this->assertEquals('09876543', $user->vkId);
    }

    /**
     * @throws \Exception
     */
    public function testAddVkIdException(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $user = $this->make(Users::class, [
            'name' => 'Mile',
            'lastName' => 'Doe',
            'email' => 'M.doe@google.com',
            'password' => '123456',
            'avatarSrc' => '/img/avatar01.jpg',
            'vkId' => '1245780'
        ]);

        $newVkId = '09876543';

        $user->addVkId($newVkId);
    }

    public function testUpdateUserInfo(): void
    {
    }
}