<?php
function getPizzaCard($pid, $picture, $name, $price)
{
    return '
        <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl duration-500 transform-gpu">
            <div>
                <!-- Detail -->
                <img src="' . $picture . '"
                    alt="Margherita Pizza" class="w-full h-48 object-contain rounded">
                <h3 class="text-md font-semibold mt-4 overflow-hidden h-12 text-center flex items-center w-full">' . $name . '</h3>
            </div>
                <!-- Btn Add -->
            <div class="w-full flex items-center justify-center mt-4">
                <a href="pizza.php?id=' . $pid . '"
                    class="w-full bg-green-500 text-center hover:bg-green-600 text-white py-2 px-6 rounded-full text-lg font-semibold transition duration-300 ease-in-out transform">
                    ฿' . $price . ' +
                </a>
            </div>
        </div>
    ';
}
