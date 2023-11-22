<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\IncomeCategory;
use \App\Models\Incomes;

class Income extends Authenticated
{
    protected $user, $income_categories, $income, $user_id, $_SESSION;

    protected function before()
    {
        parent::before();
        $this->user = Auth::getUser();
        $this->income_categories = IncomeCategory::getUserIncomeCategories($this->user->id);
    }

    protected function after()
    { 
        unset($_SESSION['s_income']);
        unset($_SESSION['e_income']);
    }

    public function showAction()
    {
        View::renderTemplate('Income/show.twig', [
            'user' => $this->user,
            'income_categories' => $this->income_categories,
        ]);
    }

    public function addAction()
    {
        $income = new Incomes($_POST);
        $user_id = $this->user->id;

        if ($income->addIncome($user_id)) {
            $_SESSION['s_income'] = 'Przychód został dodany prawidłowo.';
        }

        $this->redirect('/income/show');
    }

}
