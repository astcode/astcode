<?php

namespace App\Models;

use App\Core\DB\DbModel;

class Permission extends DbModel
{
    public ?int $id = null;
    public string $name = '';
    public string $description = '';
    public ?string $created_at = null;

    public function tableName(): string
    {
        return 'permissions';
    }

    public function attributes(): array
    {
        return ['name', 'description'];
    }

    public static function primaryKey(): string
    {
        return 'id';
    }

    public function rules(): array
    {
        return [
            'name' => [self::RULE_REQUIRED],
            'description' => [self::RULE_REQUIRED]
        ];
    }

    public static function getAllGrouped(): array
    {
        $permissions = self::findAll();
        $grouped = [];
        
        foreach ($permissions as $permission) {
            $group = explode('.', $permission->name)[0];
            $grouped[$group][] = $permission;
        }
        
        return $grouped;
    }

    public function save()
    {
        try {
            error_log("=== Permission Save Debug ===");
            error_log("Permission ID: " . ($this->id ?? 'null'));
            error_log("Permission Name: " . $this->name);
            error_log("Permission Description: " . $this->description);

            // If this is an update
            if ($this->id) {
                $tableName = $this->tableName();
                $attributes = $this->attributes();
                $params = array_map(fn($attr) => "$attr = :$attr", $attributes);
                $sql = "UPDATE $tableName SET " . implode(",", $params) . " WHERE id = :id";
                error_log("Update SQL: " . $sql);
                
                $statement = self::prepare($sql);
                foreach ($attributes as $attribute) {
                    $statement->bindValue(":$attribute", $this->{$attribute});
                    error_log("Binding $attribute = " . $this->{$attribute});
                }
                $statement->bindValue(":id", $this->id);
                
                $result = $statement->execute();
                error_log("Update result: " . ($result ? 'true' : 'false'));
                return $result;
            }
            
            // For new permissions
            return parent::save();
        } catch (\Exception $e) {
            error_log("Error saving permission: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public static function findAll(): array
    {
        // Simple direct query to get all permissions
        $sql = "SELECT * FROM permissions ORDER BY name";
        $stmt = self::prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_CLASS, static::class);
    }

    public static function findOne($where)
    {
        try {
            $tableName = (new static)->tableName();
            $attributes = array_keys($where);
            $sql = implode(" AND ", array_map(fn($attr) => "$attr = :$attr", $attributes));
            
            error_log("=== Permission findOne Debug ===");
            error_log("Looking for permission with conditions: " . json_encode($where));
            error_log("SQL Query: SELECT * FROM $tableName WHERE $sql");
            
            $statement = self::prepare("SELECT * FROM $tableName WHERE $sql");
            foreach ($where as $key => $item) {
                $statement->bindValue(":$key", $item);
                error_log("Binding $key = $item");
            }
            
            $statement->execute();
            $result = $statement->fetchObject(static::class);
            
            error_log("Query result: " . ($result ? json_encode([
                'id' => $result->id,
                'name' => $result->name,
                'description' => $result->description
            ]) : 'null'));
            
            return $result;
        } catch (\Exception $e) {
            error_log("Error in Permission::findOne: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }
}
