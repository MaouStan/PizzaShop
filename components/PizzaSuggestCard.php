<?php
function makeSuggest($pid, $img, $name, $price)
{
    return '
    <div class="w-full h-auto bg-white rounded-md shadow-xl cursor-pointer" id=\'suggest_'.$pid.'\' onclick="window.location.href=\'pizza.php?id='.$pid.'\'">
        <!-- Card Image -->
        <div class="w-full h-[15dvh] bg-gray-300 rounded-t-md overflow-hidden">
            <!-- Product Image -->
            <img src="'.$img.'"
                alt="Pizza" class="w-full h-full object-contain">
        </div>
        <!-- Card Info -->
        <div class="w-full h-auto bg-white rounded-b-md">
            <!-- Card Name -->
            <div class="w-full h-auto py-2 px-4">
                <h1 class="text-2xl font-bold text-green-600">'.$name.'</h1>
            </div>
            <!-- Card Price -->
            <div class="w-full h-auto py-2 px-4">
                <p class="text-xl font-bold text-green-600">$'.$price.'</p>
            </div>
        </div>
    </div>
    ';
}
