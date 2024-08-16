-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 25, 2023 at 02:39 AM
-- Server version: 8.0.20-0ubuntu0.19.10.1
-- PHP Version: 7.3.11-0ubuntu0.19.10.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `web66_65011212122`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`web66_65011212122`@`%` PROCEDURE `add_item_into_cart` (IN `p_order_id` INT, IN `pizza_variation_id` INT, IN `p_quantity` INT)  BEGIN
    DECLARE is_error INT DEFAULT 1;

    -- Check if the item already exists in the cart
    IF NOT EXISTS (SELECT * FROM order_items WHERE order_id = p_order_id AND variation_id = pizza_variation_id) THEN
        -- If the item does not exist, insert a new record
        INSERT INTO order_items (order_id, variation_id, quantity) VALUES (p_order_id, pizza_variation_id, p_quantity);
        SET is_error = 0;
    ELSE
        -- If the item exists, update its quantity
        UPDATE order_items SET quantity = quantity + p_quantity WHERE order_id = p_order_id AND variation_id = pizza_variation_id;
        SET is_error = 0;
    END IF;

    CALL update_order_total(p_order_id);

    IF is_error = 0 THEN
        SELECT 'success' AS status;
    ELSE
        SELECT 'error' AS status;
    END IF;
END$$

CREATE DEFINER=`web66_65011212122`@`%` PROCEDURE `Checkout` (IN `p_user_id` INT, IN `p_order_id` INT, IN `p_paymentMethod` VARCHAR(255), IN `p_name` VARCHAR(255), IN `p_phone` VARCHAR(255), IN `p_address` VARCHAR(255))  BEGIN
    DECLARE v_wallet INT;
    DECLARE v_total_price INT;
    DECLARE v_is_error INT DEFAULT 0;

    -- update total
    CALL update_order_total(p_order_id);

    -- Get user's wallet balance
    SELECT wallet INTO v_wallet FROM users WHERE user_id = p_user_id limit 1;

    -- Get order total price
    SELECT total_price INTO v_total_price FROM orders WHERE order_id = p_order_id limit 1;

    -- Check wallet balance and payment method
    IF v_wallet >= v_total_price AND p_paymentMethod = 'wallet' THEN
        -- Update order status for wallet payment
        UPDATE orders SET order_status = '2' WHERE order_id = p_order_id;
        -- update Wallet
        UPDATE users 
        SET wallet = wallet - v_total_price
        WHERE user_id = p_user_id;

    ELSEIF p_paymentMethod = 'later' THEN
        -- Update order status for later payment
        UPDATE orders SET order_status = '1' WHERE order_id = p_order_id;
    ELSE
        SET v_is_error = 1;
    END IF;

    -- Update order with receiver information
    UPDATE orders
    SET receiver_name = p_name, receiver_address = p_address, receiver_phone = p_phone
    WHERE order_id = p_order_id;

    -- updateTime
    CALL update_order_time(p_order_id);

    -- return isError
    SELECT v_is_error limit 1;
END$$

CREATE DEFINER=`web66_65011212122`@`%` PROCEDURE `create_new_order` (IN `p_user_id` INT, OUT `p_new_order_id` INT)  BEGIN
  -- Initialize p_new_order_id with a default value of 0
  SET p_new_order_id = 0;

  -- Add your code to create a new order here
  -- This is a simplified example, and you should implement the actual logic to create a new order.
  -- After creating the order, set the new_order_id as the output parameter.
  INSERT INTO orders (user_id, order_status)
  VALUES (p_user_id, '0');

  SET p_new_order_id = LAST_INSERT_ID();
END$$

