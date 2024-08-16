<?php
function AdminProductOrder($id, $user_id, $user_name, $timeStamp, $total, $status)
{
    echo '
    <div class="flex-none w-full h-80 bg-gray-100 flex items-center justify-center border-4 overflow-hidden">
        <div class="w-64 h-full text-xs space-y-2 bg-gray-100 flex flex-col items-start justify-start p-4 border-r-4">
            <p class="font-bold">Order ID : <span class="font-medium">#' . $id . '</span></p>
            <p class="font-bold">Name : <span class="font-medium">' . $user_name . '</span></p>
            <p class="font-bold">Total : <span class="font-medium">฿' . $total . '</span></p>
            <p class="font-bold">Time : <span class="font-medium">' . $timeStamp . '</span></p>
            <label for="status" class="font-bold">Status:
                <select class="font-medium" id="status" name="status">
                    <option value="2"' . ($status == 2 ? ' selected' : '') . '>No Delivery</option>
                    <option value="3"' . ($status == 3 ? ' selected' : '') . '>Delivered</option>
                </select>
            </label>
            <button id="Save_' . $id . '" class="w-full mx-auto rounded-md text-white text-center bg-green-400 p-2 hover:bg-green-300">
                Save Change
            </button>
            <button class="w-full mx-auto rounded-md text-white text-center bg-yellow-400 p-2 hover:bg-green-300"
            onclick="window.location.href=\'order.php?id=' . $id . '\'"
            >
                See More
            </button>
        </div>

        <!-- scroll x-axis able side -->
        <div class="min-w-0 w-full max-w-full h-full overflow-x-auto flex">
            ';
    // Generate multiple card components
    global $order;
    $orderDetails = $order->GetOrderDetails($user_id, $id);
    foreach ($orderDetails['items'] as $orderDetail) {
        $pizza_name = $orderDetail['pizzaName'];
        $pizza_img = $orderDetail['pizzaImage'];
        $pizza_size = $orderDetail['pizzaSize'];
        $pizza_crust = $orderDetail['pizzaCrust'];
        $pizza_amount = $orderDetail['quantity'];
        generateCard($pizza_name, $pizza_img, $pizza_size, $pizza_crust, $pizza_amount);
    }

    echo '
        </div>
    </div>';
}

function AdminProductOrder2(array $items)
{
    global $order;

    foreach ($items as $item) {
        $id = $item['order_id'];
        $timeStamp = $item['order_time'];
        $total = $item['total_price'];
        $status = $item['order_status'];

        // Get UserData
        $user_name = $item['name'];

        // Create HTML content in a variable
        echo '
    <div class="flex-none w-full h-80 bg-gray-100 flex items-center justify-center border-4 overflow-hidden">
        <div class="w-64 h-full text-xs space-y-2 bg-gray-100 flex flex-col items-start justify-start p-4 border-r-4">
            <p class="font-bold">Order ID : <span class="font-medium">#' . $id . '</span></p>
            <p class="font-bold">Name : <span class="font-medium">' . $user_name . '</span></p>
            <p class="font-bold">Total : <span class="font-medium">฿' . $total . '</span></p>
            <p class="font-bold">Time : <span class="font-medium">' . $timeStamp . '</span></p>
            <label for="status" class="font-bold">Status:
                <select class="font-medium" id="status" name="status">
                    <option value="2"' . ($status == 2 ? ' selected' : '') . '>No Delivery</option>
                    <option value="3"' . ($status == 3 ? ' selected' : '') . '>Delivered</option>
                </select>
            </label>
            <button disabled id="Save_' . $id . '" class="w-full mx-auto disabled:bg-gray-400 rounded-md text-white text-center bg-green-400 p-2 hover:bg-green-300">
                Save Change
            </button>
            <button class="w-full mx-auto rounded-md text-white text-center bg-yellow-400 p-2 hover:bg-green-300"
            onclick="window.location.href=\'order.php?id=' . $id . '\'"
            >
                See More
            </button>
        </div>

        <!-- scroll x-axis able side -->
        <div class="min-w-0 w-full max-w-full h-full overflow-x-auto flex">
            ';

        // Generate multiple card components
        foreach($item['items'] as $detail){
            generateCard($detail);
        }

        echo '
        </div>
    </div>';
    }
}

// Function to generate a card component
function generateCard($detail)
{
    $pizza_name = $detail['pizzaName'];
    $pizza_img = $detail['pizzaImage'];
    $pizza_size = $detail['pizzaSize'];
    $pizza_crust = $detail['pizzaCrust'];
    $pizza_amount = $detail['quantity'];

    echo '
    <div class="flex-none min-w-52 max-w-52 h-auto min-h-full bg-green-100 p-4 overflow-hidden border-2">
        <div class="flex flex-col justify-between space-y-2">
            <div class="w-full h-2/3">
                <img class="w-48 h-48 object-contain" src="' . $pizza_img . '" alt="' . $pizza_name . '">
            </div>
            <div class="w-full min-h-1/3 text-xs h-full flex flex-col items-center space-y-2">
                <p class="font-bold w-48 break-all line-clamp-2">' . $pizza_name . '</p>
                <div class="w-full h-8 flex items-center justify-center bg-green-400 rounded-md text-white text-center">
                    <p class="font-bold border-r px-2">' . $pizza_size . '</p>
                    <p class="font-bold border-r px-2">' . $pizza_crust . '</p>
                    <p class="font-bold px-2">x' . $pizza_amount . '</p>
                </div>
            </div>
        </div>
    </div>';
}
