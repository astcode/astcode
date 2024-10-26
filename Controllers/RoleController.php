<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Application;
use App\Models\Role;
use App\Models\Permission;

class RoleController extends Controller
{
    public function index(Request $request, Response $response)
    {
        if (Application::isGuest()) {
            $response->redirect('/login');
            return;
        }

        $roles = Role::findAll();
        $permissions = Permission::getAllGrouped();

        return $this->render('admin/roles/index', [
            'roles' => $roles,
            'permissions' => $permissions
        ]);
    }

    public function create(Request $request, Response $response)
    {
        if (Application::isGuest()) {
            $response->redirect('/login');
            return;
        }

        $role = new Role();
        $permissions = Permission::getAllGrouped();

        if ($request->isPost()) {
            $role->loadData($request->getBody());
            
            if ($role->validate() && $role->save()) {
                // Save permissions
                if (isset($_POST['permissions'])) {
                    foreach ($_POST['permissions'] as $permissionId) {
                        $role->addPermission((int)$permissionId);
                    }
                }
                
                Application::$app->session->setFlash('success', 'Role created successfully');
                $response->redirect('/roles');
                return;
            }
        }

        return $this->render('admin/roles/create', [
            'model' => $role,
            'permissions' => $permissions
        ]);
    }

    public function edit(Request $request, Response $response, $id = null)
    {
        if (Application::isGuest()) {
            $response->redirect('/login');
            return;
        }

        $role = Role::findOne(['id' => $id]);
        if (!$role) {
            Application::$app->session->setFlash('error', 'Role not found');
            $response->redirect('/roles');
            return;
        }

        $permissions = Permission::getAllGrouped();
        $rolePermissions = array_column($role->getPermissions(), 'id');

        if ($request->isPost()) {
            error_log("=== Processing Role Edit ===");
            error_log("POST data: " . json_encode($request->getBody()));
            
            $role->loadData($request->getBody());
            
            if ($role->save()) {
                error_log("Role saved successfully");
                
                // Update permissions
                $sql = "DELETE FROM role_permissions WHERE role_id = :role_id";
                $stmt = Role::prepare($sql);
                $stmt->bindValue(':role_id', $role->id);
                $stmt->execute();
                error_log("Deleted existing permissions");

                // Add new permissions if any are selected
                if (!empty($_POST['permissions'])) {
                    error_log("New permissions to add: " . json_encode($_POST['permissions']));
                    foreach ($_POST['permissions'] as $permissionId) {
                        $sql = "INSERT INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)";
                        $stmt = Role::prepare($sql);
                        $stmt->bindValue(':role_id', $role->id);
                        $stmt->bindValue(':permission_id', $permissionId);
                        $stmt->execute();
                        error_log("Added permission ID: " . $permissionId);
                    }
                } else {
                    error_log("No new permissions provided");
                }
                
                Application::$app->session->setFlash('success', 'Role updated successfully');
                $response->redirect('/roles');
                return;
            } else {
                error_log("Failed to save role: " . json_encode($role->errors));
            }
        }

        return $this->render('admin/roles/edit', [
            'model' => $role,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions
        ]);
    }

    public function delete(Request $request, Response $response)
    {
        if (Application::isGuest()) {
            $response->redirect('/login');
            return;
        }

        // Get ID from URL parameters
        $path = $request->getPath();
        preg_match('/\/roles\/delete\/(\d+)/', $path, $matches);
        $id = $matches[1] ?? null;

        if (!$id) {
            Application::$app->session->setFlash('error', 'No role ID provided');
            $response->redirect('/roles');
            return;
        }

        $role = Role::findOne(['id' => $id]);
        if ($role) {
            // Don't allow deletion of system roles
            if (in_array($role->name, ['super_admin', 'admin', 'user'])) {
                Application::$app->session->setFlash('error', 'Cannot delete system roles');
                $response->redirect('/roles');
                return;
            }

            if ($role->delete()) {
                Application::$app->session->setFlash('success', 'Role deleted successfully');
            } else {
                Application::$app->session->setFlash('error', 'Failed to delete role');
            }
        }

        $response->redirect('/roles');
    }
}
