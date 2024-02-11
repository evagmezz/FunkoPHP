<?php

namespace services;

use Cassandra\Table;
use models\Category;
use models\Funko;
use PDO;
use Ramsey\Uuid\Uuid;

require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Funko.php';
class CategoryService
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAllCategories()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categories ORDER BY name ASC");
        $stmt->execute();

        $categories = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $category = new Category(
                $row['id'],
                $row['name'],
                $row['created_at'],
                $row['updated_at'],
                $row['is_deleted']
            );
            $categories[] = $category;
        }
        return $categories;
    }

    public function findAll()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE is_deleted = false");
        $stmt->execute();

        $categories = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $category = new Category(
                $row['id'],
                $row['name'],
                $row['created_at'],
                $row['updated_at'],
                $row['is_deleted']
            );
            $categories[] = $category;
        }
        return $categories;
    }

    public function findByName($name)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE name = :name");
        $stmt->execute(['name' => $name]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return false;
        }
        return new Category(
            $row['id'],
            $row['name'],
            $row['created_at'],
            $row['updated_at'],
            $row['is_deleted']
        );
    }

    public function findById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return false;
        }
        return new Category(
            $row['id'],
            $row['name'],
            $row['created_at'],
            $row['updated_at'],
            $row['is_deleted']
        );

    }

    public function save(Category $category)
    {
        $sql = "INSERT INTO categories (id, name, created_at, updated_at, is_deleted)
            VALUES (:id, :name, :created_at, :updated_at, false)";

        $stmt = $this->pdo->prepare($sql);

        $category->id = Uuid::uuid4()->toString();
        $stmt->bindValue(':id', $category->id);
        $stmt->bindValue(':name', $category->name, PDO::PARAM_STR);
        $category->createdAt = date('Y-m-d H:i:s');
        $stmt->bindValue(':created_at', $category->created_at, PDO::PARAM_STR);
        $category->updatedAt = date('Y-m-d H:i:s');
        $stmt->bindValue(':updated_at', $category->updated_at, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function update(Category $category) {
        $sql = "UPDATE categories SET 
        name = :name,
        updated_at = :updated_at
        WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':id', $category->id, PDO::PARAM_STR);
        $stmt->bindValue(':name', $category->name, PDO::PARAM_STR);
        $category->updatedAt = date('Y-m-d H:i:s');
        $stmt->bindValue(':updated_at', $category->updatedAt, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function getFunkosByCategoryId($categoryId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM funkos WHERE category_id = :category_id");
        $stmt->bindParam(':category_id', $categoryId);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $funkos = [];
        foreach ($rows as $row) {
            $funko = new Funko(
                $row['id'],
                $row['name'],
                $row['category_id'],
                $row['created_at'],
                $row['updated_at'],
                $row['is_deleted']
            );
            $funkos[] = $funko;
        }

        return $funkos;
    }

    public function setDeleted($id, $deleted)
    {
        $stmt = $this->pdo->prepare("UPDATE categories SET is_deleted = :deleted WHERE id = :id");
        if ($deleted && $this->isCategoryUsed($id)) {
            return false;
        }
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':deleted', $deleted, PDO::PARAM_BOOL);
        $stmt->execute();
    }

    public function isCategoryUsed($categoryId)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM funkos WHERE category_id = :category_id");
        $stmt->bindParam(':category_id', $categoryId);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        return $count > 0;
    }

}