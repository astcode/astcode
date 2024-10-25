<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Controller;
use App\Models\SurveyForm;

class UserController extends Controller
{
    /**
     * Display the survey form and handle submissions.
     *
     * @param Request $request
     * @param Response $response
     * @return string
     */
    public function survey(Request $request, Response $response)
    {
        $model = new SurveyForm();

        if ($request->isPost()) {
            $model->loadData($request->getBody());

            if ($model->validate()) {
                // TODO: Process the survey data (e.g., save to database)
                // For demonstration, we'll assume it's successful

                // Redirect to a thank-you page
                $response->redirect('/thank-you');
                return;
            }
        }

        // Render the form (either first load or with validation errors)
        return $this->render('user/survey', [
            'model' => $model,
        ]);
    }

    /**
     * Display the thank-you page after successful submission.
     *
     * @return string
     */
    public function thankYou()
    {
        return $this->render('user/thank_you');
    }
}
