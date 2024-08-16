<?php
include_once 'config/session.php';

// Clear Session
session_destroy();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pizza Paradise</title>
    <!-- Include Tailwind CSS stylesheet -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Include CSS StyleSheet -->
    <link rel="stylesheet" href="style.css">
</head>

<body class="bg-gray-100">
    <div class="w-full h-full bg-cover bg-no-repeat bg-[url('https://le-cdn.hibuwebsites.com/15fe0160120042c78dad97e25db2e648/dms3rep/multi/opt/hero1-1920w.jpg')]" style="height: 100%;height: 100vh;height: 100dvh;">
        <div class="container w-full h-full mx-auto">
            <div class="w-full h-full flex items-center justify-center">
                <div class="w-1/2 h-fit flex flex-col items-center justify-end backdrop-blur-sm bg-white/80 rounded shadow p-4 md:p-6 lg:px-32 xl:px-52 2xl:px-72 ">
                    <div class="w-full h-full flex flex-col items-center justify-center py-5">
                        <!-- Logo -->
                        <img src="https://menufyproduction.imgix.net/637849359719551935+751750.png" alt="logo" class="w-full h-full object-cover">
                        <!-- Name Shop -->
                        <h1 class="text-3xl font-bold text-green-600">
                            Pizza <span class="text-red-500 font-bold">Paradise</span>
                        </h1>
                    </div>
                    <form id="formLogin" action="#" method="post" class="w-full h-full flex flex-col items-center justify-center">
                        <div class="w-full mb-4">
                            <label for="email" class="text-sm text-gray-700">Email</label>
                            <input id="email" type="email" name="email" placeholder="Email" class="w-full border border-gray-300 rounded shadow p-2">
                        </div>
                        <div class="w-full mb-4">
                            <label for="password" class="text1-sm text-gray-700">Password</label>
                            <input id="password" type="password" name="password" placeholder="Password" class="w-full border border-gray-300 rounded shadow p-2">
                        </div>
                        <div class="w-full mb-4">
                            <input type="submit" value="Login" class="w-full cursor-pointer bg-gray-700 hover:bg-gray-800 text-white font-bold text-lg rounded shadow p-2">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- scripts -->
    <!-- login -->
    <script src="js/login.js"></script>
    <!-- swl2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>