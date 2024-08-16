<?php

require_once "config/db.php";

$db = new Database();

// Define variables to hold your values
$param1 = 3;
$param2 = 24;
$param3 = 1;
$param4 = "1";
$param5 = "2";
$param6 = "3";

$sql = "CALL add_item_into_cart(?, ?, ?)";
$stmt = $db->conn->prepare($sql);
$stmt->bind_param("iii", $param1, $param2, $param3);
$stmt->execute();

if ($stmt->errno) {
  echo "Error: " . $stmt->error;
} else {
  $result = $stmt->get_result();
  if ($result) {
    $row = $result->fetch_assoc();
    print_r($row);
  } else {
    echo "Error: " . $db->conn->error;
  }
}

?>