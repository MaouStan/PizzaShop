<?php
include_once 'config/session.php';

// get items
include_once 'services/AdminManager.php';
$admin = new AdminManager();
// admin check
if ($_SESSION['order_id'] != -1) {
    header("Location: index.php");
    exit();
}
$items = $admin->GetOrders("all", "DESC")['orders'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Order - Pizza Paradise</title>
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

            <div class="w-full h-full flex flex-col shadow-md bg-white overflow-y-auto md:mt-10 mb:mb-5 px-8 py-4">
                <div class="w-full h-16 flex items-center justify-between px-4 border-b">
                    <div class="relative w-1/2 h-full flex items-center justify-start">
                        <select id='status' class="relative w-32 h-12 text-center flex text-green-600 items-center justify-center bg-white rounded-md shadow-md font-bold text-lg">
                            <option value="all" class="text-green-500" selected>All</option>
                            <option value="2" class="text-red-500">No Delivery</option>
                            <option value="3" class="text-green-500">Delivered</option>
                        </select>
                        <select id='time' class="relative w-32 h-12 text-center flex text-green-600 items-center justify-center bg-white rounded-md shadow-md font-bold text-lg ml-4">
                            <option value="ASC" class="text-green-500">Time Asc</option>
                            <option value="DESC" class="text-red-500" selected>Time Desc</option>
                        </select>
                    </div>
                </div>
                <div class="relative w-full h-full flex flex-col bg-white overflow-y-auto px-8 py-4">
                    <!-- order list table-->
                    <div id='orderList' class="relative w-full max-h-full flex flex-col items-center justify-start overflow-y-auto">
                        <!-- order item -->
                        <!-- <div class='w-full h-80 bg-gray-100 flex items-center justify-center border-4 overflow-hidden'>
                            <div class='w-54 h-full text-xs space-y-2 bg-gray-100 flex flex-col items-start justify-start p-4 border-r-4'>
                                <p class="font-bold">Order ID : <span class="font-medium">#1</span></p>
                                <p class="font-bold">Name : <span class="font-medium">John</span></p>
                                <p class="font-bold">Total : <span class="font-medium">฿100</span></p>
                                <p class="font-bold">Time : <span class="font-medium">2022-10-01 11:11:11</span></p>
                                <label for="status" class="font-bold">Status:
                                    <select class="font-medium" id="status" name="status">
                                        <option value="2">No Delivery</option>
                                        <option value="3">Delivered</option>
                                    </select>
                                </label>
                                <button class="w-full mx-auto rounded-md text-white text-center bg-green-400 p-2 hover:bg-green-300">
                                    Save Change
                                </button>
                            </div>

                            <div class="min-w-0 w-full max-w-full h-full overflow-x-auto flex">
                                <div class="flex-none min-w-52 max-w-52 h-auto min-h-full bg-green-100 p-4 overflow-hidden border-2">
                                    <div class="flex flex-col justify-between space-y-2">
                                        <div class='w-full h-2/3'>
                                            <img class="w-48 h-48 object-contain rounded" src="https://cdn.1112.com/1112/public/images/products/pizza/Topping/162217.png" alt="Pizza">
                                        </div>
                                        <div class="w-full min-h-1/3 text-xs h-full flex flex-col items-center space-y-2">
                                            <p class="font-bold w-48 break-all line-clamp-2">PizzddddddddddadsadasduahsdghsPizzddddddddddadsadasduahsdghs</p>
                                            <div class="w-full h-8 flex items-center justify-center bg-green-400 rounded-md text-white text-center">
                                                <p class="font-bold border-r px-2">S</p>
                                                <p class="font-bold border-r px-2">Thin</p>
                                                <p class="font-bold px-2">x1</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <?php
                        // include SimpleProduct
                        include_once 'components/AdminProductOrder.php';

                        AdminProductOrder2($items);

                        // loop items
                        // foreach ($items as $item) {
                        //     // function HistoryOrder($id, $timeStamp, $total, $status)
                        //     AdminProductOrder($item['order_id'], $item['user_id'], $item['order_time'], $item['total_price'], $item['order_status']);
                        // }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- swl -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Script -->
    <script>
        // get data
        async function getData() {
            // formData
            const formData = new FormData()
            formData.append("status", document.getElementById("status").value)
            formData.append("time", document.getElementById("time").value)

            const resp = fetch("services/Action.php?action=adminGetOrder", {
                method: "POST",
                body: formData
            }).then(res => res.json())

            return resp;
        }
    </script>
    <script>
        // save status
        async function saveStatus(id, status) {
            // formData
            const formData = new FormData()
            formData.append("id", id)
            formData.append("status", status)

            const resp = fetch("services/Action.php?action=saveStatus", {
                method: "POST",
                body: formData
            }).then(res => res.json())

            return resp
        }


        function GetOrderDetails(user_id, id) {

            // formData
            const formData = new FormData()
            formData.append("user_id", user_id)
            formData.append("id", id)

            const resp = fetch(
                "services/Action.php?action=getOrderDetails", {
                    method: "POST",
                    body: formData
                }
            ).then(res => res.json())

            return resp
        }

        function GetUserData(user_id) {

            // formData
            const formData = new FormData()
            formData.append("user_id", user_id)

            const resp = fetch(
                "services/Action.php?action=getUserData", {
                    method: "POST",
                    body: formData
                }
            ).then(res => res.json())

            return resp
        }

        // save status button event id Save_
        document.querySelectorAll('[id^="Save_"]').forEach(item => {

            // select status onchange remove and add disable
            item.parentElement.querySelector("select").addEventListener("change", event => {
                const elementSelector = event.target;

                // get option value by find selected attr
                const optionValue = elementSelector.options[elementSelector.selectedIndex];

                if (optionValue.hasAttribute("selected") && !item.hasAttribute("disabled")) {

                    item.setAttribute("disabled", "disabled")

                } else {
                    item.removeAttribute("disabled")
                }
            })

            item.addEventListener('click', event => {
                // get id
                const id = item.id.split("_")[1]
                // get status
                const status = item.parentElement.parentElement.querySelector("select").value

                // show confirmation dialog
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You are about to change the status of this order!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, change it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // save status
                        saveStatus(id, status).then(resp => {
                            Swal.fire({
                                icon: resp.status,
                                title: resp.message,
                                showConfirmButton: false,
                                timer: 1500
                            })

                            // selected attr set to option
                            const elementSelector = item.parentElement.parentElement.querySelector("select")

                            const optionValue = elementSelector.options[elementSelector.selectedIndex];
                            optionValue.setAttribute("selected", "selected")

                            // remove disabled
                            item.setAttribute("disabled", "disabled")

                            // other option remove selected
                            elementSelector.options.forEach(element => {
                                if (element !== optionValue) {
                                    element.removeAttribute("selected")
                                }
                            });
                        })
                    }
                })
            })
        })
    </script>
    <script src="js/filterOrder.js"></script>
</body>

</html>