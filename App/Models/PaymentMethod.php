<?php

namespace App\Models;

use PDO;

class PaymentMethod extends \Core\Model
{
    public $payment_methods;

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    public static function getUserPaymentCategories($user_id)
    {
        $sql = 'SELECT *
            FROM payment_methods_assigned_to_users
            WHERE user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $payment_methods = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $payment_methods;
    }

    public static function addPaymentCategory($userId, $paymentCategory)
    {
      $response = static::validateCategory($userId, $paymentCategory);
  
      if ($response["message_type"] != "error") {
        $sql = 'INSERT INTO payment_methods_assigned_to_users (user_id, name)
                  VALUES (:user_id, :name)';
  
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':name', $paymentCategory, PDO::PARAM_STR);
  
        $stmt->execute();

        $response["message_type"] = "success";
        $response["message"] = "Kategoria dodana prawidłowo.";
      }
      return $response;
    }
  
    private static function validateCategory($userId, $paymentCategory)
    {
      $response = ["message_type" => "", "message" => ""];
      $categoryToUpper = strtoupper($paymentCategory);

      $pattern = '/[^\wżźćąśęłóńŻŹĆĄŚĘŁÓŃ0-9 ]/i';
      $result = preg_match($pattern, $paymentCategory);
  
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
  
    private static function checkIfCategoryExists($userId, $paymentCategory)
    {
      $sql = 'SELECT name FROM  payment_methods_assigned_to_users
                WHERE user_id = :userId AND  name = :category';
  
      $db = static::getDB();
  
      $stmt = $db->prepare($sql);
      $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
      $stmt->bindParam(':category', $paymentCategory, PDO::PARAM_STR);
  
      $stmt->execute();
  
      if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
        return false;
      }
      return true;
    }

    public static function editPaymentCategory($userId, $categoryId, $newCategoryName)
    {
      $response = static::validateCategory($userId, $newCategoryName);

      if ($response["message_type"] != "error") {
        $sql = 'UPDATE `payment_methods_assigned_to_users`
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

    public static function removePaymentCategory($userId, $categoryId)
    {
      $response = ["message_type" => "", "message" => ""];

      $sql = 'DELETE FROM `payment_methods_assigned_to_users`
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
                WHERE `user_id` = :user_id AND `payment_method_assigned_to_user_id` = :categoryId';
  
      $db = static::getDB();
      $stmt = $db->prepare($sql);
      $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
      $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
  
      $stmt->execute();
    }
}