CREATE DEFINER=`web66_65011212122`@`%` PROCEDURE `generate_and_update_pizza_variations` (IN `pizza_id` INT)  BEGIN
  DECLARE size_multiplier DECIMAL(5, 2);
  DECLARE crust_multiplier DECIMAL(5, 2);
  DECLARE pizza_base_price DECIMAL(10, 2);
  DECLARE size_id INT;
  DECLARE crust_id INT;
  DECLARE selected_pizza_base_price INT; -- New variable

  -- Fetch the base_price for the pizza from the pizzas table
  SELECT base_price
  INTO selected_pizza_base_price
  FROM pizzas
  WHERE pizza_id = pizza_id
  limit 1;

  -- Assign the selected value to the declared variable
  SET pizza_base_price = selected_pizza_base_price;

  -- Loop through all possible size and crust combinations
  SET size_id = 1;
  WHILE size_id <= 4 DO  -- Assuming size IDs from 1 to 4
    SET crust_id = 1;
    WHILE crust_id <= 3 DO  -- Assuming crust IDs from 1 to 3
      -- Calculate size_multiplier based on size
      CASE size_id
        WHEN 1 THEN SET size_multiplier = 1.00;  -- S
        WHEN 2 THEN SET size_multiplier = 1.25;  -- M
        WHEN 3 THEN SET size_multiplier = 1.50;  -- L
        WHEN 4 THEN SET size_multiplier = 1.75;  -- XL
        ELSE SET size_multiplier = 1.00; -- Default to 1.00 for unknown sizes
      END CASE;

      -- Calculate crust_multiplier based on crust
      CASE crust_id
        WHEN 1 THEN SET crust_multiplier = 1;  -- Thin Crispy
        WHEN 2 THEN SET crust_multiplier = 1.35;  -- Thick and Soft
        WHEN 3 THEN SET crust_multiplier = 1.15;  -- Cheese Crust
        ELSE SET crust_multiplier = 1.00; -- Default to 1.00 for unknown crusts
      END CASE;

      -- Calculate the price based on pizza_base_price, size_multiplier, and crust_multiplier
      SET @new_price = pizza_base_price * size_multiplier * crust_multiplier;

      -- Insert the pizza variation
      INSERT INTO pizza_variations (pizza_id, size_id, crust_id, price)
      VALUES (pizza_id, size_id, crust_id, @new_price);

      SET crust_id = crust_id + 1;
    END WHILE;
    SET size_id = size_id + 1;
  END WHILE;
END$$

CREATE DEFINER=`web66_65011212122`@`%` PROCEDURE `GetCart` (IN `p_order_id` INT)  BEGIN
    DECLARE cart_exists INT DEFAULT 0;

    -- Check if the user's cart exists and has status '0'
    SELECT COUNT(*) INTO cart_exists FROM orders WHERE order_id = p_order_id limit 1;

    IF cart_exists = 0 THEN
        SELECT 'error' AS status, 'Cart not found or it is not active' AS message limit 1;
    ELSE
        -- Get the items in the user's cart
        SELECT
            pv.variation_id,
            oi.quantity,
            pv.price AS pizzaPrice,
            pv.size_id,
            pv.crust_id,
            p.pizza_id AS pizzaId,
            p.name AS pizzaName,
            p.picture AS pizzaImage,
            ps.size AS pizzaSize,
            pc.crust AS pizzaCrust
        FROM order_items oi
        JOIN pizza_variations pv ON oi.variation_id = pv.variation_id
        JOIN pizzas p ON pv.pizza_id = p.pizza_id
        JOIN pizza_sizes ps ON pv.size_id = ps.size_id
        JOIN pizza_crusts pc ON pv.crust_id = pc.crust_id
        WHERE oi.order_id = p_order_id;
    END IF;
END$$

CREATE DEFINER=`web66_65011212122`@`%` PROCEDURE `GetItemCount` (IN `p_order_id` INT)  BEGIN
    DECLARE variation_count INT DEFAULT -1;
    DECLARE result_status VARCHAR(255);
    DECLARE result_message VARCHAR(255);

    -- Get the number of items in the user's cart
    SELECT COUNT(DISTINCT variation_id) into variation_count FROM order_items WHERE order_id = p_order_id limit 1;

    IF variation_count = -1 THEN
        SET result_status = 'error';
        SET result_message = 'Failed to get item count';
    ELSE
        SET result_status = 'success';
        SET result_message = 'Item count retrieved';
    END IF;

    SELECT result_status AS status
            , result_message AS message
            , variation_count AS item_count;
END$$

CREATE DEFINER=`web66_65011212122`@`%` PROCEDURE `MostSoldPizza` ()  NO SQL
SELECT pv.pizza_id, SUM(oi.quantity) AS total_sold
    FROM order_items oi
    INNER JOIN pizza_variations pv ON oi.variation_id = pv.variation_id
    GROUP BY pv.pizza_id
    ORDER BY total_sold DESC$$

CREATE DEFINER=`web66_65011212122`@`%` PROCEDURE `Pay` (IN `p_user_id` INT, IN `p_order_id` INT)  NO SQL
BEGIN
    DECLARE v_wallet INT;
    DECLARE v_total_price INT;
    DECLARE v_is_error INT DEFAULT 0;

    -- update total
    CALL update_order_total(p_order_id);

    -- Get user's wallet balance
    SELECT wallet INTO v_wallet FROM users WHERE user_id = p_user_id limit 1;

    -- Get order total price
    SELECT total_price INTO v_total_price FROM orders WHERE order_id = p_order_id limit 1;

    -- Check wallet balance and payment method
    IF v_wallet >= v_total_price THEN
        -- Update order status for wallet payment
        UPDATE orders SET order_status = '2' WHERE order_id = p_order_id;
        -- update Wallet
        UPDATE users 
        SET wallet = wallet - v_total_price
        WHERE user_id = p_user_id;
    ELSE
        SET v_is_error = 1;
    END IF;

    -- updateTime
    CALL update_order_time(p_order_id);

    -- return isError
    SELECT v_is_error limit 1;
