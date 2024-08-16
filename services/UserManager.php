<?php
// create class
class UserManager
{
    private $db;
    private $order_id;
    private $user_id;

    public function __construct()
    {

        if (isset($GLOBALS['db'])) {
            $db = $GLOBALS['db'];
        } else {
            require_once __DIR__ . "/../config/db.php";
            $db = new Database();
        }

        $this->db = $db;

        if (isset($_SESSION['user_id'])) {
            $this->user_id = $_SESSION['user_id'];
        }

        if (isset($_SESSION['order_id'])) {
            $this->order_id = $_SESSION['order_id'];
        }
    }

    public function login($email, $pass)
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->prepared($sql, "s", array($email));
        $result = $stmt->get_result();

        // if num rows
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // if password verify
            if (password_verify($pass, $user['password'])) {
                // set session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['phone'] = $user['phone'];
                $_SESSION['address'] = $user['address'];
                $_SESSION['role'] = $user['role'];

                // get active order from service order
                require_once __DIR__ . '/OrderManager.php';
                $orderManager = new OrderManager($this->db);
                $UserActiveOrder = $orderManager->userActiveOrder();
                if ($UserActiveOrder['status'] == 'success' && $user['role'] == "customer") {
                    $_SESSION['order_id'] = $UserActiveOrder['order_id'];
                } else {
                    $_SESSION['order_id'] = -1;
                }

                return array(
                    "status" => "success",
                    "message" => "Login success",
                    "data" => array(
                        "role" => $user['role']
                    )
                );
            } else {
                return array(
                    "status" => "error",
                    "message" => "Wrong Password"
                );
            }
        } else {
            return array(
                "status" => "error",
                "message" => "Not Found User"
            );
        }
    }

    public function GetWallet()
    {
        $sql = "SELECT * FROM users WHERE user_id = ?";
        $stmt = $this->db->prepared($sql, "i", array($this->user_id));
        $result = $stmt->get_result();
        // check has email
        if ($result->num_rows == 0) {
            return array(
                "status" => "error",
                "message" => "User not found"
            );
        }

        $row = $result->fetch_assoc();
        return array(
            "status" => "success",
            "message" => "Get wallet success",
            "data" => array(
                "wallet" => $row['wallet']
            )
        );
    }
}
