<?php
include_once 'config/session.php';


// user get name, phone and address
$name = $_SESSION['name'];
$phone = $_SESSION['phone'];
$address = $_SESSION['address'];

// order
include_once 'services/OrderManager.php';
$Order = new OrderManager();
$myOrder = $Order->GetOrder();
if ($myOrder['order']['user_id'] != $_SESSION['user_id']) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Pizza Paradise</title>
    <link rel="icon" href="https://menufyproduction.imgix.net/637849359719551935+751750.png" type="image/png">
    <!-- Include CSS StyleSheet -->
    <link rel="stylesheet" href="style.css">
    <!-- Include Tailwind CSS stylesheet -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <!-- Header Navbar -->
    <?php require_once(__DIR__ . '/components/NavBar.php'); ?>

    <!-- main -->
    <!-- Main Content -->
    <main class="w-full h-full pt-20 flex items-center justify-center">
        <!-- container -->
        <div class="w-full h-full container max-w-5xl px-4 flex flex-col items-center justify-start">
            <div class="w-full flex items-start gap-4 justify-start">
                <!-- Order Summary -->
                <section class="bg-white shadow-md rounded-md p-4 mb-6">
                    <h2 class="text-2xl font-semibold mb-4">Order Summary</h2>
                    <div class="border-t border-gray-300 mt-4 pt-2">
                        <div class="flex justify-between font-semibold">
                            <span> <?= $myOrder['count'] ?> Item</span>
                            <span>฿<?= $myOrder['order']['total_price'] ?></span>
                        </div>
                    </div>
                    <!-- Button to toggle the popup
                    <button id="togglePopup" disabled class="bg-indigo-600 text-white rounded-md px-4 py-2 mt-4 hover:bg-indigo-700 focus:outline-none focus:ring focus:ring-indigo-300">
                        View Full Order Summary
                    </button> -->
                </section>

                <form method="post" id="orderForm" class="w-full h-full flex flex-col items-center justify-start">
                    <div class="w-full flex flex-col items-center justify-start">
                        <!-- right side -->
                        <!-- Personal Data -->
                        <section class="w-full bg-white shadow-md rounded-md p-4 mb-4">
                            <h2 class="text-2xl font-semibold mb-4">Personal Data</h2>
                            <!-- Personal data input fields -->
                            <div class="mb-4">
                                <label class="block font-semibold mb-1" for="name">Name <span class="text-red-500">*</span></label>
                                <input type="text" id="name" name="name" class="w-full border rounded-md px-3 py-2" value="<?= $name ?>" required>
                            </div>
                            <div class="mb-4">
                                <label class="block font-semibold mb-1" for="phone">Phone <span class="text-red-500">*</span></label>
                                <input type="text" id="phone" name="phone" class="w-full border rounded-md px-3 py-2" maxlength="10" value="<?= $phone ?>" required>
                                <div class="mb-4">
                                    <label class="block font-semibold mb-1" for="address">Address <span class="text-red-500">*</span></label>
                                    <textarea id="address" name="address" class="w-full h-24 max-h-32 h-full border rounded-md px-3 py-2" required><?= $address ?></textarea>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block font-semibold mb-1 flex justify-start gap-2 select-none items-center">
                                    <input type="checkbox" id="saveData" name="saveData" class="form-checkbox h-5 w-5 text-indigo-600">
                                    Save personal data for next time
                                </label>
                            </div>
                        </section>

                        <!-- Payment Options -->
                        <section class="w-full bg-white shadow-md rounded-md p-4">
                            <h2 class="text-2xl font-semibold mb-4">Payment</h2>
                            <!-- Payment options (wallet and credit card) -->
                            <div class="mb-4">
                                <label class="block font-semibold mb-1">Payment Method:</label>
                                <div class="flex items-center space-x-4">
                                    <input type="radio" id="paymentWallet" name="paymentMethod" value="wallet" class="form-radio h-5 w-5 text-indigo-600" checked>
                                    <?php if ($GLOBALS['wallet'] >= $myOrder['order']['total_price']) { ?>
                                        <label for="paymentWallet">Pay with Wallet (<span class="text-green-600">wallet : ฿ <?= $GLOBALS['wallet'] ?></span>)</label>
                                    <?php } else { ?>
                                        <label for="paymentWallet">Pay with Wallet (<span class="text-red-600">wallet : ฿ <?= $GLOBALS['wallet'] ?></span>)</label>
                                    <?php } ?>
                                    <input type="radio" id="paymentLater" name="paymentMethod" value="later" class="form-radio h-5 w-5 text-indigo-600">
                                    <label for="paymentLater">Pay Later</label>
                                </div>
                            </div>
                        </section>

                        <!-- Submit Button -->
                        <div class="mt-6">
                            <input type="submit" value="Place Order" class="bg-indigo-600 text-white rounded-md cursor-pointer px-4 py-2 hover:bg-indigo-700 focus:outline-none focus:ring focus:ring-indigo-300 disabled:bg-gray-500 disabled:cursor-default" <?php if ($GLOBALS['wallet'] < $myOrder['order']['total_price']) {
                                                                                                                                                                                                                                                                    echo "disabled";
                                                                                                                                                                                                                                                                } ?>>
                            </input>
                        </div>
                    </div>
            </div>

            </form>
        </div>
    </main>

    <!-- Script -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // onchange paymentMethod
        document.querySelectorAll('input[name="paymentMethod"]').forEach(item => {
            item.addEventListener("change", function() {
                // get payment method
                const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value
                // if payment method is wallet
                if (paymentMethod == "wallet" && <?= $GLOBALS['wallet'] ?> < <?= $myOrder['order']['total_price'] ?>) {
                    // disable submit button
                    document.querySelector('input[type="submit"]').disabled = true
                } else {
                    // enable submit button
                    document.querySelector('input[type="submit"]').disabled = false
                }
            })
        })
    </script>
    <script>
        // form onSubmit confirm
        document.getElementById("orderForm").addEventListener("submit", function(e) {
            // prevent default
            e.preventDefault()

            // get data
            const formData = new FormData(this)

            // get payment method
            const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value

            // add payment method to formData
            formData.append("paymentMethod", paymentMethod)

            // get save data
            const saveData = document.getElementById("saveData").checked

            // add save data to formData
            formData.append("saveData", saveData)

            // name
            const name = document.getElementById("name").value

            // add name to formData
            formData.append("name", name)

            // phone
            const phone = document.getElementById("phone").value

            // add phone to formData
            formData.append("phone", phone)

            // address
            const address = document.getElementById("address").value

            // add address to formData
            formData.append("address", address)

            // send data
            fetch("services/Action.php?action=checkout", {
                method: "POST",
                body: formData
            }).then(res => res.json()).then(res => {
                Swal.fire({
                    icon: res.status,
                    title: "title",
                    text: res.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    // redirect to history
                    window.location.href = "history.php"
                })
            })
        })
    </script>
</body>

</html>