END$$

CREATE DEFINER=`web66_65011212122`@`%` PROCEDURE `update_order_time` (IN `p_order_id` INT)  BEGIN
    DECLARE old_order_status VARCHAR(255);
    
    -- Get the old order_status
    SELECT order_status INTO old_order_status FROM orders WHERE order_id = p_order_id limit 1;

        -- Update the order_time to the current timestamp
        UPDATE orders SET order_time = CURRENT_TIMESTAMP() WHERE order_id = p_order_id;
END$$

CREATE DEFINER=`web66_65011212122`@`%` PROCEDURE `update_order_total` (IN `calc_to_order_id` INT)  BEGIN
  DECLARE order_total INT DEFAULT 0;

  -- Calculate the total price for the order
  SELECT IFNULL(SUM(oi.quantity * pv.price), 0)
  INTO order_total
  FROM order_items oi
  INNER JOIN pizza_variations pv ON oi.variation_id = pv.variation_id
  WHERE oi.order_id = calc_to_order_id limit 1;

  -- Update the total_price in the orders table
  UPDATE orders
  SET total_price = order_total
  WHERE order_id = calc_to_order_id;
END$$

CREATE DEFINER=`web66_65011212122`@`%` PROCEDURE `user_active_order` (IN `userID` INT)  BEGIN
  DECLARE temp_order_id INT;
  DECLARE orderID INT;

  -- Retrieve the order ID of the user's active order
  SELECT order_id INTO temp_order_id
  FROM orders
  WHERE user_id = userID AND order_status = '0'
  LIMIT 1;

  -- Check if an active order was found
  IF temp_order_id IS NULL THEN
    -- If no active order was found, create a new order and retrieve the new order ID
    CALL create_new_order(userID, @new_order_id);

    -- Assign the newly created order's ID to the output parameter
    SET orderID = @new_order_id;
  ELSE
    -- An active order was found, assign its ID to the output parameter
    SET orderID = temp_order_id;
  END IF;

    -- return oderID
    SELECT orderID as order_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int NOT NULL,
  `user_id` int NOT NULL,
  `order_status` enum('0','1','2','3') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `order_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total_price` int NOT NULL DEFAULT '0',
  `receiver_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `receiver_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `receiver_phone` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_status`, `order_time`, `total_price`, `receiver_name`, `receiver_address`, `receiver_phone`) VALUES
(1, 1, '1', '2023-10-01 13:11:38', 3303, '5', 'test', '5555'),
(2, 1, '3', '2023-10-01 15:11:39', 2244, '5', 'test', '6666'),
(3, 1, '1', '2023-10-24 17:37:44', 3344, '', '', ''),
(4, 1, '3', '2023-10-01 18:53:14', 600, '', '', ''),
(5, 1, '2', '2023-10-01 18:54:05', 377, '', '', ''),
(6, 1, '2', '2023-10-01 18:55:46', 1036, '', '', ''),
(7, 1, '3', '2023-10-01 18:57:47', 321, '', '', ''),
(8, 1, '3', '2023-10-01 18:57:55', 6406, '', '', ''),
(9, 1, '2', '2023-10-01 19:04:57', 686, 'john', 'dasdsadas', '1234657981'),
(10, 1, '2', '2023-10-01 19:08:40', 1374, 'john', '121222', '1234657981'),
(11, 1, '3', '2023-10-01 20:18:42', 5755, 'john', 'TEST', '1234657981'),
(12, 1, '1', '2023-10-02 05:09:41', 6548, 'john', 'TEST', '1234657981'),
(13, 1, '2', '2023-10-20 17:18:59', 1015, 'john', 'deasdasdasd', '1234657981'),
(14, 1, '3', '2023-10-21 12:16:00', 556, 'john', 'deasdasdasd', '1234657981'),
(15, 3, '1', '2023-10-24 14:23:30', 358, '99', '99', '99'),
(16, 3, '2', '2023-10-24 16:38:06', 5570, '1', '1', '1'),
(24, 3, '2', '2023-10-24 16:12:14', 1780, 'pae', 'xxxxxxxxxxxxxxxxxxxxxxx', '1111111111'),
(25, 1, '2', '2023-10-24 17:44:16', 918, 'john', 'deasdasdasd', '1234657981'),
(26, 3, '2', '2023-10-24 16:18:53', 1219, 'pae', 'xxxxxxxxxxxxxxxxxxxxxxx', '1111111111'),
(27, 3, '2', '2023-10-24 16:35:36', 269, 'pae', 'xxxxxxxxxxxxxxxxxxxxxxx', '1111111111'),
(28, 3, '2', '2023-10-24 16:41:20', 565, 'pae', 'xxxxxxxxxxxxxxxxxxxxxxx', '1111111111'),
(29, 3, '2', '2023-10-24 17:23:19', 1063, 'pae', '111', '321321'),
(31, 3, '3', '2023-10-24 17:40:16', 1109, 'pae', '111', '321321'),
(32, 3, '2', '2023-10-24 17:57:35', 2151, 'pae', '111', '321321'),
(33, 2, '0', '2023-10-24 17:40:37', 0, NULL, NULL, NULL),
(34, 1, '2', '2023-10-24 17:49:55', 481, 'john', 'deasdasdasd', '1234657981'),
(35, 1, '2', '2023-10-24 17:54:01', 3100, 'john', 'deasdasdasd', '1234657981'),
(36, 1, '1', '2023-10-24 19:11:42', 423, 'john', 'deasdasdasd', '1234657981'),
(37, 3, '0', '2023-10-24 17:57:25', 448, NULL, NULL, NULL),
(38, 1, '1', '2023-10-24 19:18:42', 4083, 'john', 'deasdasdasd', '1234657981'),
(39, 1, '2', '2023-10-24 19:19:04', 279, 'john', 'deasdasdasd', '1234657981'),
(40, 1, '0', '2023-10-24 19:19:04', 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int NOT NULL,
  `order_id` int NOT NULL,
  `variation_id` int NOT NULL,
  `quantity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `variation_id`, `quantity`) VALUES
