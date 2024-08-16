<?php
// create class
class PizzaManager
{
    private $db;
    public function __construct()
    {

        if (isset($GLOBALS['db'])) {
            $db = $GLOBALS['db'];
        } else {
            require_once __DIR__ . "/../config/db.php";
            $db = new Database();
        }

        $this->db = $db;
    }

    // Retrieves all pizzas from the database along with the total number of pizzas sold for each pizza.
    public function GetPizzas()
    {
        $sql = "SELECT * FROM pizzas";
        $result = $this->db->read($sql);

        // Call the stored procedure MostSoldPizza to get the result
        $sql = "CALL MostSoldPizza()";
        $result2 = $this->db->read($sql);

        // Fetch the results from mysqli_result into arrays
        $result = $this->db->fetch_array($result);
        $result2 = $this->db->fetch_array($result2);

        // Create an associative array from $result2 with pizza_id as the key
        $totalSoldMap = array();
        foreach ($result2 as $row) {
            $totalSoldMap[$row['pizza_id']] = $row['total_sold'];
        }

        // Combine the results
        $result3 = array();
        foreach ($result as $row) {
            $pizzaId = $row['pizza_id'];
            if (isset($totalSoldMap[$pizzaId])) {
                $row['total_sold'] = $totalSoldMap[$pizzaId];
            } else {
                // set 0
                $row['total_sold'] = "0";
            }
            $result3[] = $row;
        }

        return $result3;
    }

    // Retrieves a specific pizza and its variations from the database based on the provided pizza ID.
    public function GetPizza(int $pizza_id)
    {
        $pizza = $this->getPizzaDetails($pizza_id);
        $pizza['pizza_variations'] = $this->getPizzaVariations($pizza_id);
        return $pizza;
    }

    // Retrieves the details of a specific pizza from the database based on the provided pizza ID.
    private function getPizzaDetails(int $pizza_id)
    {
        $sql = "SELECT * FROM pizzas WHERE pizza_id = ?";
        $stmt = $this->db->prepared($sql, "i", [$pizza_id]);
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }


    // Retrieves the variations of a specific pizza from the database based on the provided pizza ID.
    private function getPizzaVariations(int $pizza_id)
    {
        $sql = "SELECT * FROM pizza_variations WHERE pizza_id = ?";
        $stmt = $this->db->prepared($sql, "i", [$pizza_id]);
        $pizza_variations = $stmt->get_result();

        $variations = [];
        while ($pizza_variation = $pizza_variations->fetch_assoc()) {
            $variations[] = $pizza_variation;
        }

        return $variations;
    }
}
