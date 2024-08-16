<?php
function HistoryOrderName($id, $user_id, $timeStamp, $total, $status)
{
    echo '
    <!-- Sample Order Item -->
    <div class="w-full border-b border-gray-200 p-4 flex items-start">
        <!-- Order Details -->
        <div class="w-3/4 p-4">
            <h2 class="text-xl font-semibold">Order #' . $id . '</h2>
            <p class="text-gray-600">User ID: ' . $user_id . '</p>
            <p class="text-gray-600">Date: ' . $timeStamp . '</p>
            <p class="text-gray-600">Status:
                <span>
                    <select class=\' text-red-500 bg-gray-100 rounded-md text-center \'>
                        <option ' . ($status == 1 ? 'selected' : '') . ' value=\'1\'>No Pay
                        </option>
                        <option ' . ($status == 2 ? 'selected' : '') . ' value=\'2\'>Delivering
                        </option>
                        <option ' . ($status == 3 ? 'selected' : '') . ' value=\'3\'>Delivered
                        </option>
                    </select>
                </span>
            </p>
            <p class="text-gray-600">Total: $' . $total . '</p>
        </div>
        <!-- Button to See More Details -->
        <div class="relative w-1/3 h-full flex items-center gap-12 justify-end">
            <!-- see detail button -->
            <button
                class="relative w-32 h-12 flex items-center justify-center bg-yellow-600 hover:bg-yellow-500 rounded-md shadow-md text-white font-bold text-lg"
                id=\'Save_' . $id . '\'">
                Save
            </button>
            <button
                class="relative w-32 h-12 flex items-center justify-center bg-green-600 hover:bg-green-500 rounded-md shadow-md text-white font-bold text-lg"
                onclick="window.location.href=\'order.php?id=' . $id . '\'">
                See Detail
            </button>
        </div>
    </div>
    ';
}
