<?php
$db = null;
$tax =  15;
if (isset($GLOBALS['db'])) {
    $db = $GLOBALS['db'];
} else {
    require_once __DIR__ . "/../config/db.php";
    $db = new Database();
}
// create class

class OrderManager
{

    private $db;

    private $user_id;
    private $order_id;

    public function __construct()
    {
        global $db;
        $this->db = $db;

        if (isset($_SESSION['user_id'])) {
            $this->user_id = $_SESSION['user_id'];
        }

        if (isset($_SESSION['order_id'])) {
            $this->order_id = $_SESSION['order_id'];
        }
    }

    // CheckPermission
    public function checkPermission($order_id)
    {
        // user and order_id or user role is 'admin'
        if ($_SESSION['role'] == 'admin') {
            return true;
        }

        $sql = "SELECT * FROM orders WHERE order_id = ? and user_id = ?";
        $stmt = $this->db->prepared($sql, "ss", [$order_id, $this->user_id]);
        $result = $stmt->get_result();

        // count
        $count = count($result->fetch_assoc());

        return $count > 0;
    }

    // UserActiveOrder
    public function userActiveOrder()
    {
        $sql = "CALL user_active_order(?)";
        $stmt = $this->db->prepared($sql, "i", [$this->user_id]);
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $orderID = $row['order_id'];

        return [
            "status" => "success",
            "order_id" => $orderID
        ];
    }

    // addItemIntoCart
    public function addItemIntoCart($pizza_variation_id, $quantity)
    {
        // Define variables to hold your values
        $sql = "CALL add_item_into_cart(?, ?, ?)";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bind_param("sss", $this->order_id, $pizza_variation_id, $quantity);
        $stmt->execute();

        if ($stmt->errno) {
            echo "Error: " . $stmt->error;
        }

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        // Check the returned status and message
        if ($row['status'] === 'success') {
            $returnData = [
                'status' => 'success',
                'message' => 'Item added to cart'
            ];
        } else {
            $returnData = [
                'status' => 'error',
                'message' => 'Failed to add item to cart'
            ];
        }
        return $returnData;
    }

    // getItemCount
    public function getCartCount()
    {
        $sql = "CALL GetItemCount(?)";
        $stmt = $this->db->prepared($sql, "i", array($this->order_id));
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $status = $row['status'];
        $message = $row['message'];
        $itemCount = $row['item_count'];

        return [
            "status" => $status,
            "message" => $message,
            "data" => array(
                "count" => $itemCount
            )
        ];
    }

    // GetOrderHistoryOrder
    public function GetOrderHistoryOrder($status, $time)
    {
        // $status is 'All'
        $sql = "SELECT * FROM orders where user_id = ? and  ";
        if ($status == 'all') {
            $sql .= "order_status != '0' ";
        } else {
            $sql .= "order_status = ? ";
        }

        // $time is 'DESC'
        if ($time == 'DESC') {
            $sql .= "ORDER BY order_time DESC";
        } else {
            $sql .= "ORDER BY order_time ASC";
        }

        if ($status == 'all') {
            $stmt = $this->db->prepared($sql, "i", array($this->user_id));
        } else {
            $stmt = $this->db->prepared($sql, "is", array($this->user_id, $status));
        }

        $result = $stmt->get_result();
        $orders = array();
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }


