<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\ExpenseCategory;
use \App\Models\Expenses;
use \App\Models\PaymentMethod;


class Expense extends Authenticated
{
    protected $user, $expense_categories,$payment_methods, $expense, $user_id, $_SESSION;

    protected function before()
    {
        parent::before();
        $this->user = Auth::getUser();
        $this->expense_categories = ExpenseCategory::getUserExpenseCategories($this->user->id);
        $this->payment_methods = PaymentMethod::getUserPaymentCategories($this->user->id);

    }

    protected function after()
    { 
        unset($_SESSION['s_expense']);
        unset($_SESSION['e_expense']);
    }

    public function showAction()
    {
        View::renderTemplate('Expense/show.twig', [
            'user' => $this->user,
            'expense_categories' => $this->expense_categories,
            'payment_methods' => $this->payment_methods,
        ]);
    }

    public function addAction()
    {
        $expense = new Expenses($_POST);
        $user_id = $this->user->id;

        if ($expense->addExpense($user_id)) {
            $_SESSION['s_expense'] = 'Wydatek został dodany prawidłowo.';
        }

        $this->redirect('/expense/show');
    }

    public function limitAction()
    {
        $user_id = $this->user->id;
        $categoryId = $this->route_params['categoryid'];

        echo json_encode(ExpenseCategory::getLimit($user_id, $categoryId), JSON_UNESCAPED_UNICODE);
    }

    public function monthlyExpensesSumAction()
    {
        $user_id = $this->user->id;
        $category = $this->route_params['category'];
        $date = $this->route_params['date'];
        
        echo json_encode(ExpenseCategory::getMonthlyCategoryExpenseSum($user_id, $category, $date), JSON_UNESCAPED_UNICODE);
    }

}
