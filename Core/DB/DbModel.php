<?php

namespace App\Core\DB;

use App\Core\Model;
use App\Core\Application;
use App\Models\User;

abstract class DbModel extends Model
{


    public function save()
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $params = array_map(fn($attr) => ":$attr", $attributes);
        $statement = self::prepare("INSERT INTO $tableName (" . implode(',', $attributes) . ") 
                VALUES (" . implode(',', $params) . ")");
        foreach ($attributes as $attribute) {
            $statement->bindValue(":$attribute", $this->{$attribute});
        }
        $statement->execute();
        return true;
    }    
    
    abstract public function tableName(): string;
    
    abstract public function attributes(): array;
    
    abstract public static function primaryKey(): string;

    public function labels(): array
    {
        return [];
    }

    public function rules(): array
    {
        return [];
    }
    
    public static function findOne($where) // $where = ['email' => 'test@test.com', 'status' => 1, firstname => 'John', ...]
    {
        $tableName = new User();
        $tableName = $tableName->tableName();
        $attributes = array_keys($where);
        // SELECT * FROM users WHERE email = :email AND status = :status AND firstname = :firstname
        $sql = implode("AND ", array_map(fn($attr) => "$attr = :$attr", $attributes));
        // SELECT * FROM users WHERE $sql
        $statement = self::prepare("SELECT * FROM $tableName WHERE $sql");
        foreach ($where as $key => $item) {
            $statement->bindValue(":$key", $item);
        }
        // dd($user);
        $statement->execute();
        return $statement->fetchObject(static::class);
    }
    
    public static function prepare($sql)
    {
        return Application::$app->db->pdo->prepare($sql);
    }
}
