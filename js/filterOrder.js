let data = []
let isAdmin = false
// if admin.php page
if (window.location.pathname.toLowerCase() == "/maoushop/admin.php") {
    isAdmin = true
}

function simpleOrder(id, timeStamp, total, status) {
    statusColor = "";
    statusText = "";
    statusBtn = "See Detail";
    switch (status) {
        case 0:
            statusColor = "text-yellow-500";
            statusText = "In Cart";
            break;
        case 1:
            statusColor = "text-orange-500";
            statusText = "No Pay";
            statusBtn = "Pay";
            break;
        case 2:
            statusColor = "text-red-500";
            statusText = "No Delivery";
            break;
        case 3:
            statusColor = "text-green-500";
            statusText = "Delivered";
            break;
        default:
            statusColor = "text-gray-500";
            break;
    }
    return `
    <!-- Sample Order Item -->
    <div class="w-full border-b border-gray-200 p-4 flex items-start">
        <!-- Order Details -->
        <div class="w-3/4 p-4">
            <h2 class="text-xl font-semibold">Order #${id}</h2>
            <p class="text-gray-600">Date: ${timeStamp}</p>
            <p class="text-gray-600">Status: <span class="${statusColor}">${statusText}</span></p>
            <p class="text-gray-600">Total: $${total}</p>
        </div>
        <!-- Button to See More Details -->
        <div class="relative w-1/3 h-full flex   items-center justify-end">
            <!-- see detail button -->
            <button
                class="relative w-32 h-12 flex items-center justify-center bg-green-600 hover:bg-green-500 rounded-md shadow-md text-white font-bold text-lg"
                onclick="window.location.href='order.php?id=${id}'"
                id="${id}">
                ${statusBtn}
            </button>
        </div>
    </div>
    `;
}

async function AdminProductOrder(id, user_name, timeStamp, total, status, items) {
    return `
    <div id="ORDER_${id}" class="flex-none w-full h-80 bg-gray-100 flex items-center justify-center border-4 overflow-hidden">
        <div class="w-64 h-full text-xs space-y-2 bg-gray-100 flex flex-col items-start justify-start p-4 border-r-4">
            <p class="font-bold">Order ID : <span class="font-medium">#${id}</span></p>
            <p class="font-bold">Name : <span class="font-medium">${user_name}</span></p>
            <p class="font-bold">Total : <span class="font-medium">฿${total}</span></p>
            <p class="font-bold">Time : <span class="font-medium">${timeStamp}</span></p>
            <label for="status" class="font-bold">Status:
                <select class="font-medium" id="status" name="status">
                    <option value="2"${status == 2 ? ' selected' : ''}>No Delivery</option>
                    <option value="3"${status == 3 ? ' selected' : ''}>Delivered</option>
                </select>
            </label>
            <button id="Save_${id}" disabled class="w-full disabled:bg-gray-400 mx-auto rounded-md text-white text-center bg-green-400 p-2 hover:bg-green-300">
                Save Change
            </button>
            <button class="w-full mx-auto rounded-md text-white text-center bg-yellow-400 p-2 hover:bg-green-300"
            onclick="window.location.href='order.php?id=${id}'"
            >
                See More
            </button>
        </div>

        <!-- scroll x-axis able side -->
        <div class="min-w-0 w-full max-w-full h-full overflow-x-auto flex">
        ${
            // Generate multiple card components
            items.map(orderDetail => {
                const pizza_name = orderDetail['pizzaName'];
                const pizza_img = orderDetail['pizzaImage'];
                const pizza_size = orderDetail['pizzaSize'];
                const pizza_crust = orderDetail['pizzaCrust'];
                const pizza_amount = orderDetail['quantity'];
                return generateCard(pizza_name, pizza_img, pizza_size, pizza_crust, pizza_amount);
            }).join('')
        }
        </div>
    </div>`;
}

// Function to generate a card component
function generateCard(pizza_name, pizza_img, pizza_size, pizza_crust, pizza_amount) {
    return `
    <div class="flex-none min-w-52 max-w-52 h-auto min-h-full bg-green-100 p-4 overflow-hidden border-2">
        <div class="flex flex-col justify-between space-y-2">
            <div class="w-full h-2/3">
                <img class="w-48 h-48 object-contain" src="${pizza_img}" alt="${pizza_name}">
            </div>
            <div class="w-full min-h-1/3 text-xs h-full flex flex-col items-center space-y-2">
                <p class="font-bold w-48 break-all line-clamp-2">${pizza_name}</p>
                <div class="w-full h-8 flex items-center justify-center bg-green-400 rounded-md text-white text-center">
                    <p class="font-bold border-r px-2">${pizza_size}</p>
                    <p class="font-bold border-r px-2">${pizza_crust}</p>
                    <p class="font-bold px-2">x${pizza_amount}</p>
                </div>
            </div>
        </div>
    </div>`;
}

// map to orderList
async function mapOrderList() {
    // temp data to filter status
    const statusSelect = document.getElementById("status").value;
    const tempData = statusSelect === 'all' ? data : data.filter((order) => {
        return order.order_status == statusSelect
    })


    const timeSelect = document.getElementById("time");
    const timeOrder = timeSelect.value === "ASC" ? 1 : -1;
    tempData.sort((a, b) => {
        if (a.order_time < b.order_time) {
            return -1 * timeOrder;
        } else if (a.order_time > b.order_time) {
            return 1 * timeOrder;
        } else {
            return 0;
        }
    });

    let orderList = document.getElementById("orderList");
    orderList.innerHTML = "";

    let addText = []

    tempData.forEach(async (order) => {
        if (isAdmin) {
            addText.push(
                await AdminProductOrder(order.order_id, order.name, order.order_time, order.total_price, parseInt(order.order_status), order.items)
            )
        } else {
            addText.push(
                simpleOrder(order.order_id, order.order_time, order.total_price, parseInt(order.order_status))
            )
        }
        orderList.innerHTML = addText.join('')
    });

    // wait
    await new Promise(resolve => setTimeout(resolve, 200));

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
                    });
                }
            })
        })
    })

}


async function init() {
    const resp = await getData();
    data = resp['orders']

    // onchange select filter status
    document.getElementById("status").addEventListener("change", mapOrderList);

    // onchange select filter time
    document.getElementById("time").addEventListener("change", mapOrderList);

}
init()