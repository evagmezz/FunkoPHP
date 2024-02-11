<?php

namespace services;

use models\Category;
use models\Funko;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../public/models/Category.php';
require_once __DIR__ . '/../../public/services/CategoryService.php';

class CategoryServiceTest extends TestCase
{
    private $pdo;
    private $categoryService;
    private $stmt;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(\PDO::class);
        $this->categoryService = new CategoryService($this->pdo);
        $this->stmt = $this->createMock(\PDOStatement::class);
    }

    public function testFindAllCategories()
    {
        $expected = [
            new Category('1', 'Marvel', '2021-01-01', '2021-01-01', false),
            new Category('2', 'DC', '2021-01-01', '2021-01-01', false),
        ];
        $this->pdo->method('prepare')
            ->willReturn($this->stmt);
        $this->stmt->method('fetch')
            ->will($this->onConsecutiveCalls(
                ['id' => '1', 'name' => 'Marvel', 'created_at' => '2021-01-01', 'updated_at' => '2021-01-01', 'is_deleted' => false],
                ['id' => '2', 'name' => 'DC', 'created_at' => '2021-01-01', 'updated_at' => '2021-01-01', 'is_deleted' => false],
                false
            ));
       $this->assertEquals($expected, $this->categoryService->findAllCategories());
    }
    public function testFindAll()
    {
        $expected = [
            new Category('1', 'Marvel', '2021-01-01', '2021-01-01', false),
            new Category('2', 'DC', '2021-01-01', '2021-01-01', false),
        ];
        $this->pdo->method('prepare')
            ->willReturn($this->stmt);
        $this->stmt->method('fetch')
            ->will($this->onConsecutiveCalls(
                ['id' => '1', 'name' => 'Marvel', 'created_at' => '2021-01-01', 'updated_at' => '2021-01-01', 'is_deleted' => false],
                ['id' => '2', 'name' => 'DC', 'created_at' => '2021-01-01', 'updated_at' => '2021-01-01', 'is_deleted' => false],
                false
            ));
       $this->assertEquals($expected, $this->categoryService->findAll());
    }

    public function testFindByName()
    {
        $expected = new Category('1', 'Marvel', '2021-01-01', '2021-01-01', false);
        $this->pdo->method('prepare')
            ->willReturn($this->stmt);
        $this->stmt->method('fetch')
            ->willReturn(['id' => '1', 'name' => 'Marvel', 'created_at' => '2021-01-01', 'updated_at' => '2021-01-01', 'is_deleted' => false]);
       $this->assertEquals($expected, $this->categoryService->findByName('Marvel'));
    }

    public function testFindByNameNotFound()
    {
        $this->pdo->method('prepare')
            ->willReturn($this->stmt);
        $this->stmt->method('fetch')
            ->willReturn(false);
       $this->assertFalse($this->categoryService->findByName('Marvel'));
    }

    public function testFindById()
    {
        $expected = new Category('1', 'Marvel', '2021-01-01', '2021-01-01', false);
        $this->pdo->method('prepare')
            ->willReturn($this->stmt);
        $this->stmt->method('fetch')
            ->willReturn(['id' => '1', 'name' => 'Marvel', 'created_at' => '2021-01-01', 'updated_at' => '2021-01-01', 'is_deleted' => false]);
       $this->assertEquals($expected, $this->categoryService->findById('1'));
    }

    public function testFindByIdNotFound()
    {
        $this->pdo->method('prepare')
            ->willReturn($this->stmt);
        $this->stmt->method('fetch')
            ->willReturn(false);
       $this->assertFalse($this->categoryService->findById('1'));
    }

    public function testSave()
    {
        $category = new Category('1', 'Marvel', '2021-01-01', '2021-01-01', false);
        $this->pdo->method('prepare')
            ->willReturn($this->stmt);
        $this->stmt->method('execute')
            ->willReturn(true);
       $this->assertTrue($this->categoryService->save($category));
    }

    public function testUpdate()
    {
        $category = new Category('1', 'Marvel', '2021-01-01', '2021-01-01', false);
        $this->pdo->method('prepare')
            ->willReturn($this->stmt);
        $this->stmt->method('execute')
            ->willReturn(true);
       $this->assertTrue($this->categoryService->update($category));
    }

    public function testSetDeleted()
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);

        $pdo = $this->createMock(\PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $categoryService = $this->getMockBuilder(CategoryService::class)
            ->setConstructorArgs([$pdo])
            ->onlyMethods(['isCategoryUsed'])
            ->getMock();

        $categoryService->method('isCategoryUsed')->willReturn(true);
        $this->assertFalse($categoryService->setDeleted('1', true));

        $categoryService->method('isCategoryUsed')->willReturn(false);
        $categoryService->setDeleted('1', true);
        $this->assertTrue(true);

        $categoryService->method('isCategoryUsed')->willReturn(false);
        $categoryService->setDeleted('1', false);
        $this->assertTrue(true);
    }


    public function testIsCategoryUsed()
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('fetchColumn')->willReturn(1);

        $pdo = $this->createMock(\PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $categoryService = new CategoryService($pdo);
        $this->assertTrue($categoryService->isCategoryUsed('1'));
    }

}
