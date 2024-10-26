<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Application;
use App\Models\Permission;

class PermissionController extends Controller
{
    public function index(Request $request, Response $response)
    {
        if (Application::isGuest()) {
            $response->redirect('/login');
            return;
        }

        $permissions = Permission::findAll();
        return $this->render('admin/permissions/index', [
            'permissions' => $permissions
        ]);
    }

    public function create(Request $request, Response $response)
    {
        if (Application::isGuest()) {
            $response->redirect('/login');
            return;
        }

        $permission = new Permission();

        if ($request->isPost()) {
            $permission->loadData($request->getBody());
            
            if ($permission->save()) {
                Application::$app->session->setFlash('success', 'Permission created successfully');
                $response->redirect('/permissions');
                return;
            }
        }

        return $this->render('admin/permissions/create', [
            'model' => $permission
        ]);
    }

    public function edit(Request $request, Response $response, $id = null)
    {
        error_log("=== Permission Edit Debug ===");
        error_log("Request Method: " . $request->method());
        error_log("Request Path: " . $request->getPath());
        error_log("ID Parameter: " . $id);
        error_log("User ID: " . (Application::$app->user ? Application::$app->user->id : 'Guest'));

        if (Application::isGuest()) {
            error_log("User is guest - redirecting to login");
            $response->redirect('/login');
            return;
        }

        $permission = Permission::findOne(['id' => $id]);
        error_log("Found permission: " . ($permission ? json_encode([
            'id' => $permission->id,
            'name' => $permission->name,
            'description' => $permission->description
        ]) : 'null'));

        if (!$permission) {
            error_log("Permission not found - redirecting");
            Application::$app->session->setFlash('error', 'Permission not found');
            $response->redirect('/permissions');
            return;
        }

        if ($request->isPost()) {
            error_log("Processing POST request");
            error_log("POST data: " . json_encode($request->getBody()));
            
            $permission->loadData($request->getBody());
            
            if ($permission->save()) {
                error_log("Permission saved successfully");
                Application::$app->session->setFlash('success', 'Permission updated successfully');
                $response->redirect('/permissions');
                return;
            } else {
                error_log("Failed to save permission: " . json_encode($permission->errors));
            }
        }

        return $this->render('admin/permissions/edit', [
            'model' => $permission
        ]);
    }

    public function delete(Request $request, Response $response, $id)
    {
        if (Application::isGuest()) {
            $response->redirect('/login');
            return;
        }

        $permission = Permission::findOne(['id' => $id]);
        if ($permission) {
            if ($permission->delete()) {
                Application::$app->session->setFlash('success', 'Permission deleted successfully');
            } else {
                Application::$app->session->setFlash('error', 'Failed to delete permission');
            }
        }

        $response->redirect('/permissions');
    }
}
