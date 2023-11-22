<?php

namespace App\Models;

use PDO;

class Incomes extends \Core\Model
{
  public $user_id, $category, $amount, $date, $comment;

  public $errors = [];

  public function __construct($data = [])
  {
    foreach ($data as $key => $value) {
      $this->$key = $value;
    };
  }

  public function addIncome($user_id)
  {
    $this->validate();

    if (empty($this->errors)) {
      $sql = 'INSERT INTO incomes (user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment)
                      VALUES (:user_id, :category, :amount, :date, :comment)';

      $db = static::getDB();
      $stmt = $db->prepare($sql);

      $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
      $stmt->bindValue(':category', $this->category, PDO::PARAM_INT);
      $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
      $stmt->bindValue(':date', $this->date, PDO::PARAM_STR);
      $stmt->bindValue(':comment', $this->comment, PDO::PARAM_STR);
      return $stmt->execute();
    }
    return false;
  }

  public function validate()
  {
    //Income amount
    if ($this->amount == '') {
      $this->errors[] = 'Kwota przychodu musi zostać podana';
    }

    //Income date
    if ($this->date == '') {
      $this->errors[] = 'Data przychodu musi zostać podana';
    }

    if ($this->date < '2000-01-01') {
      $this->errors[] = 'Data jest nieprawidlowa';
    }


    if (!empty($this->errors)) {
      $_SESSION['e_income'] = 'Przychód nie został dodany, wpisz poprawne dane';
    }
  }

  public static function getIncomeBalance($user_id, $start_date, $end_date)
  {
      $sql = 'SELECT `name`, SUM(`amount`) AS incomeSum FROM `incomes_category_assigned_to_users`, `incomes`
              WHERE `incomes`.`income_category_assigned_to_user_id` = `incomes_category_assigned_to_users`.`id`
              AND `incomes`.`user_id` = :userId 
              AND `incomes`.`date_of_income` BETWEEN :startDate AND :endDate
              GROUP BY `income_category_assigned_to_user_id` 
              ORDER BY incomeSum DESC';

      $db = static::getDB();
      $stmt = $db->prepare($sql);
      $stmt->bindParam(':userId', $user_id, PDO::PARAM_INT);
      $stmt->bindParam(':startDate', $start_date, PDO::PARAM_STR);
      $stmt->bindParam(':endDate', $end_date, PDO::PARAM_STR);
      $stmt->execute();

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getIncomes($user_id, $start_date, $end_date)
  {
      $sql = 'SELECT `name`, `amount`, `date_of_income`, `income_comment` FROM  `incomes_category_assigned_to_users`,`incomes`
              WHERE `incomes`.`income_category_assigned_to_user_id` = `incomes_category_assigned_to_users`.`id`
              AND `incomes`.`user_id` = :userId 
              AND `incomes`.`date_of_income` BETWEEN :startDate AND :endDate
              ORDER BY incomes.date_of_income DESC';

      $db = static::getDB();
      $stmt = $db->prepare($sql);
      $stmt->bindParam(':userId', $user_id, PDO::PARAM_INT);
      $stmt->bindParam(':startDate', $start_date, PDO::PARAM_STR);
      $stmt->bindParam(':endDate', $end_date, PDO::PARAM_STR);
      $stmt->execute();

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

}
