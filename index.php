<?php
include_once 'config/session.php';


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Pizza Paradise</title>
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
            <!-- Hero Section -->
            <section class="w-full bg-cover bg-center h-80 rounded-lg" style="background-image: url('https://img-global.cpcdn.com/recipes/2f8496835725af3d/640x640sq70/photo.webp');">
                <div class="bg-black bg-opacity-60 h-full flex px-4 flex-col justify-center items-center text-white">
                    <h1 class="text-4xl font-semibold">Welcome to Pizza Paradise</h1>
                    <p class="text-lg">Discover the taste of Italy in every bite.</p>
                    <button id="OrderNow" class="mt-4 bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-6 rounded-full text-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105">
                        Order Now
                    </button>
                </div>
            </section>

            <!-- Products Section -->
            <section class="my-12 w-full">
                <!-- Title -->
                <h2 id="OurMenu" class="text-3xl font-semibold text-gray-900 mb-4">Our Menu</h2>
                <!-- Filters -->
                <div class="flex w-full h-12 overflow-hidden justify-between px-8 mb-4 space-x-4">
                    <!-- Button Filter -->
                    <div id="lgOrderSort" class="hidden md:flex w-full h-12 overflow-hidden justify-start mb-4 space-x-4">
                        <button id="MostSell" class="relative bg-white w-fit px-8 hover:bg-green-500 border-2 border-gray-400 text-black py-2 text-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105">
                            MostSell
                        </button>

                        <!-- <button id="Latest" class="relative bg-white w-fit px-8 hover:bg-green-500 border-2 border-gray-400 text-black py-2 text-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105">
                            Latest
                        </button> -->

                        <select id='orderDrop' class="relative bg-white w-fit px-8 border-2 border-gray-400 text-black py-2 text-lg font-semibold transition duration-300 ease-in-out transform ">
                            <option value="Default" selected>Default</option>
                            <option value="priceASC">Price น้อยไปมาก</option>
                            <option value="priceDESC">Price มากไปน้อย</option>
                            <option value="nameASC">Name A-Z</option>
                            <option value="nameDESC">Name Z-A</option>
                        </select>
                    </div>

                    <!-- Search -->
                    <div class="max-w-[300px] w-full h-full flex items-center justify-end">
                        <input id="search" type="text" class="w-full h-full p-2 text-lg border-2 border-gray-400 rounded-md shadow-md focus:outline-none focus:border-green-500" placeholder="ค้นหา">
                    </div>

                    <!-- Button Filter -->
                    <!-- <button id="btnFilter"
                        class="md:hidden w-fit h-full flex space-x-4 items-center bg-green-500 hover:bg-green-600 text-white px-4 rounded-xl text-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105">
                        Filter
                        <svg class="h-7 w-auto" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M4 5C3.44772 5 3 5.44772 3 6C3 6.55228 3.44772 7 4 7H20C20.5523 7 21 6.55228 21 6C21 5.44772 20.5523 5 20 5H4ZM7 12C7 11.4477 7.44772 11 8 11H20C20.5523 11 21 11.4477 21 12C21 12.5523 20.5523 13 20 13H8C7.44772 13 7 12.5523 7 12ZM13 18C13 17.4477 13.4477 17 14 17H20C20.5523 17 21 17.4477 21 18C21 18.5523 20.5523 19 20 19H14C13.4477 19 13 18.5523 13 18Z"
                                fill="#fff" />
                        </svg>
                    </button> -->
                </div>
                <!-- ListProducts -->
                <div id="products" class="p-8 grid overflow-x-hidden grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <?php
                        require_once('services/PizzaManager.php');
                        require_once('components/PizzaCard.php');
                        $pizza = new PizzaManager();
                        $data = $pizza->GetPizzas();

                        foreach ($data as $row) {
                            // Assuming getPizzaCard is defined in PizzaCard.php
                            echo getPizzaCard($row['pizza_id'], $row['picture'], $row['name'], $row['base_price']);
                        }
                    ?>

                    <!-- <div id="product" class="bg-white p-6 rounded-lg shadow-md">
                    <div class="cursor-pointer">
                        <img src="https://img-global.cpcdn.com/recipes/2f8496835725af3d/640x640sq70/photo.webp"
                            alt="Margherita Pizza" class="w-full h-48 object-cover rounded">
                        <h3 class="text-xl font-semibold mt-4">Margherita Pizza</h3>
                        <p class="text-gray-600">Fresh tomato sauce, mozzarella, basil</p>
                        <span class="text-gray-700 font-semibold mt-2">$10.99 - $30.99</span>
                        <div class="w-full flex items-center justify-center mt-4">
                            <button id="add"
                                class="w-full bg-green-500 hover:bg-green-600 text-white py-2 px-6 rounded-full text-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105">
                                + Add
                            </button>
                        </div>
                    </div>
                </div> -->
                </div>
            </section>
        </div>
    </main>

    <!-- Overlay Filter From lg:Right-side -->
    <div id="FilterOverlay" class="hidden fixed w-screen h-screen top-20 right-0 inset-0 z-10">
        <!-- BG -->
        <div class="absolute inset-0 bg-black opacity-50 z-10"></div>
        <!-- Content -->
        <div class="relative w-full container max-w-5xl h-full flex items-center justify-end">
            <!-- Close -->
            <div id="CloseFilterOverlay" class="absolute top-4 right-4 w-12 h-12 flex items-center justify-center text-white text-2xl z-30 cursor-pointer text-red-500">
                <!-- SVG -->
                <svg class="h-12 w-auto" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path id="Vector" d="M18 18L12 12M12 12L6 6M12 12L18 6M12 12L6 18" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <!-- Filters Section -->
            <!-- PSort, Most Sell -->
            <!-- <div id="filter-overlay-content" class="bg-white z-20 w-screen h-screen md:w-2/5 rounded-lg shadow-lg p-8">
                <div class="w-full h-full flex items-center justify-start flex-col space-y-4 text-center">
                    Title
            <h2 class="text-3xl font-semibold text-gray-900 mb-4 select-none">Filters</h2>
            <div class="w-full h-fit flex flex-col">
                <h3 class="text-2xl font-semibold text-left">Sort By</h3>
                <div id="FilterSorts" class="w-full text-xl flex items-center justify-start flex-wrap gap-x-2">
                    <div class="w-fit h-fit py-4 flex items-start justify-start">
                        <button id="sortDefalut"
                            class="relative bg-green-500 text-white w-full px-8 hover:bg-green-500 border-2 border-gray-400 text-black py-2 text-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105">
                            Default
                        </button>
                    </div>
                    <div class="w-fit h-fit py-4 flex items-start justify-start">
                        <button id="sortName"
                            class="relative w-full px-8 hover:bg-green-500 border-2 border-gray-400 text-black py-2 text-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105">
                            Name
                        </button>
                    </div>
                    <div class="w-fit h-fit py-4 flex items-start justify-start">
                        <button id="sortMostSell"
                            class="relative w-full px-8 hover:bg-green-500 border-2 border-gray-400 text-black py-2 text-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105">
                            Most Sell
                        </button>
                    </div>
                    <div class="w-fit h-fit py-4 flex items-start justify-start">
                        <button id="sortPrice"
                            class="relative w-full px-8 hover:bg-green-500 border-2 border-gray-400 text-black py-2 text-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105">
                            Price
                        </button>
                    </div>

                </div>
                <div id="FilterOrders" class="w-full text-xl flex items-center justify-start flex-wrap gap-x-2">
                    <div class="w-fit h-fit py-4 flex items-start justify-start">
                        <button id="sortASC"
                            class="relative bg-green-500 text-white w-full px-8 hover:bg-green-500 border-2 border-gray-400 text-black py-2 text-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105">
                            ASC
                        </button>
                    </div>
                    <div class="w-fit h-fit py-4 flex items-start justify-start">
                        <button id="sortDESC"
                            class="relative w-full px-8 hover:bg-green-500 border-2 border-gray-400 text-black py-2 text-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105">
                            DESC
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
        </div>
    </div>

    <!-- Overlay Example Select Add -->
    <!-- 4 sizes (S, M, L, XL) and 3 types of crust (thin, crispy, Thick and soft, cheese edge) -->
    <div id="AddOverlay" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black opacity-50 z-50"></div>
        <div id="add-overlay-content" class="relative bg-white z-50 container max-w-5xl w-full h-full lg:w-1/2 lg:h-1/2 rounded-lg shadow-lg pt-8">
            <!-- Close -->
            <div id="CloseAddOverlay" class="absolute top-2 right-2 w-12 h-12 flex items-center justify-center text-white text-2xl z-30 cursor-pointer text-red-500">
                <!-- SVG -->
                <svg class="h-12 w-auto" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path id="Vector" d="M18 18L12 12M12 12L6 6M12 12L18 6M12 12L6 18" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div class=" w-full h-full flex items-center justify-start flex-col space-y-4 text-center">
                <!-- Title -->
                <h2 class="text-3xl font-semibold text-gray-900 mb-4">Add to Cart</h2>
                <!-- Product Detail, Img Square, Name, Price Total -->
                <div id="selectID" class="selectID w-full h-full flex items-center justify-start flex-col space-y-4 overflow-hidden">
                    <!-- Product Detail -->
                    <div class="w-full h-16 lg:h-1/6 flex items-center justify-between space-y-4 px-4 shadow-xl">
                        <!-- Img Square -->
                        <div class="w-full h-full flex items-center justify-start space-x-4">
                            <img id="selectImgPizza" src="https://img-global.cpcdn.com/recipes/2f8496835725af3d/640x640sq70/photo.webp" alt="Select Pizza" class="w-auto h-4/5 object-contain rounded">
                            <!-- Name -->
                            <h3 id="selectNamePizza" class="text-xl font-semibold">Margherita Pizza</h3>
                        </div>
                        <!-- Price Total -->
                        <span id="selectPricePizza" class="text-gray-700 font-semibold mt-2 text-2xl w-full text-right">$30.99</span>
                    </div>
                    <!-- Size & Crust & Quantity -->
                    <div class="w-full h-full flex flex-col items-center justify-start text-left space-y-4 pb-4 px-4 overflow-y-auto">
                        <!-- Size -->
                        <section class="w-full h-fit">
                            <!-- Section Name -->
                            <h3 class="text-2xl font-semibold">Size</h3>
                            <!-- Section Selections -->
                            <div class="w-full h-fit flex items-center justify-start flex-wrap space-x-4">
                                <!-- Selection -->
                                <div id="sizeSelection" class="w-fit h-1/2 py-4 flex flex-wrap items-center flex-grow-0 justify-start gap-4">
                                    <button id="1" class="bg-green-500 text-white w-fit px-8 hover:bg-green-500 border-2 border-gray-400 text-black py-2 text-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105">
                                        S
                                    </button>
                                    <button id="2" class="w-fit px-8 hover:bg-green-500 border-2 border-gray-400 text-black py-2 text-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105">
                                        M
                                    </button>
                                    <button id="3" class="w-fit px-8 hover:bg-green-500 border-2 border-gray-400 text-black py-2 text-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105">
                                        L
                                    </button>
                                    <button id="4" class="w-fit px-8 hover:bg-green-500 border-2 border-gray-400 text-black py-2 text-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105">
                                        XL
                                    </button>
                                </div>
                            </div>
                        </section>
                        <!-- Section Crust -->
                        <section class="w-full h-fit">
                            <!-- Section Name -->
                            <h3 class="text-2xl font-semibold">Crust</h3>
                            <!-- Section Selections -->
                            <div class="w-full h-fit flex items-center justify-start flex-wrap space-x-4">
                                <!-- Selection -->
                                <div id="crustSelection" class="w-fit h-1/2 py-4 flex flex-wrap items-center flex-grow-0 justify-start gap-4">
                                    <button id="1" class="bg-green-500 text-white w-fit px-8 hover:bg-green-500 border-2 border-gray-400 text-black py-2 text-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105">
                                        Thin Crispy
                                    </button>
                                    <button id="2" class="w-fit px-8 hover:bg-green-500 border-2 border-gray-400 text-black py-2 text-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105">
                                        Thick and Soft
                                    </button>
                                    <button id="3" class="w-fit px-8 hover:bg-green-500 border-2 border-gray-400 text-black py-2 text-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105">
                                        Cheese Crust
                                    </button>
                                </div>
                            </div>
                        </section>
                        <!-- Section Quantity -->
                        <section class="w-full h-fit select-none">
                            <!-- Section Name -->
                            <h3 class="text-2xl font-semibold">Quantity</h3>
                            <!-- Section Selections -->
                            <div class="w-full h-fit flex items-center justify-start flex-wrap space-x-4">
                                <!-- Selection -->
                                <div id="quantity" class="w-fit h-1/2 py-4 flex items-center justify-center space-x-4">
                                    <button id="quantityMinus" class="w-full px-4 hover:bg-green-500 border-2 border-gray-400 text-black py-2 text-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105">
                                        -
                                    </button>
                                    <span id="quantityNumber" class="w-full px-4 border-2 border-gray-400 text-black py-2 text-lg font-semibold">
                                        1
                                    </span>
                                    <button id="quantityPlus" class="w-full px-4 hover:bg-green-500 border-2 border-gray-400 text-black py-2 text-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105">
                                        +
                                    </button>
                                </div>
                            </div>
                        </section>
                        <!-- Order Button -->
                        <div class="w-full h-full flex items-center justify-center mt-4 px-12">
                            <button id="AddToCart" class="AddToCart w-full bg-green-500 hover:bg-green-600 text-white py-2 px-6 rounded-full text-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script -->
    <script src="js/home.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>