<?php
// session
include_once 'config/session.php';

// check get id
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}



// Order
require_once 'services/OrderManager.php';
$OrderM = new OrderManager();

// get orderItems
$orderItems = $OrderM->GetOrderDetails($_GET['id']);

// if orderItems error
if ($orderItems['status'] == 'error') {
    header("Location: index.php");
    exit();
}

// items
$items = $orderItems['items'];
$order = $orderItems['order'];

// tax
$tax = 0;
$taxValue = $order['total_price'] * ($tax / 100);

// status
$statusColor = "";
$statusText = "";
switch ($order['order_status']) {
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
        $statusText = "No Delivery";
        break;
    case 3:
        $statusColor = "text-green-500";
        $statusText = "Delivered";
        break;
    default:
        $statusColor = "text-gray-500";
        break;
}

// print_r($order);

// echo "<br>";


// print_r($items);


// echo "<br>";
// echo $order['order_id'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Order</title>
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
    <main class="py-32 flex items-center justify-center md:h-screen">
        <!-- container -->
        <div class="relative w-full h-full container max-w-5xl px-4 flex items-center justify-start gap-4">
            <!-- Detail date, status -->
            <div class="w-2/4 h-full flex flex-col items-center justify-start gap-4">
                <div class="relative w-full h-fit bg-white p-12 mb-5">
                    <div class="flex justify-between border-b pb-8 mb-8">
                        <h1 class="font-semibold text-2xl">Order #<?= $order['order_id'] ?></h1>
                    </div>
                    <div class="flex justify-between">
                        <h3 class="font-medium text-gray-600 text-md uppercase">Date</h3>
                        <h3 class="font-medium text-center text-gray-600 text-md uppercase"><?= $order['order_time'] ?></h3>
                    </div>
                    <div class="flex justify-between">
                        <h3 class="font-medium text-gray-600 text-md uppercase">Status</h3>
                        <h3 class="font-medium text-center text-md uppercase <?= $statusColor ?>"><?= $statusText ?></h3>
                    </div>
                    <div class="flex justify-between">
                        <h3 class="font-medium text-gray-600 text-md uppercase">receiver_name</h3>
                        <h3 class="font-medium text-center select-none text-md text-gray-600"><?= $order['receiver_name'] ?></h3>
                    </div>
                    <div class="flex justify-between">
                        <h3 class="font-medium text-gray-600 text-md uppercase">receiver_phone</h3>
                        <h3 class="font-medium text-center select-none text-md text-gray-600"><?= $order['receiver_phone'] ?></h3>
                    </div>
                    <div class="flex justify-between pb-2">
                        <h3 class="font-medium text-gray-600 text-md uppercase">receiver_address</h3>
                        <h3 class="font-medium text-center select-none text-md underline cursor-pointer text-blue-500">See More</h3>
                    </div>
                    <!-- if status is 1 button goto pay at 'checkout.php?id=' -->
                    <?php if ($order['order_status'] == 1 && $_SESSION['order_id'] != -1) { ?>
                        <div class="flex justify-between">
                            <button id='payBtn' class="relative w-full h-12 flex items-center justify-center bg-green-600 hover:bg-green-500 rounded-md shadow-md text-white font-bold text-lg disabled:bg-gray-500" <?php if ($GLOBALS['wallet'] < ($order['total_price'] + $taxValue)) {
                                                                                                                                                                                                                            echo " disabled";
                                                                                                                                                                                                                        } ?>>
                                Pay
                            </button>
                        </div>
                    <?php } ?>
                </div>
                <!-- Summary -->
                <div class="relative w-full bg-white p-10">
                    <div class="flex justify-between border-b pb-8 mb-8">
                        <h1 class="font-semibold text-2xl">Order Summary</h1>
                    </div>
                    <div class="flex justify-between">
                        <h3 class="font-semibold text-gray-600 text-md uppercase">SubTotal</h3>
                        <h3 class="font-semibold text-center text-gray-600 text-md uppercase">฿<?= $order['total_price'] ?></h3>
                    </div>
                    <div class="flex justify-between">
                        <h3 class="font-semibold text-gray-600 text-md uppercase">Tax <?= $tax ?>%</h3>
                        <h3 class="font-semibold text-center text-gray-600 text-md uppercase">฿<?= $taxValue ?></h3>
                    </div>

                    <div class="flex justify-between border-t pt-8 mt-8">
                        <h3 class="font-semibold text-gray-600 text-md uppercase">Total</h3>
                        <h3 class="font-semibold text-center text-green-700 text-md uppercase">฿<?= $order['total_price'] + $taxValue ?></h3>
                    </div>
                </div>
            </div>
            <div class="w-3/4 h-full flex shadow-md bg-white overflow-hidden">
                <div class="relative w-full bg-white px-10 my-10">
                    <div class="flex justify-between border-b pb-8">
                        <h1 class="font-semibold text-2xl">Order Items</h1>
                        <h2 class="font-semibold text-2xl"><?= count($items) ?> Items</h2>
                    </div>
                    <div class="flex mt-10 mb-5">
                        <h3 class="font-semibold text-gray-600 text-sm uppercase w-2/5">Product Details</h3>
                        <h3 class="font-semibold text-center text-gray-600 text-sm uppercase w-1/5">Quantity
                        </h3>
                        <h3 class="font-semibold text-center text-gray-600 text-sm uppercase w-1/5">Price
                        </h3>
                        <h3 class="font-semibold text-center text-gray-600 text-sm uppercase w-1/5">Total
                        </h3>
                    </div>
                    <!-- products -->
                    <div class="w-full h-full flex flex-col overflow-y-auto overflow-x-hidden pb-32" id="products">
                        <?php
                        // include ProductCart
                        include_once 'components/ProductHistory.php';

                        // loop items to ProductHistory($pid, $imageURL, $name, $size, $type, $amount, $price, $total)
                        foreach ($items as $item) {
                            ProductHistory($item['variation_id'], $item['pizzaImage'], $item['pizzaName'], $item['pizzaSize'], $item['pizzaCrust'], $item['quantity'], $item['pizzaPrice'], $item['quantity'] * $item['pizzaPrice']);
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // pay
        const payBtn = document.querySelector('#payBtn');

        if (payBtn == null) {

        } else {


            // formData
            const formData = new FormData()
            formData.append("order_id", <?= $_GET['id'] ?>)

            // swl confirm
            payBtn.addEventListener('click', () => {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#48BB78',
                    cancelButtonColor: '#9CA3AF',
                    confirmButtonText: 'Yes, pay it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // loading
                        Swal.fire({
                            title: 'Loading...',
                            text: 'Please wait',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            willOpen: () => {
                                // fetch
                                fetch("services/Action.php?action=pay", {
                                    method: "POST",
                                    body: formData
                                }).then(res => res.json()).then(res => {
                                    // close loading
                                    Swal.close();
                                    // swl
                                    Swal.fire({
                                        icon: res.status,
                                        title: "title",
                                        text: res.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(() => {
                                        // reload
                                        location.reload();
                                    })
                                })
                                Swal.showLoading()
                            },
                        })
                    }
                })
            })
        }
    </script>

    <script>
        const seeMoreBtn = document.querySelector('.underline');
        const address = '<?= $order['receiver_address'] ?>';
        // swl
        seeMoreBtn.addEventListener('click', () => {
            Swal.fire({
                title: 'Address',
                text: address,
                icon: 'info',
                confirmButtonText: 'OK'
            })
        })
    </script>
</body>

</html>