// get id from parm ?id=
const urlParams = new URLSearchParams(window.location.search);
const pid = urlParams.get("id");

///// ======= Global ======= /////
let data = [];

async function getData() {
  const resp = await fetch(
    "services/Action.php?action=getPizza&pizza_id=" + pid
  )
    .then((response) => response.json())
    .then((resp) => {
      return resp;
    });

  return resp;
}

// Functions
function clearSelectFilter(buttons) {
  buttons.forEach((button) => {
    button.classList.remove("bg-green-500");
    button.classList.remove("text-white");
    button.classList.add("text-black");
  });
}

// Calc
function Calc() {
  // check data
  if (data.length === 0) return;

  const quantity = parseInt(document.getElementById("quantity").value);
  const size = document
    .getElementById("sizeSelection")
    .querySelector(".bg-green-500").id;
  const crust = document
    .getElementById("crustSelection")
    .querySelector(".bg-green-500").id;

  // Find the pizza variation object with the matching pizza_id and size_id
  const pizzaVariation = data["pizza_variations"].find(
    (variation) =>
      variation.size_id === parseInt(size) &&
      variation.crust_id === parseInt(crust)
  );

  // Calculate the total price
  const total = parseFloat(pizzaVariation.price * quantity);

  // Set
  document.getElementById("pricePizza").innerText = `฿${pizzaVariation.price}`;

  // setID
  document.querySelector(".AddToCart").id = pizzaVariation.variation_id;
  document.querySelector(".AddToCart").innerText = `Add to Cart (฿${total})`;
}

// Event Listen
// SizeButtons
const SizeButtons = document
  .getElementById("sizeSelection")
  .querySelectorAll("button");
SizeButtons.forEach((button) => {
  button.addEventListener("click", (e) => {
    e.preventDefault();
    clearSelectFilter(SizeButtons);
    button.classList.add("bg-green-500");
    button.classList.remove("text-black");
    button.classList.add("text-white");
    Calc();
  });
});

// CurstButtons
const CurstButtons = document
  .getElementById("crustSelection")
  .querySelectorAll("button");
CurstButtons.forEach((button) => {
  button.addEventListener("click", (e) => {
    e.preventDefault();
    clearSelectFilter(CurstButtons);
    button.classList.add("bg-green-500");
    button.classList.remove("text-black");
    button.classList.add("text-white");
    Calc();
  });
});

// Input Quantity
document.getElementById("quantity").addEventListener("input", () => {
  Calc();
});

// init
async function init() {
  data = await getData();
  Calc();
  const btn = document.querySelector(".AddToCart");
  btn.removeAttribute("disabled");
}
init();
