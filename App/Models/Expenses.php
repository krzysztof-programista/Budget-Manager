<?php

namespace App\Models;

use PDO;

class Expenses extends \Core\Model
{
    public $user_id, $category, $amount, $date, $comment, $payment_method;

    public $errors = [];

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    public function addExpense($user_id)
    {
        $this->validate();

        if (empty($this->errors)) {
            $sql = 'INSERT INTO expenses (user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment)
                      VALUES (:user_id, :category, :payment_method, :amount, :date, :comment)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindValue(':category', $this->category, PDO::PARAM_INT);
            $stmt->bindValue(':payment_method', $this->payment_method, PDO::PARAM_INT);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date', $this->date, PDO::PARAM_STR);
            $stmt->bindValue(':comment', $this->comment, PDO::PARAM_STR);
            return $stmt->execute();
        }
        return false;
    }

    public function validate()
    {
        //Expense amount
        if ($this->amount == '') {
            $this->errors[] = 'Kwota wydatku musi zostać podana';
        }

        //Expense date
        if ($this->date == '') {
            $this->errors[] = 'Data wydatku musi zostać podana';
        }

        if ($this->date < '2000-01-01') {
            $this->errors[] = 'Data jest nieprawidlowa';
        }


        if (!empty($this->errors)) {
            $_SESSION['e_expense'] = 'Wydatek nie został dodany, wpisz poprawne dane';
        }
    }

    public static function getExpenseBalance($user_id, $start_date, $end_date)
    {
        $sql = 'SELECT `name`, SUM(`amount`) AS expenseSum FROM `expenses_category_assigned_to_users`, `expenses`
                WHERE `expenses`.`expense_category_assigned_to_user_id` = `expenses_category_assigned_to_users`.`id`
                AND `expenses`.`user_id` = :userId 
                AND `expenses`.`date_of_expense` BETWEEN :startDate AND :endDate
                GROUP BY `expense_category_assigned_to_user_id` 
                ORDER BY expenseSum DESC';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userId', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':startDate', $start_date, PDO::PARAM_STR);
        $stmt->bindParam(':endDate', $end_date, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getExpenses($user_id, $start_date, $end_date)
    {
        $sql = 'SELECT `name`, `amount`, `date_of_expense`, `expense_comment` FROM  `expenses_category_assigned_to_users`,`expenses`
                WHERE `expenses`.`expense_category_assigned_to_user_id` = `expenses_category_assigned_to_users`.`id`
                AND `expenses`.`user_id` = :userId 
                AND `expenses`.`date_of_expense` BETWEEN :startDate AND :endDate
                ORDER BY expenses.date_of_expense DESC';
  
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userId', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':startDate', $start_date, PDO::PARAM_STR);
        $stmt->bindParam(':endDate', $end_date, PDO::PARAM_STR);
        $stmt->execute();
  
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}