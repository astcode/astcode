<?php

namespace App\Controllers;

// use App\Core\Application;
use App\Core\Application;
use App\Core\Controller;
use App\Core\Middlewares\AuthMiddleware;
use App\Core\Request;
use App\Core\Response;
use App\Models\ContactForm;
use App\Models\LoginForm;
use App\Models\User;
// use App\Core\Request;

class SiteController extends Controller
{
    // public $isUserLoggedIn;

    public function __construct()
    {
        
    }

    /**
     * Home page
     * @return string
     */
    public function home()
    {        
        $params = [
            'name' => User::nameOrGuest()
        ];
        return $this->render('home', $params);
    }

    
    public function contact(Request $request, Response $response)
    {
        $contact = new ContactForm();
        if ($request->isPost()) {
            echo "Form submitted\n";
            $data = $request->getBody();
            echo "Received data: " . print_r($data, true) . "\n";
            $contact->loadData($data);
            if ($contact->validate() && $contact->send()) {
                echo "Form validated and sent\n";
                Application::$app->session->setFlash('success', 'Thanks for contacting us.');
                return $response->redirect('/thankyou');
            } else {
                echo "Form validation or sending failed\n";
                echo "Errors: " . print_r($contact->errors, true) . "\n";
            }
        }
        return $this->render('contact', [
            'model' => $contact
        ]);
    }


    // public function handleContact($request)
    // {
    //     $body = $request->getBody();
    //     return 'Handling submitted data';
    // }
    
    public function thankyou()
    {
        echo "Thank you method called"; // Debug line
        return $this->render('thankyou');
    }
}
