<?php

namespace models;

class User
{
    public $id;
    public $username;
    public $password;
    public $name;
    public $email;
    public $created_at;
    public $updated_at;
    public $roles = [];

    public function __construct($id, $username, $password, $name, $email, $created_at, $updated_at, $roles)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->name = $name;
        $this->email = $email;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
        $this->roles = $roles;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }
}
