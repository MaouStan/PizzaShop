<?php
session_start();

// ======== Require Class ======== \\
require_once __DIR__ . "/UserManager.php";
$userManager = new UserManager();

require_once __DIR__ . "/PizzaManager.php";
$pizzaManager = new PizzaManager();

require_once __DIR__ . "/OrderManager.php";
$orderManager = new OrderManager();

require_once __DIR__ . "/AdminManager.php";
$adminManager = new AdminManager();



// ======== Actions ======== \\
$action = NULL;

if (isset($_GET['action'])) {
    $action = $_GET['action'];
}

// ======== UserManger ======== \\
// ----- login
if ($action == "login") {
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $result = $userManager->login($email, $pass);
    echo json_encode($result);
}



// ======== PizzaManager ======== \\
// ----- getPizzas
if ($action == "getPizzas") {
    $result = $pizzaManager->GetPizzas();
    echo json_encode($result);
}

// ----- getPizza
if ($action == "getPizza") {
    $pizza_id = $_GET['pizza_id'];
    $result = $pizzaManager->GetPizza($pizza_id);
    echo json_encode($result);
}

// ======== AdminManager ======== \\
// ----- adminGetOrder
if ($action == "adminGetOrder") {
    $result = $adminManager->GetOrders($_POST['status'], $_POST['time']);
    echo json_encode($result);
}

// ----- saveStatus
if ($action == "saveStatus") {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $result = $adminManager->saveStatus($id, $status);
    echo json_encode($result);
}

// ======== OrderManager ======== \\
// ----- addToCart
if ($action == "addToCart") {
    $pizza_variation_id = $_POST['variation_id'];
    $quantity = $_POST['quantity'];
    $result = $orderManager->addItemIntoCart($pizza_variation_id, $quantity);
    echo json_encode($result);
}

// ----- getCartCount
if ($action == "getCartCount") {
    $result = $orderManager->getCartCount();
    echo json_encode($result);
}

// ----- getOrders
if ($action == "getOrders") {
    $status = $_GET['status'];
    $time = $_GET['time'];
    $result = $orderManager->GetOrderHistoryOrder($status, $time);
    echo json_encode($result);
}

// ----- getCart
if ($action == "getCart") {
    $result = $orderManager->GetCart();
    echo json_encode($result);
}

// ----- updateItemQuantity
if ($action == "updateItemQuantity") {
    $variation_id = $_POST['variation_id'];
    $quantity = $_POST['quantity'];
    $result = $orderManager->updateItemQuantity($variation_id, $quantity);
    echo json_encode($result);
}

// ----- removeItemFromCart
if ($action == "removeItemFromCart") {
    $variation_id = $_POST['variation_id'];
    $result = $orderManager->removeItemFromCart($variation_id);
    echo json_encode($result);
}
// ----- checkout
if ($action == "checkout") {
    $result = $orderManager->checkout(
        $_POST['saveData'],
        $_POST['name'],
        $_POST['phone'],
        $_POST['address'],
        $_POST['paymentMethod']
    );


    echo json_encode($result);
}

// ----- pay
if ($action == "pay") {
    $order_id = $_POST['order_id'];
    $result = $orderManager->pay($order_id);
    echo json_encode($result);
}
