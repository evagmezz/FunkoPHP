<?php

namespace models;

use Ramsey\Uuid\Uuid;

class Category
{
    private $id;
    private $name;
    private $created_at;
    private $updated_at;
    private $is_deleted;

    public function __construct($id = null, $name = null, $created_at = null, $updated_at = null, $is_deleted = null)
    {
        $this->id = $id ?? $this->generateUUID();
        $this->name = $name;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
        $this->is_deleted = $is_deleted;
    }

    public function getId()
    {
        return $this->id;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    private function generateUUID()
    {
        return Uuid::uuid4()->toString();
    }
}