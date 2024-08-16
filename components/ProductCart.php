<?php
function ProductCart($pid, $imageURL, $name, $size, $type, $amount, $price, $total)
{
    echo <<<HTML
    <div class="flex items-center hover:bg-gray-100 -mx-8 px-6 py-5" id="variation_{$pid}">
        <div class="flex w-2/5">
            <div class="w-20">
                <img class="w-full h-16" src="{$imageURL}" alt="">
            </div>
            <div class="flex flex-col justify-between ml-4 flex-grow">
                <span class="font-bold text-sm">{$name}</span>
                <span class="text-red-500 text-xs">{$size} - {$type}</span>
                <a href="#" class="font-semibold hover:text-red-500 text-gray-500 text-xs remove">Remove</a>
            </div>
        </div>
        <div class="flex justify-center w-1/5">
            <button id="decrease" class="bg-gray-200 text-gray-600 hover:bg-gray-300 w-8 h-8 rounded-full flex items-center justify-center cursor-pointer">
                -
            </button>
            <input id="amount" class="mx-2 border text-center w-8" type="text" value='{$amount}' min='1' disabled>
            <button id="increase" class="bg-gray-200 text-gray-600 hover:bg-gray-300 w-8 h-8 rounded-full flex items-center justify-center cursor-pointer">
                +
            </button>
        </div>
        <span class="text-center w-1/5 font-semibold text-sm">฿{$price}</span>
        <span class="text-center w-1/5 font-semibold text-sm">฿{$total}</span>
    </div>
HTML;
}
