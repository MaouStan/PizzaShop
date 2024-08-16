<?php
include_once 'config/session.php';

// check cart item count
include_once 'services/OrderManager.php';
$order = new OrderManager();
$count = $order->GetCartCount()['data']['count'];

// get GetCart
$items = $order->GetCart()['items'];

// total
$total = 0;
$tax = 15;
foreach ($items as $item) {
    $total += $item['quantity'] * $item['pizzaPrice'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Pizza Paradise</title>
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
            <div class="relative w-full h-full flex shadow-md bg-white overflow-hidden md:mt-10 mb:mb-5">
                <div class="relative w-full md:w-3/4 bg-white px-10 my-10">
                    <div class="flex justify-between border-b pb-8">
                        <h1 class="font-semibold text-2xl">Shopping Cart</h1>
                        <h2 class="font-semibold text-2xl countItem"><?= $count ?> Items</h2>
                    </div>
                    <div class="flex mt-10 mb-5">
                        <h3 class="font-semibold text-gray-600 text-xs uppercase w-2/5">Product Details</h3>
                        <h3 class="font-semibold text-center text-gray-600 text-xs uppercase w-1/5">Quantity
                        </h3>
                        <h3 class="font-semibold text-center text-gray-600 text-xs uppercase w-1/5">Price
                        </h3>
                        <h3 class="font-semibold text-center text-gray-600 text-xs uppercase w-1/5">Total
                        </h3>
                    </div>
                    <!-- products -->
                    <div class="w-full h-full flex flex-col overflow-y-auto overflow-x-hidden md:pb-32" id="products">
                        <?php
                        // include ProductCart
                        include_once 'components/ProductCart.php';

                        // loop items to ProductCart($pid, $imageURL, $name, $size, $type, $amount, $price, $total)
                        foreach ($items as $item) {
                            ProductCart($item['variation_id'], $item['pizzaImage'], $item['pizzaName'], $item['pizzaSize'], $item['pizzaCrust'], $item['quantity'], $item['pizzaPrice'], $item['quantity'] * $item['pizzaPrice']);
                        }
                        ?>
                    </div>
                </div>

                <!-- summary MD -->
                <div class="summary hidden md:flex md:flex-col w-1/4 px-8 py-10">
                    <h1 class="font-semibold text-2xl">Order Summary</h1>
                    <div class="flex justify-between mt-10 mb-5">
                        <span class="font-semibold text-sm uppercase countItem">Items <?= $count ?></span>
                        <span class="font-semibold text-sm total">฿<?= $total ?></span>
                    </div>
                    <div>
                        <label class="font-medium inline-block mb-3 text-sm uppercase">Shipping</label>
                        <select class="block p-2 text-gray-600 w-full text-sm">
                            <option>Standard shipping - ฿0.00</option>
                        </select>
                    </div>
                    <!-- <div class="py-10">
                        <label for="promo" class="font-semibold inline-block mb-3 text-sm uppercase">Promo Code</label>
                        <input type="text" id="promo" placeholder="Enter your code"
                            class="p-2 text-white text-sm w-full bg-gray-800">
                    </div>
                    <button class="bg-red-500 hover:bg-red-600 px-5 py-2 text-sm text-white uppercase">Apply</button> -->
                    <div class="border-t mt-8">
                        <div class="flex font-semibold justify-between py-3 text-sm uppercase">
                            <span>Tax</span>
                            <span class="tax">฿ <?= $tax ?></span>
                        </div>
                        <div class="flex font-semibold justify-between py-6 text-sm uppercase">
                            <span>Total cost</span>
                            <span class="grandTotal">฿<?= $total ?></span>
                        </div>
                        <button id="checkoutBtn" class="bg-indigo-500 font-semibold hover:bg-indigo-600 py-3 text-sm text-white uppercase w-full">Checkout</button>
                    </div>
                </div>
            </div>
            <!-- summary less than MD -->
            <div class="summary md:hidden w-full px-8 py-10">
                <h1 class="font-semibold text-2xl border-b pb-8">Order Summary</h1>
                <div class="flex justify-between mt-10 mb-5">
                    <span class="font-semibold text-sm uppercase countItem">Items <?= $count ?></span>
                    <span class="font-semibold text-sm total">฿<?= $total ?></span>
                </div>
                <div>
                    <label class="font-medium inline-block mb-3 text-sm uppercase">Shipping</label>
                    <select class="block p-2 text-gray-600 w-full text-sm">
                        <option>Standard shipping - ฿0.00</option>
                    </select>
                </div>
                <div class="border-t mt-8">
                    <div class="flex font-semibold justify-between py-3 text-sm uppercase">
                        <span>Tax</span>
                        <span class="tax">฿ <?= $tax ?></span>
                    </div>
                    <div class="flex font-semibold justify-between py-6 text-sm uppercase">
                        <span>Total cost</span>
                        <span class="grandTotal">฿<?= $total ?></span>
                    </div>
                    <button class="bg-indigo-500 font-semibold hover:bg-indigo-600 py-3 text-sm text-white uppercase w-full">Checkout</button>
                </div>
            </div>
        </div>
    </main>

    <!-- Script -->
    <script>
        // global getData
        // getData
        async function getData() {
            const formData = new FormData()
            formData.append("order_id", <?= $_SESSION["order_id"] ?>);
            const resp = await fetch("services/Action.php?action=getCart", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(resp => {
                    return resp
                })
            return resp
        }

        // Amount On Change
        function updateAmount(input) {
            // get amount
            const amount = parseInt(input.value)

            // calc
            const total = amount * parseFloat(input.parentElement.nextElementSibling.innerHTML.replace("฿", "")).toFixed(0);
            input.parentElement.nextElementSibling.nextElementSibling.innerHTML = `฿${total.toFixed(0)}`;

            // update to data
            const pid = input.parentElement.parentElement.id.replace("variation_", "");
            data.forEach((productData) => {
                if (productData.variation_id == pid) {
                    productData.quantity = amount
                }
            })

            CalcOrder();

            // UpdateItemQuantity
            const formData = new FormData()
            formData.append("variation_id", pid)
            formData.append("user_id", <?= $_SESSION["user_id"] ?>);
            formData.append("order_id", <?= $_SESSION["order_id"] ?>);
            formData.append("quantity", amount);

            fetch("services/Action.php?action=updateItemQuantity", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(resp => {
                    if (resp.status == 'error') {
                        Swal.fire({
                            icon: resp.status,
                            title: resp.message,
                            showConfirmButton: false,
                            timer: 1500
                        })
                    }
                })
        }

        // Event Remove
        function eventRemove() {
            const remove = document.querySelectorAll(".remove")
            remove.forEach((button) => {
                button.addEventListener("click", () => {
                    const removeFromCart = button.parentElement.parentElement.parentElement.id.replace("variation_", "")
                    const formData = new FormData()
                    formData.append("variation_id", removeFromCart)
                    formData.append("user_id", <?= $_SESSION["user_id"] ?>);
                    formData.append("order_id", <?= $_SESSION["order_id"] ?>);
                    // swl confirm
                    Swal.fire({
                            title: 'Are you sure?',
                            text: "You won't be able to revert this!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#10B981',
                            cancelButtonColor: '#EF4444',
                            confirmButtonText: 'Yes, remove it!'
                        })
                        .then((result) => {
                            if (result.isConfirmed) {
                                fetch("services/Action.php?action=removeItemFromCart", {
                                        method: "POST",
                                        body: formData
                                    })
                                    .then(response => response.json())
                                    .then(resp => {
                                        // SWL 2 Echo Status
                                        Swal.fire({
                                            icon: resp.status,
                                            title: resp.message,
                                            showConfirmButton: false,
                                            timer: 1500
                                        }).then(() => {
                                            // if status is success
                                            if (resp.status == "success") {
                                                // reload
                                                location.reload();
                                            }
                                        })
                                    })
                            }
                        })
                })
            })
        }
    </script>
    <script src="js/cart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>