        return array(
            "orders" => $orders
        );
    }

    // GetCarts
    public function GetCart()
    {

        $sql = "CALL GetCart(?)";
        $stmt = $this->db->prepared($sql, "i", array($this->order_id));
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (isset($row['status']) && $row['status'] == 'error') {
            return [
                "status" => $row['status'],
                "message" => $row['message']
            ];
        } else {


            $this->db = new Database();
            $cart = [];
            $sql = "CALL GetCart(?)";
            $stmt = $this->db->prepared($sql, "i", array($this->order_id));
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $cart[] = $row;
            }

            return array(
                "status" => "success",
                "message" => "Cart retrieved",
                "items" => $cart
            );
        }
    }

    // updateItemQuantity
    public function updateItemQuantity($variation_id, $quantity)
    {
        $update_item_sql = "UPDATE order_items SET quantity = ? WHERE order_id = ? AND variation_id = ?";
        $stmt = $this->db->prepared($update_item_sql, "sss", array($quantity, $this->order_id, $variation_id));
        if ($stmt->affected_rows > 0) {
            return array(
                "status" => "success",
                "message" => "Item quantity updated"
            );
        } else {
            return array(
                "status" => "error",
                "message" => "Failed to update item quantity"
            );
        }
    }

    // removeItemFromCart
    public function removeItemFromCart($variation_id)
    {
        $remove_item_sql = "DELETE FROM order_items WHERE order_id = ? AND variation_id = ?";
        $stmt = $this->db->prepared($remove_item_sql, "ii", array($this->order_id, $variation_id));
        if ($stmt->affected_rows > 0) {
            return array(
                "status" => "success",
                "message" => "Item removed from cart"
            );
        } else {
            return array(
                "status" => "error",
                "message" => "Failed to remove item from cart"
            );
        }
    }

    // GetOrder
    public function GetOrder()
    {
        global $tax;

        // calc update total proc
        $sql = "CALL update_order_total(?, ?)";
        $this->db->prepared($sql, "is", array($this->order_id, $tax));

        $sql = "SELECT * FROM orders WHERE order_id = ?";
        $stmt = $this->db->prepared($sql, "i", array($this->order_id));
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        // getItemCount this
        $count = $this->getCartCount()['data']['count'];


        return array(
            "status" => "success",
            "message" => "Order retrieved",
            "order" => $row,
            "count" => $count
        );
    }

    // checkout
    public function checkOut($saveData, $name, $phone, $address, $paymentMethod)
    {
        // check is saveData to user
        if ($saveData) {
            $sql = "UPDATE users SET name = ?, phone = ?, address = ? WHERE user_id = ?";
            $stmt = $this->db->prepared($sql, "sssi", array($name, $phone, $address, $this->user_id));

            // setSession
            $_SESSION['phone'] = $phone;
            $_SESSION['address'] = $address;
        }

        global $tax;

        // Call Procedure
        $sql = "CALL Checkout(?, ?, ?, ?, ?, ?, ?)";
        $types = "iisssss";
        $params = array(
            $this->user_id,
            $this->order_id,
            $paymentMethod,
            $name,
            $phone,
            $address,
            $tax
        );


        $stmt = $this->db->prepared($sql, $types, $params);
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['v_is_error'] == 0) {

            $this->db = new Database();
            $orderID = $this->userActiveOrder()['order_id'];
            $this->order_id = $orderID;
            $_SESSION['order_id'] = $orderID;
            return array(
                "status" => "success",
                "message" => "Checkout success"
            );
        } else {
            return array(
                "status" => "error",
                "message" => "Failed to checkout"
            );
        }
    }

    // GetOrderDetails
    public function GetOrderDetails($order_id)
    {
        if (!$this->checkPermission($order_id)) {
            return [
                "status" => "error",
                "message" => "Failed to get order details"
            ];
        }

        // Get order details
        $sql = "SELECT
                od.quantity,
                p.pizza_id AS pizzaId,
                p.name AS pizzaName,
                pv.price AS pizzaPrice,
                p.picture AS pizzaImage,
                ps.size AS pizzaSize,
                pc.crust AS pizzaCrust,
                od.variation_id
            FROM order_items od
            JOIN pizza_variations pv ON od.variation_id = pv.variation_id
            JOIN pizzas p ON pv.pizza_id = p.pizza_id
            JOIN pizza_sizes ps ON pv.size_id = ps.size_id
            JOIN pizza_crusts pc ON pv.crust_id = pc.crust_id
            WHERE od.order_id = ?";

        $stmt = $this->db->prepared($sql, "i", array($order_id));
        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        if (empty($rows)) {
            return [
                "status" => "error",
                "message" => "Failed to get cart"
            ];
        }

        // Get order information
        $orderInfo = $this->getOrderInformation($order_id);

        return [
            "status" => "success",
            "message" => "Cart retrieved",
            "items" => $rows,
            "order" => $orderInfo
        ];
    }

    private function getOrderInformation($order_id)
    {
        $sql = "SELECT * FROM orders WHERE order_id = ?";
        $stmt = $this->db->prepared($sql, "i", array($order_id));
        $result = $stmt->get_result();
        $order_row = $result->fetch_assoc();

        return $order_row;
    }




    public function pay($order_id)
    {

        // Check if the order ID is valid and the user has permission to pay
        if (!$this->checkPermission($order_id)) {
            return array(
                "status" => "error",
                "message" => "Permission denied to pay for this order."
            );
        }

        // Call Procedure
        $sql = "CALL Pay(?, ?)";
        $types = "ii";
        $params = array(
            $this->user_id,
            $order_id
        );

        // Prepare the SQL statement and execute it
        $stmt = $this->db->prepared($sql, $types, $params);
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['v_is_error'] == 0) {
            return array(
                "status" => "success",
                "message" => "Payment success"
            );
        } else {
            return array(
                "status" => "error",
                "message" => "Payment failed"
            );
        }
    }
}
