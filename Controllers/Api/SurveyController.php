<?php

namespace App\Controllers\Api;

use App\Controllers\Api\Base\ApiController;
use App\Models\Survey;
use App\Core\Request;
use App\Core\Response;

class SurveyController extends ApiController
{
    public function getSurvey(Request $request, Response $response)
    {
        $surveys = Survey::getAll();
        return $this->sendJson(['data' => $surveys]);
    }

    public function submitSurvey(Request $request, Response $response)
    {
        $this->authenticate($request);

        $body = $request->getBody();
        $survey = new Survey();
        $survey->loadData($body);

        if (!$survey->validate() || !$survey->save()) {
            return $this->sendJson(['errors' => $survey->errors], 422);
        }

        return $this->sendJson(['message' => 'Survey submitted successfully'], 201);
    }
}
