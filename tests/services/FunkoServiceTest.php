<?php

namespace services;

use models\Funko;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../public/models/Category.php';
require_once __DIR__ . '/../../public/services/CategoryService.php';
require_once __DIR__ . '/../../public/models/Funko.php';
require_once __DIR__ . '/../../public/services/FunkoService.php';

class FunkoServiceTest extends TestCase
{
    private $pdo;
    private $funkoService;
    private $stmt;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(\PDO::class);
        $this->funkoService = new FunkoService($this->pdo);
        $this->stmt = $this->createMock(\PDOStatement::class);
    }

    public function testFindAllWithCategoryName()
    {
        $expected = [
            new Funko('1', 'Spiderman', 'spiderman.jpg', '20.00', '10', '2021-01-01', '2021-01-01', '1', 'Marvel'),
            new Funko('2', 'Batman', 'batman.jpg', '20.00', '10', '2021-01-01', '2021-01-01', '2', 'DC'),
        ];
        $this->pdo->method('prepare')
            ->willReturn($this->stmt);
        $this->stmt->method('fetch')
            ->will($this->onConsecutiveCalls(
                ['id' => '1', 'name' => 'Spiderman', 'image' => 'spiderman.jpg', 'price' => '20.00', 'stock' => '10', 'created_at' => '2021-01-01', 'updated_at' => '2021-01-01', 'category_id' => '1', 'category_name' => 'Marvel'],
                ['id' => '2', 'name' => 'Batman', 'image' => 'batman.jpg', 'price' => '20.00', 'stock' => '10', 'created_at' => '2021-01-01', 'updated_at' => '2021-01-01', 'category_id' => '2', 'category_name' => 'DC'],
                false
            ));
        $this->assertEquals($expected, $this->funkoService->findAllWithCategoryName(''));
    }

    public function testFindById()
    {

        $expectedFunko = new Funko(1, 'Funko 1', 'image.jpg', 100, 10,
            '2021-01-01', '2021-01-01', 1, 'Category 1', 1);

        $this->pdo->method('prepare')
            ->willReturn($this->stmt);

        $this->stmt->method('fetch')
            ->willReturn([
                'id' => 1,
                'name' => 'Funko 1',
                'image' => 'image.jpg',
                'price' => 100,
                'stock' => 10,
                'created_at' => '2021-01-01',
                'updated_at' => '2021-01-01',
                'category_id' => 1,
                'category_name' => 'Category 1',
                'is_deleted' => '1'
            ]);

        $this->assertEquals($expectedFunko, $this->funkoService->findById(1));
    }

    public function testFindByIdNotFound()
    {
        $this->pdo->method('prepare')
            ->willReturn($this->stmt);
        $this->stmt->method('fetch')
            ->willReturn(false);
        $this->assertNull($this->funkoService->findById('1'));
    }

    public function testUpdate()
    {
        $funko = new Funko('1', 'Spiderman', 'spiderman.jpg', '20.00', '10', '2021-01-01', '2021-01-01', '1', 'Marvel');
        $this->pdo->method('prepare')
            ->willReturn($this->stmt);
        $this->stmt->method('execute')
            ->willReturn(true);
        $this->assertTrue($this->funkoService->update($funko));
    }

    public function testSave()
    {
        $funko = new Funko('1', 'Spiderman', 'spiderman.jpg', '20.00', '10', '2021-01-01', '2021-01-01', '1', 'Marvel');
        $this->pdo->method('prepare')
            ->willReturn($this->stmt);
        $this->stmt->method('execute')
            ->willReturn(true);
        $this->assertTrue($this->funkoService->save($funko));
    }

    public function testDeleteById()
    {
        $this->pdo->method('prepare')
            ->willReturn($this->stmt);
        $this->stmt->method('execute')
            ->willReturn(true);
        $this->assertTrue($this->funkoService->deleteById('1'));
    }

    public function testDeleteByIdNotFound()
    {
        $this->pdo->method('prepare')
            ->willReturn($this->stmt);
        $this->stmt->method('execute')
            ->willReturn(false);
        $this->assertFalse($this->funkoService->deleteById('1'));
    }

}
