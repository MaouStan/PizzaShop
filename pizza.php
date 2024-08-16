<?php
session_start();

// check id
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

// include services
require_once 'services/PizzaManager.php';

// create class
$pizza = new PizzaManager();

// get pizza detail
$pizza_detail = $pizza->GetPizza($_GET['id']);

$pizza_name = $pizza_detail['name'];
$pizza_image = $pizza_detail['picture'];
$pizza_description = $pizza_detail['description'];
$pizza_price = $pizza_detail['base_price'];

// get pizza variations
$pizza_variations = $pizza_detail['pizza_variations'];
?>

<?php
include_once 'config/session.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pizza_name ?> - Pizza Paradise</title>
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
    <main class="w-full h-full pt-20 flex items-center justify-center">
        <!-- container -->
        <div class="w-full h-full container max-w-5xl px-4 flex flex-col items-center justify-start">
            <!-- Product Image BG Top -->
            <div class="w-full h-[33dvh] bg-gray-300 rounded-t-md overflow-hidden">
                <!-- Product Image -->
                <img src="<?= $pizza_image ?>" alt="Pizza" class="w-full h-full object-contain" id="pizzaImage">
            </div>
            <!-- Product Info -->
            <div class="w-full h-auto bg-white rounded-md shadow-xl">
                <!-- Product Name -->
                <div class="w-full h-auto py-2 px-4">
                    <h1 class="text-3xl font-bold text-green-600" id="namePizza"><?= $pizza_name ?></h1>
                </div>
                <!-- Product Description -->
                <div class="w-full h-auto py-2 px-4">
                    <p class="text-lg text-gray-600" id="descPizza"><?= $pizza_description ?>
                    </p>
                </div>
                <!-- Product Price -->
                <div class="w-full h-auto py-2 px-4">
                    <p id="pricePizza" class="text-2xl font-bold text-green-600"> ฿<?= $pizza_price ?> </p>
                </div>
                <!-- Product Size -->
                <div class="w-full h-auto py-2 px-4">
                    <div class="w-full h-auto flex items-center justify-start space-x-4">
                        <!-- Size Label -->
                        <div class="w-1/3 h-auto flex items-center justify-start">
                            <p class="text-xl font-bold text-gray-600">Size:</p>
                        </div>
                        <!-- Size Input -->
                        <div id="sizeSelection" class="w-fit h-1/2 py-4 flex flex-wrap items-center flex-grow-0 justify-start gap-4">
                            <button id="1" class="bg-green-500 text-white w-fit px-8 hover:bg-green-500 border-2 border-gray-400 py-2 text-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105">
                                S
                            </button>
                            <button id="2" class="w-fit px-8 text-black hover:bg-green-500 border-2 border-gray-400 py-2 text-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105">
                                M
                            </button>
                            <button id="3" class="w-fit px-8 text-black hover:bg-green-500 border-2 border-gray-400 py-2 text-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105">
                                L
                            </button>
                            <button id="4" class="w-fit px-8 text-black hover:bg-green-500 border-2 border-gray-400 py-2 text-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105">
                                XL
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Product Crust -->
                <div class="w-full h-auto py-2 px-4">
                    <div class="w-full h-auto flex items-center justify-start space-x-4">
                        <!-- Crust Label -->
                        <div class="w-1/3 h-auto flex items-center justify-start">
                            <p class="text-xl font-bold text-gray-600">Crust:</p>
                        </div>
                        <!-- Crust Input -->
                        <div id="crustSelection" class="w-fit h-1/2 py-4 flex flex-wrap items-center flex-grow-0 justify-start gap-4">
                            <button id="1" class="bg-green-500 text-white w-fit px-8 hover:bg-green-500 border-2 border-gray-400 py-2 text-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105">
                                Thin Crispy
                            </button>
                            <button id="2" class="w-fit px-8 text-black hover:bg-green-500 border-2 border-gray-400 py-2 text-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105">
                                Thick and Soft
                            </button>
                            <button id="3" class="w-fit px-8 text-black hover:bg-green-500 border-2 border-gray-400 py-2 text-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105">
                                Cheese Crust
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Product Quantity -->
                <div class="w-full h-auto py-2 px-4">
                    <div class="w-full h-auto flex items-center justify-start space-x-4">
                        <!-- Quantity Label -->
                        <div class="w-1/3 h-auto flex items-center justify-start">
                            <p class="text-xl font-bold text-gray-600">Quantity:</p>
                        </div>
                        <!-- Quantity Input -->
                        <div class="w-2/3 h-auto flex items-center justify-start">
                            <input id="quantity" type="number" min="1" value="1" class="w-16 h-8 border-2 border-gray-300 rounded-md text-center text-lg text-gray-600">
                        </div>
                    </div>
                </div>
                <!-- Product Add to Cart -->
                <div class="w-full h-auto py-2 px-4">
                    <div class="w-full h-auto flex items-center justify-center">
                        <button disabled class="AddToCart w-full h-12 bg-green-600 rounded-md shadow-xl text-white text-xl font-bold hover:bg-green-500">Add
                            to Cart</button>
                    </div>
                </div>

            </div>

            <!-- Suggest Section -->
            <div class="w-full h-auto py-2 px-4">
                <div class="w-full h-auto py-2 px-4">
                    <h1 class="text-3xl font-bold text-green-600">You might also like:</h1>
                </div>
                <!-- IF more than md show image name and cost else just image -->
                <!-- Cards Grid -->
                <div class="grid grid-cols-1 grid-col sm:grid-cols-3 gap-8 w-full mx-auto mt-7 mb-9
                        md:gap-4 md:mt-4 md:mb-6
                        lg:gap-4 lg:mt-4 lg:mb-6
                        xl:gap-4 xl:mt-4 xl:mb-6
                        2xl:gap-4 2xl:mt-4 2xl:mb-6
            " id="suggestCards">
                    <!-- Example Card -->
                    <?php
                    // include component
                    require_once(__DIR__ . '/components/PizzaSuggestCard.php');

                    // get pizza suggest
                    $pizzas = $pizza->GetPizzas();

                    // random 3
                    $data = array_rand($pizzas, 3);

                    // inf loop check data length
                    while (true) {
                        if (count($data) < 3) {
                            $data = array_rand($pizzas, 3);
                        } else {
                            if ($pizzas[$data[0]]['pizza_id'] == $_GET['id'] || $pizzas[$data[1]]['pizza_id'] == $_GET['id'] || $pizzas[$data[2]]['pizza_id'] == $_GET['id']) {
                                $data = array_rand($pizzas, 3);
                            } else {
                                break;
                            }
                        }
                    }

                    // loop pizza suggest
                    foreach ($data as $pizza) {
                        echo makeSuggest($pizzas[$pizza]['pizza_id'], $pizzas[$pizza]['picture'], $pizzas[$pizza]['name'], $pizzas[$pizza]['base_price']);
                    }
                    ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Script -->
    <script src="js/product.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        ///// ======= Add to Cart ======= /////
        // Add to Cart
        function addToCart(variation_id) {
            const quantity = document.getElementById("quantity").value;
            const formData = new FormData();
            formData.append("quantity", quantity);
            formData.append("variation_id", variation_id);

            fetch("services/Action.php?action=addToCart", {
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
                    })

                    if (resp.status == "success") {
                        // getCount Items Cart
                        async function getCount() {
                            const resp = await fetch("services/Action.php?action=getCartCount", {
                                method: "GET",
                            })
                            return resp.json()
                        }

                        // init getCount
                        async function initCount() {
                            const resp = await getCount();
                            const count = resp['data']['count'];
                            document.getElementById("cart_number").innerHTML = count ? count : 0;
                        }

                        initCount();
                    }
                })

        }

        // Add to Cart Event
        document.querySelector(".AddToCart").addEventListener("click", (e) => {
            e.preventDefault();
            const variation_id = document.querySelector(".AddToCart").id;

            addToCart(variation_id)
        });
    </script>
</body>

</html>
