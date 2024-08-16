let tax = 15;
let data = [];
// checkoutBtn Event
document.getElementById("checkoutBtn").addEventListener("click", () => {
    // if no item
    if (data.length == 0) {
        // SWL 2 Echo Status
        Swal.fire({
            icon: "error",
            title: "No Item Yet",
            showConfirmButton: false,
            timer: 1500
        })
    } else {
        // goto checkout.html
        window.location.href = "checkout.php";
    }
})

// // Event Amount
function eventAmount() {
    const increase = document.querySelectorAll("#increase")
    increase.forEach((button) => {
        button.addEventListener("click", () => {
            const amount = parseInt(button.previousElementSibling.value);
            button.previousElementSibling.value = amount + 1;
            updateAmount(button.previousElementSibling)
        })
    })

    const decrease = document.querySelectorAll("#decrease")
    decrease.forEach((button) => {
        button.addEventListener("click", () => {
            const amount = parseInt(button.nextElementSibling.value);
            if (amount <= 1) {
                button.parentElement.previousElementSibling.querySelector(".remove").click()
                return
            };
            button.nextElementSibling.value = amount - 1
            updateAmount(button.nextElementSibling)
        })
    })
}

// // calC
function CalcOrder() {
    let total = 0
    data.forEach((productData) => {
        total += productData.quantity * productData.pizzaPrice
    })

    // set total
    document.querySelectorAll(".total").forEach((element) => {
        element.innerHTML = `฿${total.toFixed(0)}`
    })

    // set countItem class
    document.querySelectorAll(".countItem").forEach((countItem) => {
        countItem.innerHTML = `${data.length} Items`
    })

    // set tax
    document.querySelectorAll(".tax").forEach((taxElement) => {
        taxElement.innerHTML = `฿${(tax).toFixed(0)}`
    })

    // set grandTotal
    document.querySelectorAll(".grandTotal").forEach((grandTotal) => {
        grandTotal.innerHTML = `฿${(total + tax).toFixed(0)}`
        console.log(total, tax)
    })
}

// // Init
async function init() {
    const resp = await getData();
    data = resp['items']

    eventRemove()
    eventAmount()

    // set tax
    tax = parseInt(document.querySelector('.tax').innerHTML.split(" ")[1]);

    CalcOrder();
}
init();