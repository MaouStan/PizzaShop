<?php
function HistoryOrder($id, $timeStamp, $total, $status)
{
    $statusColor = "";
    $statusText = "";
    switch ($status) {
        case 0:
            $statusColor = "text-yellow-500";
            $statusText = "In Cart";
            break;
        case 1:
            $statusColor = "text-orange-500";
            $statusText = "No Pay";
            break;
        case 2:
            $statusColor = "text-red-500";
            $statusText = "Delivering";
            break;
        case 3:
            $statusColor = "text-green-500";
            $statusText = "Delivered";
            break;
        default:
            $statusColor = "text-gray-500";
            break;
    }
    echo '
    <!-- Sample Order Item -->
    <div class="w-full border-b border-gray-200 p-4 flex items-start">
        <!-- Order Details -->
        <div class="w-3/4 p-4">
            <h2 class="text-xl font-semibold">Order #' . $id . '</h2>
            <p class="text-gray-600">Date: ' . $timeStamp . '</p>
            <p class="text-gray-600">Status: <span class="' . $statusColor . '">' . $statusText . '</span></p>
            <p class="text-gray-600">Total: $' . $total . '</p>
        </div>
        <!-- Button to See More Details -->
        <div class="relative w-1/3 h-full flex items-center justify-end">
            <!-- see detail button -->
            <button
                class="relative w-32 h-12 flex items-center justify-center bg-green-600 hover:bg-green-500 rounded-md shadow-md text-white font-bold text-lg"
                onclick="window.location.href=\'order.php?id=' . $id . '\'">
                ' . ($status == 1 ? "Pay" : "See Detail") . '
            </button>
        </div>
    </div>
    ';
}