(3, 1, 39, 2),
(4, 2, 48, 2),
(5, 1, 74, 1),
(6, 3, 37, 2),
(7, 3, 38, 2),
(8, 3, 39, 1),
(9, 3, 40, 1),
(10, 3, 41, 1),
(11, 4, 37, 2),
(12, 4, 39, 1),
(13, 5, 38, 2),
(14, 6, 38, 2),
(15, 6, 47, 2),
(16, 7, 39, 1),
(17, 8, 74, 7),
(18, 8, 25, 2),
(19, 8, 31, 1),
(20, 8, 37, 2),
(21, 9, 38, 2),
(22, 9, 73, 2),
(23, 10, 86, 2),
(24, 11, 68, 7),
(25, 11, 67, 2),
(26, 11, 69, 2),
(28, 12, 13, 2),
(29, 12, 124, 7),
(35, 13, 3, 1),
(36, 13, 87, 1),
(37, 13, 163, 1),
(39, 14, 13, 2),
(40, 14, 62, 1),
(41, 15, 13, 2),
(43, 16, 2, 2),
(44, 16, 207, 9),
(45, 16, 212, 1),
(46, 16, 206, 1),
(48, 16, 214, 2),
(49, 16, 215, 1),
(50, 16, 209, 1),
(53, 24, 128, 1),
(54, 24, 129, 1),
(55, 24, 127, 1),
(56, 26, 16, 2),
(57, 26, 18, 2),
(58, 3, 24, 2),
(59, 27, 26, 2),
(60, 28, 44, 2),
(62, 3, 46, 2),
(63, 3, 121, 1),
(64, 29, 1, 2),
(65, 29, 19, 2),
(66, 29, 22, 1),
(67, 29, 17, 1),
(68, 31, 11, 1),
(69, 31, 33, 2),
(70, 25, 27, 2),
(71, 25, 30, 1),
(72, 25, 32, 2),
(73, 32, 35, 2),
(74, 32, 36, 2),
(75, 34, 45, 2),
(76, 35, 43, 1),
(77, 35, 89, 1),
(78, 35, 90, 1),
(79, 35, 148, 1),
(80, 35, 149, 1),
(81, 35, 150, 2),
(82, 32, 145, 1),
(83, 32, 154, 1),
(84, 32, 193, 1),
(85, 32, 195, 1),
(86, 37, 15, 1),
(87, 37, 14, 2),
(88, 36, 23, 1),
(89, 38, 37, 12),
(90, 38, 1, 2),
(91, 38, 38, 1),
(92, 39, 133, 1);

