<?php
$name = $_SESSION['name'];
$email = $_SESSION['email'];

// get wallet
include_once 'services/UserManager.php';
$user = new UserManager();

include_once 'services/OrderManager.php';
$orderM = new OrderManager();

// check is admin.php
$admin = $_SESSION['order_id'] == -1;

if (!$admin) {
    $wallet = $user->GetWallet()['data']['wallet'];
    // set session
    $_SESSION['wallet'] = $wallet;
    // format wallet
    $wallet_display = $wallet > 9999 ? "9999+" : $wallet;

    // GetItemCount In Cart
    $count = $orderM->getCartCount()['data']['count'];
}

echo '
<header class="fixed top-0 left-0 z-50 w-full h-16 py-2 bg-white shadow-xl">
    <div class="mx-auto w-full h-full flex items-center justify-space-between container max-w-5xl px-4">
        <!-- logo -->
        <div class="cursor-pointer select-none w-full h-full flex items-center justify-start"
            onclick="window.location = \'index.php\'">
            <!-- img -->
            <img src="https://menufyproduction.imgix.net/637849359719551935+751750.png" alt="Maou\'s Pizza Shop Logo"
                class="w-12 h-12 mr-2">
            <!-- text -->
            <h1 class="text-md lg:text-3xl font-bold text-green-600">
                Pizza <span class="text-red-500 font-bold">Paradise</span>
            </h1>
        </div>
        <!-- user img and name, shopping cart, and settings cog settings cog dropdown "Home", "Profile", "Bill", "Logout" -->
        <div class="w-full h-full flex items-center justify-end space-x-4">
            <!-- user img and name -->
            <div class="w-fit h-full flex items-center justify-center">
                <!-- text -->
                <div id="NameNav"
                    class="text-md lg:text-xl text-green-700 font-bold flex items-center justify-start overflow-hidden whitespace-nowrap">
                    ' . strtoupper($name) . ' (' . $email . ')
                </div>
            </div>';
if (!$admin) {
    echo '
            <!-- shopping cart -->
            <div id="carticon"
                class="relative select-none cursor-pointer w-fit h-full flex items-center justify-center"
                onclick="window.location.href = \'cart.php\'"
                >
                <!-- font awesome icon cart -->
                <img class="cursor-pointer" width="48" height="48" src="assets/cart.png" alt="shopping-cart--v1" />
                <p id="cart_number" class="absolute -top-1 -right-1 rounded px-2 py-1 border shadow
                        bg-green-500 text-white text-xs rounded-full">' . $count . '</p>
            </div>';
}
echo '
            <!-- settings cog -->
            <div class="relative w-fit h-full flex items-center justify-center">
                <!-- font awesome icon cog -->
                <img id="dropdown-gear-btn" class="cursor-pointer" width="48" height="48" src="assets/gear.png"
                    alt="settings" />
                <!-- settings cog dropdown -->
                <div id="dropdown-gear"
                    class="absolute top-14 right-0 border-2 w-48 h-48 bg-white rounded-md shadow-xl overflow-hidden z-10 hidden">
                    ';
if (!$admin) {
    echo '
                    <!-- dropdown item -->
                    <div class="w-full h-1/4 flex items-center justify-center text-green-700">
                        <p class="text-md font-bold">Wallet :<span class="text-green-400"> ฿' . $wallet_display . '</span></p>
                    </div>
                    <!-- dropdown item -->
                    <div
                        class="w-full h-1/4 flex items-center justify-center text-green-600 hover:text-white hover:bg-green-500">
                        <a href="index.php" class="w-full h-full flex items-center justify-center">
                            <p class="text-md font-bold">Home</p>
                        </a>
                    </div>';
}

if (!$admin) {
    echo '
                    <!-- dropdown item -->
                    <div
                        class="w-full h-1/4 flex items-center justify-center text-green-600 hover:text-white hover:bg-green-500">
                        <a href="history.php" class="w-full h-full flex items-center justify-center">
                            <p class="text-md font-bold">History</p>
                        </a>
                    </div>
                    ';
}
if ($admin) {
    echo '
                    <!-- dropdown item -->
                    <div
                        class="w-full h-1/4 flex items-center justify-center text-green-600 hover:text-white hover:bg-green-500">
                        <a href="admin.php" class="w-full h-full flex items-center justify-center">
                            <p class="text-md font-bold">Admin</p>
                        </a>
                    </div>
                    ';
}
echo '
                    <!-- dropdown item -->
                    <div
                        class="w-full h-1/4 flex items-center justify-center text-green-600 hover:text-white hover:bg-green-500">
                        <a href="login.php" class="w-full h-full flex items-center justify-center">
                            <p class="text-md font-bold">Logout</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
';

// add js
echo '
<script>
function dropdownGear() {
    if (document.getElementById("dropdown-gear").classList.contains("hidden")) {
        document.getElementById("dropdown-gear").classList.remove("hidden");
    }
    else {
        document.getElementById("dropdown-gear").classList.add("hidden");
    }
}

document.getElementById("dropdown-gear-btn").addEventListener("click", dropdownGear);
</script>
';
