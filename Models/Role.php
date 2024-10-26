<?php

namespace App\Models;

use App\Core\DB\DbModel;

class Role extends DbModel
{
    public ?int $id = null;
    public string $name = '';  // Initialize with empty string
    public string $description = '';  // Initialize with empty string
    public ?string $created_at = null;
    protected array $permissions = [];

    // Define system roles that cannot be modified
    private const SYSTEM_ROLES = ['super_admin', 'admin', 'user'];

    public function __construct()
    {
        // Initialize properties with default values
        $this->name = '';
        $this->description = '';
    }

    public function tableName(): string
    {
        return 'roles';
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

    public function save()
    {
        error_log("=== Role Save Debug ===");
        error_log("Role ID: " . ($this->id ?? 'null'));
        error_log("Role Name: " . $this->name);
        error_log("Role Description: " . $this->description);
        
        try {
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
            
            return parent::save();
        } catch (\Exception $e) {
            error_log("Error saving role: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function isSystemRole(): bool
    {
        return in_array($this->name, self::SYSTEM_ROLES);
    }

    public static function findAll(): array
    {
        $tableName = (new static)->tableName();
        $statement = self::prepare("SELECT * FROM $tableName ORDER BY name");
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_CLASS, static::class);
    }

    public function getPermissions(): array
    {
        error_log("=== Getting Permissions for Role ===");
        error_log("Role ID: " . $this->id);
        error_log("Role Name: " . $this->name);
        
        if (empty($this->permissions)) {
            $sql = "SELECT p.* FROM permissions p 
                    JOIN role_permissions rp ON p.id = rp.permission_id 
                    WHERE rp.role_id = :role_id";
            error_log("SQL Query: " . $sql);
            
            $stmt = self::prepare($sql);
            $stmt->bindValue(':role_id', $this->id);
            $stmt->execute();
            
            // Debug the actual SQL that was executed
            ob_start();
            $stmt->debugDumpParams();
            $debugSql = ob_get_clean();
            error_log("Executed SQL with params: " . $debugSql);
            
            $this->permissions = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            error_log("Found permissions: " . json_encode($this->permissions));
        } else {
            error_log("Using cached permissions: " . json_encode($this->permissions));
        }
        
        return $this->permissions;
    }

    public function hasPermission(string $permission): bool
    {
        // Super admin always has all permissions
        if ($this->name === 'super_admin') {
            error_log('Super admin role - granting all permissions');
            return true;
        }

        $permissions = $this->getPermissions();
        error_log('Checking permission: ' . $permission);
        error_log('Available permissions: ' . json_encode($permissions));
        
        $result = in_array($permission, array_column($permissions, 'name'));
        error_log('Permission check result: ' . ($result ? 'true' : 'false'));
        
        return $result;
    }

    public function addPermission(int $permissionId): bool
    {
        // Skip if super_admin (they already have all permissions)
        if ($this->name === 'super_admin') {
            return true;
        }

        $sql = "INSERT INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)";
        $stmt = self::prepare($sql);
        $stmt->bindValue(':role_id', $this->id);
        $stmt->bindValue(':permission_id', $permissionId);
        return $stmt->execute();
    }

    public function removePermission(int $permissionId): bool
    {
        // Skip if super_admin (they should keep all permissions)
        if ($this->name === 'super_admin') {
            return true;
        }

        $sql = "DELETE FROM role_permissions WHERE role_id = :role_id AND permission_id = :permission_id";
        $stmt = self::prepare($sql);
        $stmt->bindValue(':role_id', $this->id);
        $stmt->bindValue(':permission_id', $permissionId);
        return $stmt->execute();
    }

    public static function getDefaultRole(): ?Role
    {
        return self::findOne(['name' => 'user']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->name === 'super_admin';
    }

    public static function findOne($where)
    {
        try {
            $tableName = (new static)->tableName();
            $attributes = array_keys($where);
            $sql = implode(" AND ", array_map(fn($attr) => "$attr = :$attr", $attributes));
            
            error_log("=== Role findOne Debug ===");
            error_log("Looking for role with conditions: " . json_encode($where));
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
            error_log("Error in Role::findOne: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function updatePermissions(array $permissionIds): bool
    {
        // Clear existing permissions
        $sql = "DELETE FROM role_permissions WHERE role_id = :role_id";
        $stmt = self::prepare($sql);
        $stmt->bindValue(':role_id', $this->id);
        $stmt->execute();

        // Add new permissions
        foreach ($permissionIds as $permissionId) {
            $sql = "INSERT INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)";
            $stmt = self::prepare($sql);
            $stmt->bindValue(':role_id', $this->id);
            $stmt->bindValue(':permission_id', $permissionId);
            $stmt->execute();
        }

        return true;
    }
}