-- --------------------------------------------------------

--
-- Table structure for table `pizzas`
--

CREATE TABLE `pizzas` (
  `pizza_id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `picture` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `base_price` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pizzas`
--

INSERT INTO `pizzas` (`pizza_id`, `name`, `description`, `picture`, `base_price`) VALUES
(1, 'Double Cheese', 'Extra Mozzarella Cheese and Pizza Sauce', 'https://cdn.1112.com/1112/public/images/products/pizza/Topping/162216.png', 179),
(2, 'Double Pepperoni', 'Pepperoni, Mozzarella Cheese and Pizza Sauce', 'https://cdn.1112.com/1112/public/images/products/pizza/Topping/162217.png', 179),
(3, 'Sliced Pork Mala', 'Sliced Pork, Huajiaoyou Oil, Dried Chilli, Spring Onion, Vegetable Mix, Mozzarella Cheese and Mala Sauce', 'https://cdn.1112.com/1112/public/images/products/pizza/Aug23/102783.png', 199),
(4, 'Hawaiian', 'Ham, Bacon, Pineapple, Mozzarella Cheese and Pizza Sauce', 'https://cdn.1112.com/1112/public/images/products/pizza/Topping/102204.png', 279),
(5, 'Seafood Cocktail', 'Shrimp, Crab Sticks, Ham, Pineapple, Mozzarella Cheese and Thousand Island Sauce', 'https://cdn.1112.com/1112/public/images/products/pizza/Topping/102208.png', 339),
(6, 'Super Deluxe', 'Ham, Bacon, Pepperoni, Smoked Sausage, Italian Sausage, Mushroom, Pineapple, Onion, Capsicums, Mozzarella Cheese and Pizza Sauce', 'https://cdn.1112.com/1112/public/images/products/pizza/Topping/102201.png', 279),
(7, 'Grilled Hawaiian', 'Ham, Bacon, Pineapple and Pizza Sauce', 'https://cdn.1112.com/1112/public/images/products/pizza/Nov2022/199446.png', 309),
(8, 'Spicy Super Seafood', 'Squid, Garlic Pepper Shrimp, Red & Green Chilli, Capsicums, Onion, Basil, Mozzarella Cheese and Marinara Sauce', 'https://cdn.1112.com/1112/public/images/products/pizza/Topping/102734.png', 339),
(9, 'Seafood Deluxe', 'Shrimp, Crab Sticks, Onion, Capsicums, Mozzarella Cheese and Marinara Sauce', 'https://cdn.1112.com/1112/public/images/products/pizza/Topping/102228.png', 339),
(10, 'Shrimp Cocktail', 'Shrimp, Mushroom, Pineapple, Tomato, Mozzarella Cheese and Thousand Island Sauce', 'https://cdn.1112.com/1112/public/images/products/pizza/Topping/102209.png', 339),
(11, 'Tom Yum Kung', 'EShrimp, Squid, Mushroom, Mozzarella Cheese and Tom Yum Sauce', 'https://cdn.1112.com/1112/public/images/products/pizza/Topping/102212.png', 339),
(12, 'Meat Deluxe', 'Ham, Bacon, Pepperoni, Smoked Sausage, Bacon Dice, Mozzarella Cheese and Pizza Sauce', 'https://cdn.1112.com/1112/public/images/products/pizza/Topping/102210.png', 279),
(13, 'Chicken Trio', 'BBQ Chicken, Garlic Buttered Chicken, Roasted Chicken, Mushroom, Red&Green Chili, Mozzarella Cheese and Pizza Sauce', 'https://cdn.1112.com/1112/public/images/products/pizza/Topping/102203.png', 279),
(14, '4 Cheese & Bacon', 'Bacon, American Cheese, Emmental Cheese, Dairy Valley Parmesan Cheese, Mozzarella Cheese and Sour Cream Sauce', 'https://cdn.1112.com/1112/public/images/products/pizza/Topping/102726.png', 279),
(15, 'Roasted spinach & Tomato', 'Spinach, Mushroom, Onion, Tomato, Red& Green Chilli, Mozzarella Cheese and Pizza Sauce', 'https://cdn.1112.com/1112/public/images/products/pizza/Topping/102725.png', 279),
(16, 'Spicy Grilled Chicken', 'Roasted Chicken, Pineapple, Red&Green Chili, Mozzarella Cheese and Thousand Island Sauce', 'https://cdn.1112.com/1112/public/images/products/pizza/Topping/102833.png', 279),
(17, 'Ham&Crab Sticks', 'Ham, Crab Sticks, Pineapple, Mozzarella Cheese and Thousand Island Sauce', 'https://cdn.1112.com/1112/public/images/products/pizza/Topping/102226.png', 239),
(18, 'Mighty Meat', 'Ham, Smoked Sausage, Pepperoni, Mushroom, Pineapple, Mozzarella Cheese and Pizza Sauce', 'https://cdn.1112.com/1112/public/images/products/pizza/Topping/102723.png', 239);

-- --------------------------------------------------------

--
-- Table structure for table `pizza_crusts`
--

CREATE TABLE `pizza_crusts` (
  `crust_id` int NOT NULL,
  `crust` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pizza_crusts`
--

INSERT INTO `pizza_crusts` (`crust_id`, `crust`) VALUES
(1, 'Thin'),
(2, 'Thick and Soft'),
(3, 'Cheese Crust');

-- --------------------------------------------------------

--
-- Table structure for table `pizza_sizes`
--

CREATE TABLE `pizza_sizes` (
  `size_id` int NOT NULL,
  `size` varchar(10) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pizza_sizes`
--

INSERT INTO `pizza_sizes` (`size_id`, `size`) VALUES
(1, 'S'),
(2, 'M'),
(3, 'L'),
(4, 'XL');

-- --------------------------------------------------------

--
-- Table structure for table `pizza_variations`
--

CREATE TABLE `pizza_variations` (
  `variation_id` int NOT NULL,
  `pizza_id` int NOT NULL,
  `size_id` int NOT NULL,
  `crust_id` int NOT NULL,
  `price` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pizza_variations`
--

INSERT INTO `pizza_variations` (`variation_id`, `pizza_id`, `size_id`, `crust_id`, `price`) VALUES
(1, 1, 1, 1, 179),
(2, 1, 1, 2, 242),
(3, 1, 1, 3, 206),
(4, 1, 2, 1, 224),
(5, 1, 2, 2, 302),
(6, 1, 2, 3, 257),
(7, 1, 3, 1, 269),
(8, 1, 3, 2, 362),
(9, 1, 3, 3, 309),
(10, 1, 4, 1, 313),
(11, 1, 4, 2, 423),
(12, 1, 4, 3, 360),
(13, 2, 1, 1, 179),
(14, 2, 1, 2, 242),
(15, 2, 1, 3, 206),
(16, 2, 2, 1, 224),
(17, 2, 2, 2, 302),
(18, 2, 2, 3, 257),
(19, 2, 3, 1, 269),
(20, 2, 3, 2, 362),
(21, 2, 3, 3, 309),
(22, 2, 4, 1, 313),
(23, 2, 4, 2, 423),
(24, 2, 4, 3, 360),
(25, 3, 1, 1, 199),
(26, 3, 1, 2, 269),
(27, 3, 1, 3, 229),
(28, 3, 2, 1, 249),
(29, 3, 2, 2, 336),
(30, 3, 2, 3, 286),
(31, 3, 3, 1, 299),
(32, 3, 3, 2, 403),
(33, 3, 3, 3, 343),
(34, 3, 4, 1, 348),
(35, 3, 4, 2, 470),
(36, 3, 4, 3, 400),
(37, 4, 1, 1, 279),
(38, 4, 1, 2, 377),
(39, 4, 1, 3, 321),
(40, 4, 2, 1, 349),
(41, 4, 2, 2, 471),
(42, 4, 2, 3, 401),
(43, 4, 3, 1, 419),
(44, 4, 3, 2, 565),
(45, 4, 3, 3, 481),
(46, 4, 4, 1, 488),
(47, 4, 4, 2, 659),
(48, 4, 4, 3, 561),
(49, 5, 1, 1, 339),
(50, 5, 1, 2, 458),
(51, 5, 1, 3, 390),
(52, 5, 2, 1, 424),
(53, 5, 2, 2, 572),
(54, 5, 2, 3, 487),
(55, 5, 3, 1, 509),
(56, 5, 3, 2, 686),
(57, 5, 3, 3, 585),
(58, 5, 4, 1, 593),
(59, 5, 4, 2, 801),
(60, 5, 4, 3, 682),
(61, 6, 1, 1, 279),
(62, 6, 1, 2, 377),
(63, 6, 1, 3, 321),
(64, 6, 2, 1, 349),
(65, 6, 2, 2, 471),
(66, 6, 2, 3, 401),
(67, 6, 3, 1, 419),
(68, 6, 3, 2, 565),
(69, 6, 3, 3, 481),
(70, 6, 4, 1, 488),
(71, 6, 4, 2, 659),
(72, 6, 4, 3, 561),
(73, 7, 1, 1, 309),
(74, 7, 1, 2, 417),
(75, 7, 1, 3, 355),
(76, 7, 2, 1, 386),
(77, 7, 2, 2, 521),
(78, 7, 2, 3, 444),
(79, 7, 3, 1, 464),
(80, 7, 3, 2, 626),
(81, 7, 3, 3, 533),
(82, 7, 4, 1, 541),
(83, 7, 4, 2, 730),
(84, 7, 4, 3, 622),
(85, 8, 1, 1, 339),
(86, 8, 1, 2, 458),
(87, 8, 1, 3, 390),
(88, 8, 2, 1, 424),
(89, 8, 2, 2, 572),
(90, 8, 2, 3, 487),
(91, 8, 3, 1, 509),
(92, 8, 3, 2, 686),
(93, 8, 3, 3, 585),
(94, 8, 4, 1, 593),
(95, 8, 4, 2, 801),
(96, 8, 4, 3, 682),
(97, 9, 1, 1, 339),
(98, 9, 1, 2, 458),
(99, 9, 1, 3, 390),
(100, 9, 2, 1, 424),
(101, 9, 2, 2, 572),
(102, 9, 2, 3, 487),
(103, 9, 3, 1, 509),
(104, 9, 3, 2, 686),
(105, 9, 3, 3, 585),
(106, 9, 4, 1, 593),
(107, 9, 4, 2, 801),
(108, 9, 4, 3, 682),
(109, 10, 1, 1, 339),
(110, 10, 1, 2, 458),
(111, 10, 1, 3, 390),
(112, 10, 2, 1, 424),
(113, 10, 2, 2, 572),
(114, 10, 2, 3, 487),
(115, 10, 3, 1, 509),
(116, 10, 3, 2, 686),
(117, 10, 3, 3, 585),
(118, 10, 4, 1, 593),
(119, 10, 4, 2, 801),
(120, 10, 4, 3, 682),
(121, 11, 1, 1, 339),
(122, 11, 1, 2, 458),
(123, 11, 1, 3, 390),
(124, 11, 2, 1, 424),
(125, 11, 2, 2, 572),
(126, 11, 2, 3, 487),
(127, 11, 3, 1, 509),
(128, 11, 3, 2, 686),
(129, 11, 3, 3, 585),
(130, 11, 4, 1, 593),
(131, 11, 4, 2, 801),
(132, 11, 4, 3, 682),
(133, 12, 1, 1, 279),
(134, 12, 1, 2, 377),
(135, 12, 1, 3, 321),
(136, 12, 2, 1, 349),
(137, 12, 2, 2, 471),
(138, 12, 2, 3, 401),
(139, 12, 3, 1, 419),
(140, 12, 3, 2, 565),
(141, 12, 3, 3, 481),
(142, 12, 4, 1, 488),
(143, 12, 4, 2, 659),
(144, 12, 4, 3, 561),
(145, 13, 1, 1, 279),
(146, 13, 1, 2, 377),
(147, 13, 1, 3, 321),
(148, 13, 2, 1, 349),
(149, 13, 2, 2, 471),
(150, 13, 2, 3, 401),
(151, 13, 3, 1, 419),
(152, 13, 3, 2, 565),
(153, 13, 3, 3, 481),
(154, 13, 4, 1, 488),
(155, 13, 4, 2, 659),
(156, 13, 4, 3, 561),
(157, 14, 1, 1, 279),
(158, 14, 1, 2, 377),
(159, 14, 1, 3, 321),
(160, 14, 2, 1, 349),
(161, 14, 2, 2, 471),
(162, 14, 2, 3, 401),
(163, 14, 3, 1, 419),
(164, 14, 3, 2, 565),
(165, 14, 3, 3, 481),
(166, 14, 4, 1, 488),
(167, 14, 4, 2, 659),
(168, 14, 4, 3, 561),
(169, 15, 1, 1, 279),
(170, 15, 1, 2, 377),
(171, 15, 1, 3, 321),
(172, 15, 2, 1, 349),
(173, 15, 2, 2, 471),
(174, 15, 2, 3, 401),
(175, 15, 3, 1, 419),
(176, 15, 3, 2, 565),
(177, 15, 3, 3, 481),
(178, 15, 4, 1, 488),
(179, 15, 4, 2, 659),
(180, 15, 4, 3, 561),
(181, 16, 1, 1, 279),
(182, 16, 1, 2, 377),
(183, 16, 1, 3, 321),
(184, 16, 2, 1, 349),
(185, 16, 2, 2, 471),
(186, 16, 2, 3, 401),
(187, 16, 3, 1, 419),
(188, 16, 3, 2, 565),
(189, 16, 3, 3, 481),
(190, 16, 4, 1, 488),
(191, 16, 4, 2, 659),
(192, 16, 4, 3, 561),
(193, 17, 1, 1, 239),
(194, 17, 1, 2, 323),
(195, 17, 1, 3, 275),
(196, 17, 2, 1, 299),
(197, 17, 2, 2, 403),
(198, 17, 2, 3, 344),
(199, 17, 3, 1, 359),
(200, 17, 3, 2, 484),
(201, 17, 3, 3, 412),
(202, 17, 4, 1, 418),
(203, 17, 4, 2, 565),
(204, 17, 4, 3, 481),
(205, 18, 1, 1, 239),
(206, 18, 1, 2, 323),
(207, 18, 1, 3, 275),
(208, 18, 2, 1, 299),
(209, 18, 2, 2, 403),
(210, 18, 2, 3, 344),
(211, 18, 3, 1, 359),
(212, 18, 3, 2, 484),
(213, 18, 3, 3, 412),
(214, 18, 4, 1, 418),
(215, 18, 4, 2, 565),
(216, 18, 4, 3, 481),
(229, 1, 1, 1, 179),
(230, 1, 1, 2, 242),
(231, 1, 1, 3, 206),
(232, 1, 2, 1, 224),
(233, 1, 2, 2, 302),
(234, 1, 2, 3, 257),
(235, 1, 3, 1, 269),
(236, 1, 3, 2, 362),
(237, 1, 3, 3, 309),
(238, 1, 4, 1, 313),
(239, 1, 4, 2, 423),
(240, 1, 4, 3, 360);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `role` enum('admin','customer') COLLATE utf8mb4_general_ci NOT NULL,
  `wallet` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `name`, `phone`, `address`, `role`, `wallet`) VALUES
(1, 'john@example.com', '$2y$10$SD.Di58fQ.vV4AO6P9Zxf.3Lz6ImQsjEXA3H18fa0Cy3sv6gd3hqy', 'john', '1234657981', 'deasdasdasd', 'customer', 4307),
(2, 'jane@example.com', '$2y$10$SD.Di58fQ.vV4AO6P9Zxf.3Lz6ImQsjEXA3H18fa0Cy3sv6gd3hqy', 'jane', '1234567890', '2', 'admin', 0),
(3, 'pae@example.com', '$2y$10$SD.Di58fQ.vV4AO6P9Zxf.3Lz6ImQsjEXA3H18fa0Cy3sv6gd3hqy', 'pae', '321321', '111', 'customer', 820970646);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `variation_id` (`variation_id`);

--
-- Indexes for table `pizzas`
--
ALTER TABLE `pizzas`
  ADD PRIMARY KEY (`pizza_id`);

--
-- Indexes for table `pizza_crusts`
--
ALTER TABLE `pizza_crusts`
  ADD PRIMARY KEY (`crust_id`);

--
-- Indexes for table `pizza_sizes`
--
ALTER TABLE `pizza_sizes`
  ADD PRIMARY KEY (`size_id`);

--
-- Indexes for table `pizza_variations`
--
ALTER TABLE `pizza_variations`
  ADD PRIMARY KEY (`variation_id`),
  ADD KEY `pizza_variations_ibfk_1` (`pizza_id`),
  ADD KEY `pizza_variations_ibfk_2` (`size_id`),
  ADD KEY `pizza_variations_ibfk_3` (`crust_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `pizzas`
--
ALTER TABLE `pizzas`
  MODIFY `pizza_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `pizza_crusts`
--
ALTER TABLE `pizza_crusts`
  MODIFY `crust_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pizza_sizes`
--
ALTER TABLE `pizza_sizes`
  MODIFY `size_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pizza_variations`
--
ALTER TABLE `pizza_variations`
  MODIFY `variation_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=241;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`variation_id`) REFERENCES `pizza_variations` (`variation_id`);

--
-- Constraints for table `pizza_variations`
--
ALTER TABLE `pizza_variations`
  ADD CONSTRAINT `pizza_variations_ibfk_1` FOREIGN KEY (`pizza_id`) REFERENCES `pizzas` (`pizza_id`),
  ADD CONSTRAINT `pizza_variations_ibfk_2` FOREIGN KEY (`size_id`) REFERENCES `pizza_sizes` (`size_id`),
  ADD CONSTRAINT `pizza_variations_ibfk_3` FOREIGN KEY (`crust_id`) REFERENCES `pizza_crusts` (`crust_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
