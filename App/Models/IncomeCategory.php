<?php

namespace App\Models;

use PDO;

class IncomeCategory extends \Core\Model
{
  public $income_categories;

  public static function getUserIncomeCategories($user_id)
  {
    $sql = 'SELECT *
            FROM incomes_category_assigned_to_users
            WHERE user_id = :user_id';

    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $income_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $income_categories;
  }

  public static function addIncomeCategory($userId, $incomeCategory)
  {
    $response = static::validateCategory($userId, $incomeCategory);

    if ($response["message_type"] != "error") {
      $sql = 'INSERT INTO incomes_category_assigned_to_users (user_id, name)
              VALUES (:user_id, :name)';

      $db = static::getDB();
      $stmt = $db->prepare($sql);
      $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
      $stmt->bindParam(':name', $incomeCategory, PDO::PARAM_STR);

      $stmt->execute();

      $response["message_type"] = "success";
      $response["message"] = "Kategoria dodana prawidłowo.";
    }
    return $response;
  }

  private static function validateCategory($userId, $incomeCategory)
  {
    $response = ["message_type" => "", "message" => ""];
    $categoryToUpper = strtoupper($incomeCategory);

    $pattern = '/[^\wżźćąśęłóńŻŹĆĄŚĘŁÓŃ0-9 ]/i';
    $result = preg_match($pattern, $categoryToUpper);

    if ($result == 1) {
        $response["message_type"] = "error";
        $response["message"] = "Użyto niedozwolonyuch znaków.";
    }

    if (static::checkIfCategoryExists($userId, $categoryToUpper)) {
        $response["message_type"] = "error";
        $response["message"] = "Nazwa kategorii jest już zajęta";
    }

    return $response;
  }

  private static function checkIfCategoryExists($userId, $incomeCategory)
  {
    $sql = 'SELECT name FROM  incomes_category_assigned_to_users
              WHERE user_id = :userId AND  name = :category';

    $db = static::getDB();

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':category', $incomeCategory, PDO::PARAM_STR);

    $stmt->execute();

    if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
      return false;
    }
    return true;
  }

  public static function editIncomeCategory($userId, $categoryId, $newCategoryName)
  {
    $response = static::validateCategory($userId, $newCategoryName);

    if ($response["message_type"] != "error") {
      $sql = 'UPDATE `incomes_category_assigned_to_users`
                  SET `name`= :newCategory WHERE `user_id` = :userId AND `id` = :categoryId
                  LIMIT 1';

      $db = static::getDB();
      $stmt = $db->prepare($sql);
      $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
      $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
      $stmt->bindParam(':newCategory', $newCategoryName, PDO::PARAM_STR);

      $stmt->execute();

      $response["message_type"] = "success";
      $response["message"] = "Kategoria edytowana prawidłowo.";
    }
    return $response;
  }

  public static function removeIncomeCategory($userId, $categoryId)
  {
    $response = ["message_type" => "", "message" => ""];

    $sql = 'DELETE FROM `incomes_category_assigned_to_users`
              WHERE `user_id` = :user_id AND `id` = :categoryId
              LIMIT 1';

    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);

    $stmt->execute();

    static::removeCategoryIncomes($userId, $categoryId);

    $response["message_type"] = "success";
    $response["message"] = "Kategoria przychodów została usunięta.";

    return $response;
  }

  private static function removeCategoryIncomes($user_id, $categoryId)
  {
    $sql = 'DELETE FROM `incomes`
              WHERE `user_id` = :user_id AND `income_category_assigned_to_user_id` = :categoryId';

    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);

    $stmt->execute();
  }
}
