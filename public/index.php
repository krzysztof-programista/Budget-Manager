<?php

require dirname(__DIR__) . '/vendor/autoload.php';

error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

session_start();

$router = new Core\Router();

$router->add('api/limit/{categoryid:\d+}', ['controller' => 'Expense', 'action' => 'limit']);
$router->add('api/limitSum/{category:[\wżźćńółęąśŻŹĆĄŚĘŁÓŃ ]+}/{date:[\d-]+}', ['controller' => 'Expense', 'action' => 'monthlyExpensesSum']);
$router->add('tooglelimit/{categoryid:[\d-]+}', ['controller' => 'Settings', 'action' => 'toogleLimit']);

$router->add('settings/income/add', ['controller' => 'Settings', 'action' => 'addIncomeCategory']);
$router->add('settings/expense/add', ['controller' => 'Settings', 'action' => 'addExpenseCategory']);
$router->add('settings/payment/add', ['controller' => 'Settings', 'action' => 'addPaymentCategory']);

$router->add('settings/income/edit/{categoryid:[\d-]+}', ['controller' => 'Settings', 'action' => 'editIncomeCategory']);
$router->add('settings/expense/edit/{categoryid:[\d-]+}', ['controller' => 'Settings', 'action' => 'editExpenseCategory']);
$router->add('settings/payment/edit/{categoryid:[\d-]+}', ['controller' => 'Settings', 'action' => 'editPaymentCategory']);

$router->add('settings/income/remove/{categoryid:[\d-]+}', ['controller' => 'Settings', 'action' => 'removeIncomeCategory']);
$router->add('settings/expense/remove/{categoryid:[\d-]+}', ['controller' => 'Settings', 'action' => 'removeExpenseCategory']);
$router->add('settings/payment/remove/{categoryid:[\d-]+}', ['controller' => 'Settings', 'action' => 'removePaymentCategory']);

$router->add('custombalance/{startdate:[\d-]+}/{enddate:[\d-]+}', ['controller' => 'Balance', 'action' => 'showCustom']);

$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('login', ['controller' => 'Login', 'action' => 'new']);
$router->add('logout', ['controller' => 'Login', 'action' => 'destroy']);
$router->add('password/reset/{token:[\da-f]+}', ['controller' => 'Password', 'action' => 'reset']);
$router->add('signup/activate/{token:[\da-f]+}', ['controller' => 'Signup', 'action' => 'activate']);
$router->add('{controller}/{action}');
    
$url = $_SERVER['QUERY_STRING'];

$router->dispatch($url);


