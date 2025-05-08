const orderBody = document.getElementById("order-body");
const summary = document.getElementById("summary");
let orderList = {};

function addToOrder(product) {
  if (orderList[product.name]) {
    orderList[product.name].qty += 1;
  } else {
    orderList[product.name] = { ...product, qty: 1 };
  }
  renderOrder();
}

function renderOrder() {
  orderBody.innerHTML = "";
  let subtotal = 0;

  for (const key in orderList) {
    const item = orderList[key];
    const total = item.qty * item.price;
    subtotal += total;

    const row = document.createElement("tr");
    row.innerHTML = `
      <td><img src="${item.image}" width="40"></td>
      <td>${item.name}</td>
      <td>$${item.price.toFixed(2)}</td>
      <td>${item.qty}</td>
      <td>$${total.toFixed(2)}</td>
      <td>
        <button onclick="decreaseQty('${key}')">−</button>
        <button onclick="increaseQty('${key}')">+</button>
      </td>
    `;
    orderBody.appendChild(row);
  }

  if (Object.keys(orderList).length === 0) {
    orderBody.innerHTML = `<tr id="empty-row"><td colspan="6" class="empty">Empty List (Select Product)</td></tr>`;
  }

  const discount = 1.0;
  const finalTotal = subtotal - discount;

  summary.innerHTML = `
    <p>Subtotal: <strong>$${subtotal.toFixed(2)}</strong></p>
    <p>Discount: <strong>$${discount.toFixed(2)}</strong></p>
    <p>Total: <strong>$${finalTotal.toFixed(2)}</strong></p>
  `;
}

function increaseQty(name) {
  orderList[name].qty += 1;
  renderOrder();
}

function decreaseQty(name) {
  if (orderList[name].qty > 1) {
    orderList[name].qty -= 1;
  } else {
    delete orderList[name];
  }
  renderOrder();
}

function printOrder() {
  window.print();
}

function createOrder() {
  alert("🧾 Order Created!");
}

function goBack() {
  window.history.back();
}
