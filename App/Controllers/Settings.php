<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Incomes;
use \App\Models\IncomeCategory;
use \App\Models\ExpenseCategory;
use \App\Models\PaymentMethod;

class Settings extends Authenticated
{
    public $user, $income_categories, $expense_categories,$payment_categories;

    protected function before()
    {
        parent::before();
        $this->user = Auth::getUser();
        $this->income_categories = IncomeCategory::getUserIncomeCategories($this->user->id);
        $this->expense_categories = ExpenseCategory::getUserExpenseCategories($this->user->id);
        $this->payment_categories = PaymentMethod::getUserPaymentCategories($this->user->id);
    }
    protected function after()
    {
        unset($_SESSION['s_i_settings']);
        unset($_SESSION['e_i_settings']);
        unset($_SESSION['s_e_settings']);
        unset($_SESSION['e_e_settings']);
        unset($_SESSION['s_p_settings']);
        unset($_SESSION['e_p_settings']);
    }

    public function showAction()
    {
        View::renderTemplate('Settings/show.twig', [
            'user' => $this->user,
            'income_categories' => $this->income_categories,
            'expense_categories' => $this->expense_categories,
            'payment_categories' => $this->payment_categories,
        ]);
    }

    public function addIncomeCategoryAction()
    {
        $userId = $this->user->id;
        $newCategory = $_POST["new-category-name"];

        echo json_encode(IncomeCategory::addIncomeCategory($userId, $newCategory), JSON_UNESCAPED_UNICODE);
    }

    public function addExpenseCategoryAction()
    {
        $userId = $this->user->id;
        $newCategory = $_POST["new-category-name"];
        $categoryLimit = $_POST['category-limit'] ?? null;


        echo json_encode(ExpenseCategory::addExpenseCategory($userId, $newCategory, $categoryLimit), JSON_UNESCAPED_UNICODE);
    }

    public function addPaymentCategoryAction()
    {
        $userId = $this->user->id;
        $newCategory = $_POST["new-category-name"];

        echo json_encode(PaymentMethod::addPaymentCategory($userId, $newCategory), JSON_UNESCAPED_UNICODE);
    } 

    public function editIncomeCategoryAction()
    {
        $userId = $this->user->id;
        $categoryId = $this->route_params['categoryid'];
        $newCategoryName = $_POST["new-category-name"];

        echo json_encode(IncomeCategory::editIncomeCategory($userId, $categoryId, $newCategoryName), JSON_UNESCAPED_UNICODE);
    }

    public function editExpenseCategoryAction()
    {
        $userId = $this->user->id;
        $categoryId = $this->route_params['categoryid'];
        $newCategoryName = $_POST["new-category-name"];
        $categoryLimit = $_POST['category-limit'] ?? "";

        
        echo json_encode(ExpenseCategory::editExpenseCategory($userId, $categoryId, $newCategoryName, $categoryLimit), JSON_UNESCAPED_UNICODE);
    }

    public function toogleLimitAction()
    {
        $userId = $this->user->id;
        $categoryId = $this->route_params['categoryid'];

        echo json_encode(ExpenseCategory::toogleLimit($userId, $categoryId), JSON_UNESCAPED_UNICODE);
    }

    public function editPaymentCategoryAction()
    {
        $userId = $this->user->id;
        $categoryId = $this->route_params['categoryid'];
        $newCategoryName = $_POST["new-category-name"];

        echo json_encode(PaymentMethod::editPaymentCategory($userId, $categoryId, $newCategoryName), JSON_UNESCAPED_UNICODE);
    }

    public function removeIncomeCategoryAction()
    {
        $userId = $this->user->id;
        $categoryId = $this->route_params['categoryid'];

        echo json_encode(IncomeCategory::removeIncomeCategory($userId, $categoryId), JSON_UNESCAPED_UNICODE);
    }

    public function removeExpenseCategoryAction()
    {
        $userId = $this->user->id;
        $categoryId = $this->route_params['categoryid'];

        echo json_encode(ExpenseCategory::removeExpenseCategory($userId, $categoryId), JSON_UNESCAPED_UNICODE);
    }

    public function removePaymentCategoryAction()
    {
        $userId = $this->user->id;
        $categoryId = $this->route_params['categoryid'];

        echo json_encode(PaymentMethod::removePaymentCategory($userId, $categoryId), JSON_UNESCAPED_UNICODE);
    }
}