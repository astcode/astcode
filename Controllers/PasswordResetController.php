<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\User;
use App\Models\PasswordResetForm;

class PasswordResetController extends Controller
{
    public function forgotPassword(Request $request, Response $response)
    {
        $passwordResetForm = new PasswordResetForm();

        if ($request->isPost()) {
            $passwordResetForm->loadData($request->getBody());
            if ($passwordResetForm->validate() && $passwordResetForm->sendResetLink()) {
                $this->setFlash('success', 'If an account exists with that email, we have sent a password reset link.');
                return $response->redirect('/login');
            }
        }

        return $this->render('auth/forgot-password', [
            'model' => $passwordResetForm
        ]);
    }

    public function resetPassword(Request $request, Response $response)
    {
        $token = $request->getBody()['token'] ?? null;
        $user = User::findByPasswordResetToken($token);

        if (!$user) {
            $this->setFlash('error', 'Invalid or expired password reset token.');
            return $response->redirect('/forgot-password');
        }

        $passwordResetForm = new PasswordResetForm();

        if ($request->isPost()) {
            $passwordResetForm->loadData($request->getBody());
            if ($passwordResetForm->validate() && $passwordResetForm->resetPassword($user)) {
                $this->setFlash('success', 'Your password has been reset successfully.');
                return $response->redirect('/login');
            }
        }

        return $this->render('auth/reset-password', [
            'model' => $passwordResetForm,
            'token' => $token
        ]);
    }
}
