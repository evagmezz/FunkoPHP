<?php

namespace services;

use models\User;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../public/models/User.php';
require_once __DIR__ . '/../../public/services/UserService.php';

class UserServiceTest extends TestCase
{

    private $pdo;
    private $userService;
    private $stmt;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(\PDO::class);
        $this->userService = new UserService($this->pdo);
        $this->stmt = $this->createMock(\PDOStatement::class);
    }

    public function testFindUserByUsername()
    {
        $expectedUser = new User(
            '1',
            'user',
            'password',
            'User',
            'user@example.com',
            '2022-01-01 00:00:00',
            '2022-01-01 00:00:00',
            ['role1', 'role2']
        );

        $this->pdo->method('prepare')->willReturn($this->stmt);
        $this->stmt->method('bindParam')->willReturn([
            ':username' => 'user',
        ]);
        $this->stmt->method('execute')->willReturn(true);
        $this->stmt->method('fetch')->willReturn([
            'id' => '1',
            'username' => 'user',
            'password' => 'password',
            'name' => 'User',
            'email' => 'user@example.com',
            'created_at' => '2022-01-01 00:00:00',
            'updated_at' => '2022-01-01 00:00:00',
        ]);

        $this->pdo->method('prepare')->willReturn($this->stmt);
        $this->stmt->method('bindParam')->willReturn([
            ':user_id' => '1',
        ]);
        $this->stmt->method('execute')->willReturn(true);
        $this->stmt->method('fetchAll')->willReturn(['role1', 'role2']);

        $actualUser = $this->userService->findUserByUsername('testuser');

        $this->assertEquals($expectedUser, $actualUser);
    }

    public function testFindUserByUsernameNotFound()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not found');

        $this->pdo->method('prepare')->willReturn($this->stmt);
        $this->stmt->method('bindParam')->willReturn([
            ':username' => 'user',
        ]);
        $this->stmt->method('execute')->willReturn(true);
        $this->stmt->method('fetch')->willReturn(false);

        $this->userService->findUserByUsername('testuser');
    }

    public function testSave()
    {
        $user = new User(
            'id',
            'username',
            'password',
            'name',
            'email',
            'created_at',
            'updated_at',
            ['role1', 'role2']
        );

        $this->pdo->method('prepare')->willReturn($this->stmt);
        $this->stmt->method('bindParam')->willReturn([
            ':id' => 'id',
            ':username' => 'username',
            ':password' => 'password',
            ':name' => 'name',
            ':email' => 'email',
            ':created_at' => 'created_at',
            ':updated_at' => 'updated_at',
        ]);
        $this->stmt->method('execute')->willReturn(true);
        $this->assertTrue($this->userService->save($user));
    }

}
