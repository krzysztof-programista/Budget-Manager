<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\DateValidator;
use \App\Models\Incomes;
use \App\Models\Expenses;


class Balance extends Authenticated
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

    public function showCurrentMonthAction()
    {
        $user_id = $this->user->id;

        $period = "BILANS MIESIECZNY BIEŻĄCY";
        $start_date = $this->dateRange['startOfCurrentMonth'];
        $end_date = $this->dateRange['endOfCurrentMonth'];

        Balance::showAction($user_id, $period, $start_date, $end_date);
    }

    public function showPreviousMonthAction()
    {
        $user_id = $this->user->id;

        $period = "BILANS MIESIECZNY POPRZEDNI";
        $start_date = $this->dateRange['startOfPreviousMonth'];
        $end_date = $this->dateRange['endOfPreviousMonth'];

        Balance::showAction($user_id, $period, $start_date, $end_date);
    }

    public function showYearAction()
    {
        $user_id = $this->user->id;

        $period = "BILANS ROCZNY";
        $start_date = $this->dateRange['startOfCurrentYear'];
        $end_date = $this->dateRange['endOfCurrentYear'];

        Balance::showAction($user_id, $period, $start_date, $end_date);
    }

    public function showCustomAction()
    {
        $user_id = $this->user->id;

        $period = "BILANS INDYWIDUALNY";
        

        $start_date = $this->route_params['startdate'];
        $end_date = $this->route_params['enddate'];

        if ($start_date && $end_date){
            Balance::showAction($user_id, $period, $start_date, $end_date);
        }
    }

    public function showAction($user_id, $period, $start_date, $end_date)
    {
        $incomes = Incomes::getIncomes($user_id, $start_date, $end_date);
        $expenses = Expenses::getExpenses($user_id, $start_date, $end_date);
        $income_balance = Incomes::getIncomeBalance($user_id, $start_date, $end_date);
        $expense_balance = Expenses::getExpenseBalance($user_id, $start_date, $end_date);


        View::renderTemplate('Balance/show.twig', [
            'user' => $this->user,
            'period' => $period,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'income_balance' => $income_balance,
            'expense_balance' => $expense_balance,
            'incomes' => $incomes,
            'expenses' => $expenses,
        ]);
    }


}
