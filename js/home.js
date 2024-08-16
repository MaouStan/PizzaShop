///// ======= Global ======= /////
let data = [];

async function getData() {
  const resp = await fetch("services/Action.php?action=getPizzas")
    .then((response) => response.json())
    .then((resp) => {
      return resp;
    });

  return resp;
}

///// ======= FIlter Functions ======= /////
// Search
function search() {
  // search id
  const search = document.getElementById("search").value.toLowerCase();

  // filter
  const filter = data.filter((product) => {
    return (
      product.name.toLowerCase().includes(search) ||
      product.description.toLowerCase().includes(search)
    );
  });

  // show
  show(filter);
}

function clearSelectFilter(buttons) {
  buttons.forEach((button) => {
    button.classList.remove("bg-green-500");
    button.classList.remove("text-white");
    button.classList.add("text-black");
  });
}

// Calc
function Calc() {
  const pid = document
    .getElementById("AddOverlay")
    .querySelector(".selectID").id;
  const quantity = parseInt(
    document.getElementById("quantityNumber").innerText
  );
  const size = document
    .getElementById("sizeSelection")
    .querySelector(".bg-green-500").id;
  const crust = document
    .getElementById("crustSelection")
    .querySelector(".bg-green-500").id;

  // Find the pizza variation object with the matching pizza_id and size_id
  const pizzaVariation = data
    .find((pizza) => pizza.pizza_id === pid)
    .pizza_variations.find(
      (variation) =>
        variation.size_id === parseInt(size) &&
        variation.crust_id === parseInt(crust)
    );

  // Calculate the total price
  const total = parseFloat(pizzaVariation.price * quantity).toFixed(2);

  // Set the total price in the HTML
  document.getElementById("selectPricePizza").innerText = `฿ ${total}`;

  // setID
  document.querySelector(".AddToCart").id = pizzaVariation.variation_id;
}

///// ======= Events ======= /////
// OrderNow Button Scroll to OurMenu
document.getElementById("OrderNow").addEventListener("click", () => {
  // Scroll to OurMenu ID
  document.getElementById("OurMenu").scrollIntoView({
    behavior: "smooth",
  });
});

// btnFilter FilterOverlay
// document.getElementById("btnFilter").addEventListener("click", () => {
//     if (document.getElementById("FilterOverlay").classList.contains("hidden")) {
//         // remove class "hidden"
//         document.querySelector("body").classList.add("overflow-hidden")
//         document.getElementById("FilterOverlay").classList.remove("hidden")
//     } else {
//         document.querySelector("body").classList.remove("overflow-hidden")
//         document.getElementById("FilterOverlay").classList.add("hidden")
//     }
// });

function sortBasedOnOrderDrop(a, b, option) {
  switch (option) {
    case "priceASC":
      return a.base_price - b.base_price;
    case "priceDESC":
      return b.base_price - a.base_price;
    case "nameASC":
      return a.name.localeCompare(b.name);
    case "nameDESC":
      return b.name.localeCompare(a.name);
    default:
      return a.pizza_id - b.pizza_id;
  }
}

function filter() {
  const orderDrop = document.getElementById("orderDrop");
  const mostsell = document.getElementById("MostSell");

  const mostSellOn = mostsell.classList.contains("bg-green-500");

  let sortedData = [...data];

  sortedData.sort((a, b) => {
    if (mostSellOn) {
      if (a.total_sold === b.total_sold) {
        return sortBasedOnOrderDrop(a, b, orderDrop.value);
      } else {
        return b.total_sold - a.total_sold;
      }
    } else {
      return sortBasedOnOrderDrop(a, b, orderDrop.value);
    }
  });

  show(sortedData);
}

// Filter
// MostSell btn
document.getElementById("MostSell").addEventListener("click", (e) => {
  e.preventDefault();
  // element
  const element = e.target;
  // check toggle
  if (element.classList.contains("bg-green-500")) {
    element.classList.remove("bg-green-500");
    element.classList.remove("text-white");
    // add black white
    element.classList.add("text-black");
    element.classList.add("bg-white");
  } else {
    // remove
    element.classList.remove("bg-white");
    element.classList.remove("text-black");
    // add
    element.classList.add("bg-green-500");
    element.classList.add("text-white");
  }
  filter();
});

// orderDrop onChange
document.getElementById("orderDrop").addEventListener("change", (e) => {
  filter();
});

// Close FilterOverlay
document.getElementById("CloseFilterOverlay").addEventListener("click", () => {
  document.querySelector("body").classList.remove("overflow-hidden");
  document.querySelector("body").classList.remove("max-h-screen");
  document.getElementById("FilterOverlay").classList.add("hidden");
});

// CloseAddOverlay
document.getElementById("CloseAddOverlay").addEventListener("click", () => {
  document.querySelector("body").classList.remove("overflow-hidden");
  document.querySelector("body").classList.remove("max-h-screen");
  document.getElementById("AddOverlay").classList.add("hidden");
});

// Input On id "search"
document.getElementById("search").addEventListener("input", search);

// const sortButtons = document.getElementById("FilterSorts").querySelectorAll("button");
// sortButtons.forEach((button) => {
//     button.addEventListener("click", () => {
//         clearSelectFilter(sortButtons)
//         button.classList.add("bg-green-500");
//         button.classList.add("text-white");
//     });
// });

// const OrderButtons = document.getElementById("FilterOrders").querySelectorAll("button");
// OrderButtons.forEach((button) => {
//     button.addEventListener("click", () => {
//         clearSelectFilter(OrderButtons)
//         button.classList.add("bg-green-500");
//         button.classList.add("text-white");
//     });
// });

