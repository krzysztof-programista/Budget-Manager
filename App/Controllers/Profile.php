<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;


class Profile extends Authenticated
{
    public $user;

    protected function before()
    {
        parent::before();
        $this->user = Auth::getUser();
    }


    public function showAction()
    {
        View::renderTemplate('Profile/show.twig', [
            'user' => $this->user
        ]);
    }


    public function editAction()
    {
        View::renderTemplate('Profile/edit.twig', [
            'user' => $this->user
        ]);
    }


    public function updateAction()
    {
        if ($this->user->updateProfile($_POST)) {

            Flash::addMessage('Changes saved');

            $this->redirect('/profile/show');

        } else {

            View::renderTemplate('Profile/edit.twig', [
                'user' => $this->user
            ]);

        }
    }
}
