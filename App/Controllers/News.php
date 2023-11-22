<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\DateValidator;
use \App\Models\Incomes;
use \App\Models\Expenses;


class News extends Authenticated
{
    protected $user, $dateRange;

    
    public function __construct($route_params)
    {
        parent::__construct($route_params);
        $this->dateRange = DateValidator::getDate();
    }
    
    protected function before()
    {
        parent::before();
        $this->user = Auth::getUser();
    }

    protected function after()
    {
        unset($_SESSION['e_period']);
    }

    public function addAction()
    {
        $category = 'xd';

        //var_dump ($this->dateRange);
        echo $category;
    }
}