const SizeButtons = document
  .getElementById("sizeSelection")
  .querySelectorAll("button");
SizeButtons.forEach((button) => {
  button.addEventListener("click", () => {
    clearSelectFilter(SizeButtons);
    button.classList.add("bg-green-500");
    button.classList.add("text-white");
    Calc();
  });
});

const CurstButtons = document
  .getElementById("crustSelection")
  .querySelectorAll("button");
CurstButtons.forEach((button) => {
  button.addEventListener("click", () => {
    clearSelectFilter(CurstButtons);
    button.classList.add("bg-green-500");
    button.classList.add("text-white");
    Calc();
  });
});

// quantity
// quantityMinus for quantityNumber quantityPlus
document.getElementById("quantityMinus").addEventListener("click", () => {
  const quantityNumber = document.getElementById("quantityNumber");
  const quantity = parseInt(quantityNumber.innerText);
  if (quantity > 1) {
    quantityNumber.innerText = quantity - 1;
  }
  Calc();
});

document.getElementById("quantityPlus").addEventListener("click", () => {
  const quantityNumber = document.getElementById("quantityNumber");
  const quantity = parseInt(quantityNumber.innerText);
  quantityNumber.innerText = quantity + 1;
  Calc();
});

// ///// ======= Add to Cart ======= /////
// // Add to Cart
// function addToCart(variation_id) {
//   const formData = new FormData();
//   formData.append("user_id", sessionStorage.getItem("user_id"));
//   formData.append("order_id", sessionStorage.getItem("order_id"));
//   formData.append("variation_id", variation_id);

//   fetch("php/Action.php?action=addToCart", {
//     method: "POST",
//     body: formData,
//   })
//     .then((response) => response.json())
//     .then((resp) => {
//       // SWL 2 Echo Status
//       Swal.fire({
//         icon: resp.status,
//         title: resp.message,
//         showConfirmButton: false,
//         timer: 1500,
//       });

//       // close overlay
//       document.querySelector("body").classList.remove("overflow-hidden");
//       document.querySelector("body").classList.remove("max-h-screen");
//       document.getElementById("AddOverlay").classList.add("hidden");

//       // getCount Items Cart
//       async function getCount() {
//         const formData = new FormData();
//         formData.append("user_id", sessionStorage.getItem("user_id"));
//         formData.append("order_id", sessionStorage.getItem("order_id"));

//         const resp = await fetch("php/Action.php?action=getCartCount", {
//           method: "POST",
//           body: formData,
//         });
//         return resp.text();
//       }

//       // init getCount
//       async function initCount() {
//         const resp = await getCount();
//         document.getElementById("cart_number").innerHTML = resp ? resp : 0;
//       }

//       initCount();
//     });
// }

// // Add to Cart Event
// document.querySelector(".AddToCart").addEventListener("click", () => {
//   const variation_id = document.querySelector(".AddToCart").id;

//   addToCart(variation_id);
// });

///// ======= Our Menu Product ======= /////
// Product
function Product(pid, name, price, picture) {
  return `
        <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl duration-500 transform-gpu">
            <div>
                <!-- Detail -->
                <img src="${picture}"
                    alt="Margherita Pizza" class="w-full h-48 object-contain rounded">
                <h3 class="text-md font-semibold mt-4 overflow-hidden h-12 text-center flex items-center w-full">${name}</h3>
            </div>
                <!-- Btn Add -->
            <div class="w-full flex items-center justify-center mt-4">
                <a href="pizza.php?id=${pid}"
                    class="w-full bg-green-500 text-center hover:bg-green-600 text-white py-2 px-6 rounded-full text-lg font-semibold transition duration-300 ease-in-out transform">
                    ฿${price} +
                </a>
            </div>
        </div>
    `;
}

// Show
function show(filter) {
  const products = document.getElementById("products");
  products.innerHTML = "";
  filter.forEach((product) => {
    products.innerHTML += Product(
      product.pizza_id,
      product.name,
      product["base_price"],
      product.picture
    );
  });

  //   // Event
  //   filter.forEach((product) => {
  //     // Event For Product to Product?id=
  //     document
  //       .getElementById(`Product_${product.pizza_id}`)
  //       .addEventListener("click", () => {
  //         window.location.href = `pizza.php?id=${product.pizza_id}`;
  //       });

  //     // Event for Add Button
  //     document
  //       .getElementById(`Add_${product.pizza_id}`)
  //       .addEventListener("click", () => {
  //         if (
  //           document.getElementById("AddOverlay").classList.contains("hidden")
  //         ) {
  //           // Set selectID
  //           document.querySelector(".selectID").id = `${product.pizza_id}`;
  //           // Set selectName
  //           document.getElementById(
  //             "selectNamePizza"
  //           ).innerText = `${product.name}`;
  //           // Set selectImage
  //           document.getElementById("selectImgPizza").src = `${product.picture}`;
  //           Calc(product.pizza_id);
  //           // remove class "hidden"
  //           document.querySelector("body").classList.add("overflow-hidden");
  //           document.getElementById("AddOverlay").classList.remove("hidden");
  //         } else {
  //           document.querySelector("body").classList.remove("overflow-hidden");
  //           document.getElementById("AddOverlay").classList.add("hidden");
  //         }
  //       });
  //   });
}

// Init
async function init() {
  const resp = await getData();
  data = resp;
}
init();
