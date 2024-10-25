<?php

use App\Core\Application;


function dd($data)
{
    echo '<pre>';
    die(var_dump($data));
    echo '</pre>';
}

function dump($data)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}

function prePrint($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

function ppd($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    die();
}

function makeName($user)
{
    return $user->firstname . ' ' . $user->lastname;
}

function nameOrGuest($user)
{
    switch (!Application::isGuest()) {
        case true:
            $name = Application::$app->user->getDisplayName();
            break;
        default:
            $name = 'Guest';
            break;
    }
    return $name;
}
