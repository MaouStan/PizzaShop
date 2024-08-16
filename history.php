<?php
include_once 'config/session.php';

// get items
include_once 'services/OrderManager.php';
$order = new OrderManager();
$items = $order->GetOrderHistoryOrder("all", "DESC")['orders'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Order - Pizza Paradise</title>
    <link rel="icon" href="https://menufyproduction.imgix.net/637849359719551935+751750.png" type="image/png">
    <!-- Include CSS StyleSheet -->
    <link rel="stylesheet" href="style.css">
    <!-- Include Tailwind CSS stylesheet -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <!-- Header Navbar -->
    <?php require_once(__DIR__ . '/components/NavBar.php'); ?>

    <!-- Main Content -->
    <main class="relative w-full min-h-screen md:h-screen md:max-h-screen md:pt-12 pt-20 pb-5 flex items-center justify-center">
        <!-- container -->
        <div class="relative w-full h-full container max-w-5xl px-4 flex flex-col items-center justify-start">

            <div class="relative w-full h-full flex flex-col shadow-md bg-white overflow-y-auto md:mt-10 mb:mb-5 px-8 py-4">
                <div class="relative w-full h-16 flex items-center justify-between px-4 border-b">
                    <div class="relative w-1/2 h-full flex items-center justify-start">
                        <select id='status' class="relative w-32 h-12 text-center flex text-green-600 items-center justify-center bg-white rounded-md shadow-md font-bold text-lg">
                            <option value="all" class="text-black" selected>All</option>
                            <option value="1" class="text-orange-500">No Pay</option>
                            <option value="2" class="text-red-500">Delivering</option>
                            <option value="3" class="text-green-500">Delivered</option>
                        </select>
                        <select id='time' class="relative w-32 h-12 text-center flex text-green-600 items-center justify-center bg-white rounded-md shadow-md font-bold text-lg ml-4">
                            <option value="ASC" class="text-green-500">Time Asc</option>
                            <option value="DESC" class="text-red-500" selected>Time Desc</option>
                        </select>
                    </div>
                </div>
                <div class="relative w-full h-full flex flex-col bg-white overflow-y-auto md:mt-10 mb:mb-5 px-8 py-4" id="orderList">

                    <!-- order list table-->
                    <div class="relative w-full h-full flex flex-col items-center justify-start">
                        <!-- order item -->
                        <?php
                        // include SimpleProduct
                        include_once 'components/HistoryOrder.php';

                        // loop items
                        foreach ($items as $item) {
                            // function HistoryOrder($id, $timeStamp, $total, $status)
                            HistoryOrder($item['order_id'], $item['order_time'], $item['total_price'], $item['order_status']);
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Script -->
    <script>
        // get data
        async function getData() {
            // formData
            const status = document.getElementById("status").value;
            const time = document.getElementById("time").value;
            const url = `services/Action.php?action=getOrders&status=${status}&time=${time}`;
            const resp = fetch(url, {
                method: "GET"
            }).then(res => res.json());

            return resp
        }
    </script>
    <script src="js/filterOrder.js"></script>
</body>

</html>