<?php

namespace services;

use models\Funko;
use PDO;


require_once __DIR__ . '/../models/Funko.php';

class FunkoService
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAllWithCategoryName($searchTerm)
    {
        $sql = "SELECT f.*, c.name AS category_name
            FROM funkos f
            LEFT JOIN categories c ON f.category_id = c.id";

        if (is_string($searchTerm) && $searchTerm !== '') {
            $sql .= " WHERE LOWER(f.name) LIKE :searchTerm OR LOWER(c.name) LIKE :searchTerm";
            $stmt = $this->pdo->prepare($sql);
            $searchTerm = '%' . strtolower($searchTerm) . '%';
            $stmt->bindValue(':searchTerm', $searchTerm, PDO::PARAM_STR);
        } else {
            $stmt = $this->pdo->prepare($sql);
        }

        $stmt->execute();

        $funkos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $funkos[] = new Funko(
                $row['id'],
                $row['name'],
                $row['image'],
                $row['price'],
                $row['stock'],
                $row['created_at'],
                $row['updated_at'],
                $row['category_id'],
                $row['category_name']
            );
        }
        return $funkos;
    }

    public function findById($id)
    {
        $sql = "SELECT f.*, c.name AS category_name
            FROM funkos f
            LEFT JOIN categories c ON f.category_id = c.id
            WHERE f.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        $funko = new Funko(
            $row['id'],
            $row['name'],
            $row['image'],
            $row['price'],
            $row['stock'],
            $row['created_at'],
            $row['updated_at'],
            $row['category_id'],
            $row['category_name'],
            $row['is_deleted']
        );

        return $funko;
    }

    public function update(Funko $funko)
    {
        if ($funko->image !== Funko::$IMAGEN_DEFAULT) {
            $sql = "UPDATE funkos SET 
              name = :name,
              image = :image,
              stock = :stock, 
              price = :price,
              updated_at = :updated_at,
              category_id = :category_id
              WHERE id = :id";
        } else {
            $sql = "UPDATE funkos SET 
              name = :name,
              stock = :stock, 
              price = :price,
              updated_at = :updated_at,
              category_id = :category_id
              WHERE id = :id";
        }

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':id', $funko->id, PDO::PARAM_INT);
        $stmt->bindValue(':name', $funko->name, PDO::PARAM_STR);
        if ($funko->image !== Funko::$IMAGEN_DEFAULT) {
            $stmt->bindValue(':image', $funko->image, PDO::PARAM_STR);
        }
        $stmt->bindValue(':price', $funko->price, PDO::PARAM_INT);
        $stmt->bindValue(':stock', $funko->stock, PDO::PARAM_INT);
        $funko->updated_at = date('Y-m-d H:i:s');
        $stmt->bindValue(':updated_at', $funko->updated_at, PDO::PARAM_STR);
        $stmt->bindValue(':category_id', $funko->category_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function save(Funko $funko)
    {
        $sql = "SELECT NEXTVAL('funkos_id_seq')";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $funkoId = $stmt->fetchColumn();

        $sql = "INSERT INTO funkos (id, name, image, price, stock, category_id, created_at, updated_at, is_deleted)
        VALUES (:id, :name, :image, :price, :stock, :category_id, :created_at, :updated_at, false)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':id', $funkoId, PDO::PARAM_INT);
        $stmt->bindValue(':name', $funko->name, PDO::PARAM_STR);
        $stmt->bindValue(':image', $funko->image, PDO::PARAM_STR);
        $stmt->bindValue(':price', $funko->price, PDO::PARAM_INT);
        $stmt->bindValue(':stock', $funko->stock, PDO::PARAM_INT);
        $funko->created_at = date('Y-m-d H:i:s');
        $stmt->bindValue(':created_at', $funko->created_at);
        $funko->updated_at = date('Y-m-d H:i:s');
        $stmt->bindValue(':updated_at', $funko->updated_at);
        $stmt->bindValue(':category_id', $funko->category_id);

        return $stmt->execute();
    }

    public function deleteById($id)
    {
        $sql = "DELETE FROM funkos WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
