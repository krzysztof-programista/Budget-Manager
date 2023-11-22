<?php

namespace App\Controllers;
use \Core\View;
use \App\Models\User;
use \App\Models\IncomeCategory;

class Signup extends \Core\Controller
{
    public function newAction()
    {
        View::renderTemplate('Signup/new.twig');
    }

    public function createAction()
    {
        $user = new User($_POST);

        if ($user->save()) {

            $user->sendActivationEmail();

            $this->redirect('/signup/success');

        } else {

            View::renderTemplate('Signup/new.twig', [
                'user' => $user
            ]);

        }
    }

    public function successAction()
    {
        View::renderTemplate('Signup/success.twig');
    }

    public function activateAction()
    {
        User::activate($this->route_params['token']);

        $this->redirect('/signup/activated');        
    }

    public function activatedAction()
    {
        View::renderTemplate('Signup/activated.twig');
    }
}
