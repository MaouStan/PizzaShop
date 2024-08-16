<?php
$db = null;
if (isset($GLOBALS['db'])) {
    $db = $GLOBALS['db'];
} else {
    require_once __DIR__ . "/../config/db.php";
    $db = new Database();
}
class AdminManager
{
    private $db;
    public $is_admin;

    public function __construct()
    {
        global $db;
        $this->db = $db;

        $this->is_admin = false;

        if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
            $this->is_admin = true;
        }
    }

    public function GetOrders($status, $time)
    {
        // Prepare the SQL statement with placeholders
        $sql = "SELECT orders.*, users.name,
                (SELECT JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'pizzaId', od.variation_id,
                        'pizzaName', p.name,
                        'pizzaPrice', pv.price,
                        'pizzaImage', p.picture,
                        'pizzaSize', ps.size,
                        'pizzaCrust', pc.crust,
                        'quantity', od.quantity
                    )
                ) FROM order_items od
                JOIN pizza_variations pv ON od.variation_id = pv.variation_id
                JOIN pizzas p ON pv.pizza_id = p.pizza_id
                JOIN pizza_sizes ps ON pv.size_id = ps.size_id
                JOIN pizza_crusts pc ON pv.crust_id = pc.crust_id
                WHERE od.order_id = orders.order_id) AS items
            FROM orders
            INNER JOIN users ON orders.user_id = users.user_id
            WHERE users.role = 'customer'";

        if ($status != 'all') {
            $sql .= " AND orders.order_status = ?";
        } else {
            $sql .= " AND orders.order_status not in ('0', '1')";
        }

        $sql .= " ORDER BY orders.order_time ";
        $sql .= ($time == 'DESC') ? "DESC" : "ASC";

        // Bind parameters and execute the query
        if ($status == 'all') {
            $stmt = $this->db->prePareNoBind($sql);
        } else {
            $stmt = $this->db->prepared($sql, "s", array($status));
        }

        $result = $stmt->get_result();

        $orders = array();

        while ($row = $result->fetch_assoc()) {
            $orderItems = json_decode($row['items'], true);
            $row['items'] = $orderItems;
            $orders[] = $row;
        }


        return ["orders" => $orders];
    }

    public function saveStatus($id, $status)
    {
        $sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
        $stmt = $this->db->prepared($sql, "ss", array($status, $id));
        return ($stmt->affected_rows > 0) ? [
            "status" => "success",
            "message" => "Order status updated"
        ] : [
            "status" => "error",
            "message" => "Failed to update order status"
        ];
    }
}
