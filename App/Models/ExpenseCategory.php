<?php

namespace App\Models;

use PDO;
use \App\DateValidator;

class ExpenseCategory extends \Core\Model
{
    public $expense_categories;

    public static function getUserExpenseCategories($user_id)
    {
        $sql = 'SELECT *
            FROM expenses_category_assigned_to_users
            WHERE user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $expense_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $expense_categories;
    }

    public static function addExpenseCategory($userId, $expenseCategory, $categoryLimit)
    {
      $response = static::validateCategory($userId, $expenseCategory);

      //brak walidacji limitu ze względu na walidację w formularzu

      if ($response["message_type"] != "error") {

        if ($categoryLimit == null) {
          $sql = 'INSERT INTO expenses_category_assigned_to_users (user_id, name, cash_limit)
                  VALUES (:user_id, :name, :cashLimit)';
        } else {
          $sql = 'INSERT INTO expenses_category_assigned_to_users (user_id, name, is_limit_active, cash_limit)
          VALUES (:user_id, :name, :isLimitActive, :cashLimit)';
        }

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':name', $expenseCategory, PDO::PARAM_STR);
        $stmt->bindParam(':cashLimit', $categoryLimit, PDO::PARAM_INT);

        if ($categoryLimit !== null) {
          $isLimitActive = 1;
          $stmt->bindParam(':isLimitActive', $isLimitActive, PDO::PARAM_INT);
        }

        $stmt->execute();
  
        $response["message_type"] = "success";
        $response["message"] = "Kategoria dodana prawidłowo.";
      }
      return $response;
    }

    public static function editExpenseCategory($userId, $categoryId, $newCategoryName, $categoryLimit)
    {
      $response = ["message_type" => "", "message" => ""];
      
      if (empty($newCategoryName) && $categoryLimit == "") {
        $response["message_type"] = "error";
        $response["message"] = "Oba pola nie mogą być puste";
      }

      if (!empty($newCategoryName)) {
          $response = static::validateCategory($userId, $newCategoryName);
      }

      //brak walidacji limitu ze względu na walidację w formularzu

      if ($response["message_type"] != "error") {

        if ($categoryLimit != "" && empty($newCategoryName)) {
            $sql = 'UPDATE `expenses_category_assigned_to_users`
                    SET `cash_limit` = :categoryLimit
                    WHERE `user_id` = :userId AND `id` = :categoryId
                    LIMIT 1';
        } else if (!empty($newCategoryName) && $categoryLimit == "") {
            $sql = 'UPDATE `expenses_category_assigned_to_users`
                    SET `name` = :newCategoryName
                    WHERE `user_id` = :userId AND `id` = :categoryId
                    LIMIT 1';
        } else {
            $sql = 'UPDATE `expenses_category_assigned_to_users`
                    SET `name` = :newCategoryName, `cash_limit` = :categoryLimit
                    WHERE `user_id` = :userId AND `id` = :categoryId
                    LIMIT 1';
        }

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);

        if (!empty($newCategoryName)) {
            $stmt->bindParam(':newCategoryName', $newCategoryName, PDO::PARAM_STR);
        }

        if ($categoryLimit != "") {
            $stmt->bindParam(':categoryLimit', $categoryLimit, PDO::PARAM_INT);
        }

        $stmt->execute();

        $response["message_type"] = "success";
        $response["message"] = "Kategoria została edytowana.";
      }

      return $response;
    }

    public static function toogleLimit($userId, $categoryId)
    {
        $sql = 'UPDATE `expenses_category_assigned_to_users`
                SET `is_limit_active` = CASE
                    WHEN `is_limit_active` = 0 THEN 1
                    WHEN `is_limit_active` = 1 THEN 0
                END
                WHERE `user_id` = :userId AND `id` = :categoryId';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);

        $stmt->execute();

        return ["message_type" => "success", "message" => "Status limitu został zmieniony. Podaj kwotę lub zamknij okno."];
    }

    public static function removeExpenseCategory($userId, $categoryId)
    {
      $response = ["message_type" => "", "message" => ""];

      $sql = 'DELETE FROM `expenses_category_assigned_to_users`
                WHERE `user_id` = :user_id AND `id` = :categoryId
                LIMIT 1';
  
      $db = static::getDB();
      $stmt = $db->prepare($sql);
      $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
      $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
  
      $stmt->execute();
  
      static::removeCategoryExpenses($userId, $categoryId);

      $response["message_type"] = "success";
      $response["message"] = "Kategoria przychodów została usunięta.";
  
      return $response;
    }
  
    private static function removeCategoryExpenses($user_id, $categoryId)
    {
      $sql = 'DELETE FROM `expenses`
                WHERE `user_id` = :user_id AND `expense_category_assigned_to_user_id` = :categoryId';
  
      $db = static::getDB();
      $stmt = $db->prepare($sql);
      $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
      $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
  
      $stmt->execute();
    }

    private static function validateCategory($userId, $expenseCategory)
    {
      $response = ["message_type" => "", "message" => ""];
      $categoryToUpper = strtoupper($expenseCategory);

      $pattern = '/[^\wżźćąśęłóńŻŹĆĄŚĘŁÓŃ0-9 ]/i';
      $result = preg_match($pattern, $expenseCategory);
  
      if ($result == 1) {
        $response["message_type"] = "error";
        $response["message"] = "Użyto niedozwolonych znaków.";
      } 

      if (static::checkIfCategoryExists($userId, $categoryToUpper)) {
          $response["message_type"] = "error";
          $response["message"] = "Nazwa kategorii jest już zajęta";
      }

      return $response;
    }

    private static function checkIfCategoryExists($userId, $expenseCategory)
    {
      $sql = 'SELECT name FROM  expenses_category_assigned_to_users
                WHERE user_id = :userId AND  name = :category';
  
      $db = static::getDB();
  
      $stmt = $db->prepare($sql);
      $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
      $stmt->bindParam(':category', $expenseCategory, PDO::PARAM_STR);
  
      $stmt->execute();
  
      if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
        return false;
      }
      return true;
    }

    public static function getLimit($userId, $categoryId)
    {
      $sql = 'SELECT is_limit_active, cash_limit FROM  expenses_category_assigned_to_users
      WHERE user_id = :userId AND id = :categoryId';
  
      $db = static::getDB();
      $stmt = $db->prepare($sql);
      $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
      $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);

      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      return $result;
    } 

    public static function getCategoryId($userId, $category)
    {
      $sql = 'SELECT id FROM  expenses_category_assigned_to_users
      WHERE user_id = :userId AND name = :category';
  
      $db = static::getDB();
      $stmt = $db->prepare($sql);
      $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
      $stmt->bindParam(':category', $category, PDO::PARAM_STR);

      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      return $result['id'];
    } 

    public static function getMonthlyCategoryExpenseSum($user_id, $categoryId, $date)
    {
        $year = substr($date, 0, 4);
        $month = substr($date, 5, 2);
        $firstDay = substr($date, 0, 8) . '01';
        $lastDay = substr($date, 0, 8) . DateValidator::findLastDayOfMonth($month, $year);

        $sql = 'SELECT SUM(amount) AS monthlySum FROM expenses
                WHERE user_id = :user_id AND expense_category_assigned_to_user_id = :category_id
                AND date_of_expense BETWEEN :start_date AND :end_date';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindParam(':start_date', $firstDay, PDO::PARAM_STR);
        $stmt->bindParam(':end_date', $lastDay, PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['monthlySum'];
    }
}
