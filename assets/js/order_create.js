console.log('order_create.js: Script parsing started.'); // Log 1

let orderList = {};
let orderListModal = {};

// DOM Elements - these will be null if script runs before DOM is ready for elements not on every page
// We will assign critical ones within DOMContentLoaded
const productGrid = document.getElementById("product-grid"); // Might be null on orders/index
const categoryTabs = document.querySelector(".category-tabs"); // Might be null on orders/index
const searchInput = document.querySelector(".search-bar input"); // Might be null on orders/index

// Elements for the main order creation form (left panel)
let orderBody, summary, tableSelect, waiterSelect, selectedTableIdHidden, selectedWaiterIdHidden;

// Elements for the modal
let editOrderModal, modalBillNo, modalOrderIdInput, modalTableSelect,
    modalWaiterSelect, modalPaidStatusSelect, modalOrderBody, modalSummaryDiv,
    modalProductSearchInput, modalProductSearchResultsDiv;

var base_url = '/RestaurantMS_CI/';
if (typeof window.base_url_php !== 'undefined') {
    base_url = window.base_url_php;
}

// --- Main Order Functions ---
function addToOrder(productData) {
  const productId = productData.id;
  if (orderList[productId]) {
    orderList[productId].qty += 1;
  } else {
    orderList[productId] = { ...productData, qty: 1 };
  }
  renderOrder();
}

function increaseQty(productId) {
  if (orderList[productId]) {
    orderList[productId].qty += 1;
    renderOrder();
  }
}

function decreaseQty(productId) {
  if (orderList[productId]) {
    if (orderList[productId].qty > 1) {
      orderList[productId].qty -= 1;
    } else {
      delete orderList[productId];
    }
    renderOrder();
  }
}

function renderOrder() {
  if (!orderBody) { // Check if orderBody is assigned
      console.warn("renderOrder: orderBody element not found. Skipping render for main order list.");
      return;
  }
  orderBody.innerHTML = "";
  let subtotal = 0;

  for (const productId in orderList) {
    const item = orderList[productId];
    const total = item.qty * item.price;
    subtotal += total;

    const row = document.createElement("tr");
    row.innerHTML = `
      <td><img src="${item.image}" alt="${item.name}" width="40" height="40" style="object-fit: cover;"></td>
      <td>${item.name}</td>
      <td>${parseFloat(item.price).toFixed(2)}</td>
      <td>${item.qty}</td>
      <td>${total.toFixed(2)}</td>
      <td>
        <button type="button" onclick="decreaseQty('${productId}')" class="btn btn-sm btn-warning">&minus;</button>
        <button type="button" onclick="increaseQty('${productId}')" class="btn btn-sm btn-success">+</button>
      </td>
    `;
    orderBody.appendChild(row);
  }

  if (Object.keys(orderList).length === 0) {
    orderBody.innerHTML = `<tr id="empty-row"><td colspan="6" class="empty">Empty List (Select Product)</td></tr>`;
  }
  renderSummary(subtotal);
}

function renderSummary(subtotal) {
    const discount = 0.00;
    const summaryEl = document.getElementById("summary"); // Get it here as it might not be global yet
    if (!summaryEl) {
        console.warn("renderSummary: summary element not found. Skipping render for main summary.");
        return;
    }

    const serviceChargeRate = parseFloat(summaryEl.dataset.serviceCharge || window.companyServiceCharge || 0) / 100;
    const vatRate = parseFloat(summaryEl.dataset.vatCharge || window.companyVatCharge || 0) / 100;

    const grossAmount = subtotal - discount;
    const serviceCharge = grossAmount * serviceChargeRate;
    const vatAmount = grossAmount * vatRate;
    const netTotal = grossAmount + serviceCharge + vatAmount;

    summaryEl.innerHTML = `
        <p>Subtotal: <strong id="summary-subtotal">${subtotal.toFixed(2)}</strong></p>
        <p>Discount: <strong id="summary-discount">${discount.toFixed(2)}</strong></p>
        <hr style="margin: 5px 0;">
        <p>Net Total: <strong id="summary-net-total" style="font-size: 1.1em;">${netTotal.toFixed(2)}</strong></p>
    `;

    const grossAmountInput = document.getElementById('gross_amount_value');
    const serviceChargeRateInput = document.getElementById('service_charge_rate_value');
    const serviceChargeValueInput = document.getElementById('service_charge_value');
    const vatChargeRateInput = document.getElementById('vat_charge_rate_value');
    const vatChargeValueInput = document.getElementById('vat_charge_value');
    const netAmountInput = document.getElementById('net_amount_value');
    const discountInput = document.getElementById('discount_value');

    if (grossAmountInput) grossAmountInput.value = grossAmount.toFixed(2);
    if (serviceChargeRateInput) serviceChargeRateInput.value = (serviceChargeRate * 100).toFixed(2);
    if (serviceChargeValueInput) serviceChargeValueInput.value = serviceCharge.toFixed(2);
    if (vatChargeRateInput) vatChargeRateInput.value = (vatRate * 100).toFixed(2);
    if (vatChargeValueInput) vatChargeValueInput.value = vatAmount.toFixed(2);
    if (netAmountInput) netAmountInput.value = netTotal.toFixed(2);
    if (discountInput) discountInput.value = discount.toFixed(2);
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('order_create.js: DOMContentLoaded event fired.'); // Log 2

    // Assign DOM elements that are specific to create/edit pages or modal
    orderBody = document.getElementById("order-body");
    summary = document.getElementById("summary");
    // tableSelect and waiterSelect are no longer dropdowns for the main form
    selectedTableIdHidden = document.getElementById("selected_table_id_hidden");
    selectedWaiterIdHidden = document.getElementById("selected_waiter_id_hidden");

    editOrderModal = document.getElementById("editOrderModal");
    modalBillNo = document.getElementById("modal-bill-no");
    modalOrderIdInput = document.getElementById("order_id_modal");
    modalTableSelect = document.getElementById("modal_table_name");
    modalWaiterSelect = document.getElementById("modal_waiter_id");
    modalPaidStatusSelect = document.getElementById("modal_paid_status");
    modalOrderBody = document.getElementById("modal-order-body");
    modalSummaryDiv = document.getElementById("modal-summary");
    modalProductSearchInput = document.getElementById("modal_product_search");
    modalProductSearchResultsDiv = document.getElementById("modal_product_search_results");

    if (productGrid) {
        productGrid.addEventListener('click', (event) => {
            const card = event.target.closest('.product-card');
            if (card && card.dataset.productId) {
                const productData = {
                    id: card.dataset.productId,
                    name: card.dataset.productName,
                    price: parseFloat(card.dataset.productPrice),
                    image: card.querySelector('img')?.src || ''
                };
                addToOrder(productData);
            }
        });
    }

    if (categoryTabs) {
        categoryTabs.addEventListener('click', (event) => {
            if (event.target.classList.contains('tab')) {
                event.preventDefault();
                categoryTabs.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
                event.target.classList.add('active');
                const categoryId = event.target.dataset.categoryId;
                filterProductsClientSide(null, categoryId);
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', (event) => {
            const searchTerm = event.target.value.toLowerCase();
            filterProductsClientSide(searchTerm);
        });
    }

    // Initial render for create page or if edit page has no initial items (handled by inline script for edit)
    if (typeof initialOrderItems === 'undefined' || Object.keys(initialOrderItems).length === 0) {
        if(orderBody) renderOrder(); // Only if orderBody exists (on create/edit page)
    }


    const tableButtonsContainer = document.getElementById('table-selection-buttons');
    if (tableButtonsContainer) {
        tableButtonsContainer.addEventListener('click', function(event) {
            const clickedButton = event.target.closest('.table-button');
            if (clickedButton) {
                const tableStatus = clickedButton.dataset.tableStatus;
                console.log("Selected table status:", tableStatus);
                tableButtonsContainer.querySelectorAll('.table-button').forEach(btn => btn.classList.remove('selected'));
                clickedButton.classList.add('selected');
                if (selectedTableIdHidden) selectedTableIdHidden.value = clickedButton.dataset.tableId;
            }
        });
    }

    const waiterButtonsContainer = document.getElementById('waiter-selection-buttons');
    if (waiterButtonsContainer) {
        waiterButtonsContainer.addEventListener('click', function(event) {
            const clickedButton = event.target.closest('.waiter-button');
            if (clickedButton) {
                waiterButtonsContainer.querySelectorAll('.waiter-button').forEach(btn => btn.classList.remove('selected'));
                clickedButton.classList.add('selected');
                if (selectedWaiterIdHidden) selectedWaiterIdHidden.value = clickedButton.dataset.waiterId;
            }
        });
    }
    
    if (modalProductSearchInput) {
        modalProductSearchInput.addEventListener('input', function(event) {
            const searchTerm = event.target.value.trim();
            searchProductsForModal(searchTerm);
        });
    }
});

function createOrder(event) {
  const orderData = {
    waiter_id: selectedWaiterIdHidden ? selectedWaiterIdHidden.value : null,
    table_id: selectedTableIdHidden ? selectedTableIdHidden.value : null,
    items: orderList,
  };

  if (!orderData.table_id || !orderData.waiter_id || Object.keys(orderData.items).length === 0) {
    alert("Please select a table, assign a waiter, and add items to the order.");
    return;
  }

  const createButton = event.target;
  if (createButton) createButton.disabled = true;

  let formActionUrl = '';
  const orderFormForAction = document.getElementById('order-form');
  if (orderFormForAction) {
    formActionUrl = orderFormForAction.action;
  } else {
    console.error("Order form not found for createOrder action.");
    if (createButton) createButton.disabled = false;
    return;
  }

  fetch(formActionUrl, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: JSON.stringify(orderData)
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert('Order Created Successfully!');
      window.location.reload();
    } else {
      alert('Error creating order: ' + (data.message || 'Unknown error'));
    }
  }).finally(() => {
    if (createButton) createButton.disabled = false;
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred while creating the order.');
    if (createButton) createButton.disabled = false;
  });
}

function goBack() {
  window.history.back();
}

function submitOrderForUpdate() { // This is for the main edit page (orders/edit), not the modal
    const orderForm = document.getElementById('order-form');
    if (!orderForm) {
        alert("Error: Order form not found for update.");
        return;
    }

    const existingItemsContainer = orderForm.querySelector('.temp-items-container');
    if (existingItemsContainer) {
        existingItemsContainer.remove();
    }

    const itemsContainer = document.createElement('div');
    itemsContainer.style.display = 'none';
    itemsContainer.classList.add('temp-items-container');

    let itemIndex = 0;
    // For the main edit page, orderList is used.
    for (const productId in orderList) {
        const item = orderList[productId];
        ['product', 'qty', 'rate_value', 'amount_value'].forEach(fieldName => {
            const input = document.createElement('input');
            input.type = 'hidden';
            if (fieldName === 'product') {
                input.name = `product[${itemIndex}]`;
                input.value = item.id;
            } else if (fieldName === 'qty') {
                input.name = `qty[${itemIndex}]`;
                input.value = item.qty;
            } else if (fieldName === 'rate_value') {
                input.name = `rate_value[${itemIndex}]`;
                input.value = item.price.toFixed(2);
            } else if (fieldName === 'amount_value') {
                input.name = `amount_value[${itemIndex}]`;
                input.value = (item.qty * item.price).toFixed(2);
            }
            itemsContainer.appendChild(input);
        });
        itemIndex++;
    }
    orderForm.appendChild(itemsContainer);

    if (Object.keys(orderList).length === 0 && !confirm("The order list is empty. Do you want to proceed? This might clear all items.")) {
        itemsContainer.remove();
        return;
    }
    orderForm.submit();
}

function filterProductsClientSide(searchTerm = null, categoryId = null) {
    if (!productGrid) return; // productGrid might not exist on orders/index
    const cards = productGrid.querySelectorAll('.product-card');

    if (categoryId === null && categoryTabs) { // categoryTabs might not exist on orders/index
        const activeTab = categoryTabs.querySelector('.tab.active');
        if (activeTab) {
            categoryId = activeTab.dataset.categoryId;
        }
    }

    cards.forEach(card => {
        const productName = card.dataset.productName?.toLowerCase() || '';
        const productCategory = card.dataset.categoryId || 'all';
        let showCard = true;

        if (searchTerm !== null && !productName.includes(searchTerm)) {
            showCard = false;
        }
        if (categoryId !== null && categoryId !== 'all' && productCategory !== categoryId) {
            showCard = false;
        }
        card.style.display = showCard ? '' : 'none';
    });
}

// --- Modal Functionality ---
function openEditOrderModal(orderId) {
  if (!orderId || !editOrderModal) {
      console.error("openEditOrderModal: orderId or editOrderModal element is missing.");
      return;
  }
  console.log("order_create.js: openEditOrderModal called with ID:", orderId);

  orderListModal = {};
  renderOrderModal();
  
  const modalForm = document.getElementById('edit-order-form-modal');
  if (modalForm) modalForm.reset();
  
  if (modalOrderIdInput) {
      modalOrderIdInput.value = orderId;
  } else {
      console.error("modalOrderIdInput is null.");
      return;
  }

  if (modalForm) modalForm.action = `${base_url}orders/update/${orderId}`;

  fetch(`${base_url}orders/get_order_details_json/${orderId}`, {
      method: 'GET',
      headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
      }
  })
      .then(response => {
          if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
          return response.json();
      })
      .then(data => {
          if (data.success && data.order_data) {
              const order = data.order_data;
              if (modalBillNo) modalBillNo.textContent = order.bill_no || 'N/A';
              if (modalTableSelect) modalTableSelect.value = order.table_id || '';
              if (modalWaiterSelect) modalWaiterSelect.value = order.waiter_id || '';
              if (modalPaidStatusSelect) modalPaidStatusSelect.value = order.paid_status || '2';

              if (data.order_items && Array.isArray(data.order_items)) {
                  data.order_items.forEach(item => {
                      orderListModal[item.product_id] = {
                          id: item.product_id,
                          name: item.product_name,
                          price: parseFloat(item.rate),
                          image: item.product_image ? `${base_url}${item.product_image}` : '',
                          qty: parseInt(item.qty)
                      };
                  });
              }
              renderOrderModal();
              editOrderModal.style.display = "block";
          } else {
              alert("Error fetching order details: " + (data.message || "Unknown error"));
          }
      })
      .catch(error => {
          console.error("Error fetching order details:", error);
          alert("Failed to fetch order details. " + error.message);
      });
}
console.log('order_create.js: openEditOrderModal function defined.'); // Log 3


function closeEditOrderModal() {
  if(editOrderModal) editOrderModal.style.display = "none";
}

function addToOrderModal(productData) {
    const productId = productData.id;
    if (orderListModal[productId]) {
        orderListModal[productId].qty += 1;
    } else {
        orderListModal[productId] = { ...productData, qty: 1 };
    }
    renderOrderModal();
}

function increaseQtyModal(productId) {
    if (orderListModal[productId]) {
        orderListModal[productId].qty += 1;
        renderOrderModal();
    }
}

function decreaseQtyModal(productId) {
    if (orderListModal[productId]) {
        if (orderListModal[productId].qty > 1) {
            orderListModal[productId].qty -= 1;
        } else {
            delete orderListModal[productId];
        }
        renderOrderModal();
    }
}

function renderOrderModal() {
    if (!modalOrderBody) {
        console.warn("renderOrderModal: modalOrderBody element not found.");
        return;
    }
    modalOrderBody.innerHTML = "";
    let subtotal = 0;

    for (const productId in orderListModal) {
        const item = orderListModal[productId];
        const total = item.qty * item.price;
        subtotal += total;

        const row = document.createElement("tr");
        row.innerHTML = `
            <td>${item.name}</td>
            <td>${parseFloat(item.price).toFixed(2)}</td>
            <td>${item.qty}</td>
            <td>${total.toFixed(2)}</td>
            <td>
            <button type="button" onclick="decreaseQtyModal('${productId}')" class="btn btn-sm btn-warning">&minus;</button>
            <button type="button" onclick="increaseQtyModal('${productId}')" class="btn btn-sm btn-success">+</button>
            </td>
        `;
        modalOrderBody.appendChild(row);
    }

    if (Object.keys(orderListModal).length === 0) {
        modalOrderBody.innerHTML = `<tr><td colspan="5" class="empty">No items in order</td></tr>`;
    }
    renderSummaryModal(subtotal);
}

function renderSummaryModal(subtotal) {
    if (!modalSummaryDiv) {
        console.warn("renderSummaryModal: modalSummaryDiv element not found.");
        return;
    }
    const discount = 0.00;
    const serviceChargeRate = parseFloat(window.companyServiceCharge || 0) / 100;
    const vatRate = parseFloat(window.companyVatCharge || 0) / 100;

    const grossAmount = subtotal - discount;
    const serviceCharge = grossAmount * serviceChargeRate;
    const vatAmount = grossAmount * vatRate;
    const netTotal = grossAmount + serviceCharge + vatAmount;

    modalSummaryDiv.innerHTML = `
        <p>Subtotal: <strong id="modal-summary-subtotal">${subtotal.toFixed(2)}</strong></p>
        <p>Discount: <strong id="modal-summary-discount">${discount.toFixed(2)}</strong></p>
        <hr style="margin: 5px 0;">
        <p>Net Total: <strong id="modal-summary-net-total" style="font-size: 1.1em;">${netTotal.toFixed(2)}</strong></p>
    `;

    const modalGrossAmountInput = document.getElementById('modal_gross_amount_value');
    const modalServiceChargeRateInput = document.getElementById('modal_service_charge_rate_value');
    const modalServiceChargeValueInput = document.getElementById('modal_service_charge_value');
    const modalVatChargeRateInput = document.getElementById('modal_vat_charge_rate_value');
    const modalVatChargeValueInput = document.getElementById('modal_vat_charge_value');
    const modalNetAmountInput = document.getElementById('modal_net_amount_value');
    const modalDiscountInput = document.getElementById('modal_discount_value');

    if (modalGrossAmountInput) modalGrossAmountInput.value = grossAmount.toFixed(2);
    if (modalServiceChargeRateInput) modalServiceChargeRateInput.value = (serviceChargeRate * 100).toFixed(2);
    if (modalServiceChargeValueInput) modalServiceChargeValueInput.value = serviceCharge.toFixed(2);
    if (modalVatChargeRateInput) modalVatChargeRateInput.value = (vatRate * 100).toFixed(2);
    if (modalVatChargeValueInput) modalVatChargeValueInput.value = vatAmount.toFixed(2);
    if (modalNetAmountInput) modalNetAmountInput.value = netTotal.toFixed(2);
    if (modalDiscountInput) modalDiscountInput.value = discount.toFixed(2);
}

function submitOrderUpdateFromModal(event) {
    const modalForm = document.getElementById('edit-order-form-modal');
    if (!modalForm) {
        alert("Error: Modal form not found.");
        return;
    }

    const existingModalItemsContainer = modalForm.querySelector('.temp-modal-items-container');
    if (existingModalItemsContainer) {
        existingModalItemsContainer.remove();
    }

    const modalItemsContainer = document.createElement('div');
    modalItemsContainer.style.display = 'none';
    modalItemsContainer.classList.add('temp-modal-items-container');

    let itemIndex = 0;
    for (const productId in orderListModal) {
        const item = orderListModal[productId];
        ['product', 'qty', 'rate_value', 'amount_value'].forEach(fieldName => {
            const input = document.createElement('input');
            input.type = 'hidden';
            if (fieldName === 'product') {
                input.name = `product[${itemIndex}]`;
                input.value = item.id;
            } else if (fieldName === 'qty') {
                input.name = `qty[${itemIndex}]`;
                input.value = item.qty;
            } else if (fieldName === 'rate_value') {
                input.name = `rate_value[${itemIndex}]`;
                input.value = item.price.toFixed(2);
            } else if (fieldName === 'amount_value') {
                input.name = `amount_value[${itemIndex}]`;
                input.value = (item.qty * item.price).toFixed(2);
            }
            modalItemsContainer.appendChild(input);
        });
        itemIndex++;
    }
    modalForm.appendChild(modalItemsContainer);

    if (Object.keys(orderListModal).length === 0 && !confirm("The order list is empty. Do you want to proceed? This might clear all items.")) {
        modalItemsContainer.remove();
        return;
    }

    const updateButton = event.target;
    if(updateButton) updateButton.disabled = true;
    if(updateButton) updateButton.textContent = "Updating...";

    const formData = new FormData(modalForm);

    fetch(modalForm.action, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.redirected || response.ok) {
            alert('Order updated successfully! Page will refresh.');
            window.location.reload();
        } else {
            response.text().then(text => {
                try {
                    const errorData = JSON.parse(text);
                    alert('Error updating order: ' + (errorData.message || errorData.errors || "Unknown server error."));
                } catch (e) {
                    alert('Error updating order. Server response: ' + text.substring(0, 200) + "...");
                }
            });
        }
    })
    .catch(error => {
        console.error('Error submitting modal form:', error);
        alert('An error occurred while updating the order.');
    })
    .finally(() => {
        if(updateButton) updateButton.disabled = false;
        if(updateButton) updateButton.textContent = "Update Order";
    });
}

window.onclick = function(event) {
    if (editOrderModal && event.target == editOrderModal) {
        closeEditOrderModal();
    }
}

function printSpecificOrder(orderId) {
    if (orderId) {
        window.open(`${base_url}orders/printDiv/${orderId}`, '_blank');
    } else {
        console.error("No order ID provided for printing.");
    }
}

async function searchProductsForModal(searchTerm) {
    if (!modalProductSearchResultsDiv) return;
    modalProductSearchResultsDiv.innerHTML = '';

    if (searchTerm.length < 1) {
        return;
    }

    try {
        const response = await fetch(`${base_url}products/fetchProductsJSON?term=${encodeURIComponent(searchTerm)}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const products = await response.json();

        if (products.length > 0) {
            products.forEach(product => {
                const itemDiv = document.createElement('div');
                itemDiv.classList.add('search-result-item');
                itemDiv.textContent = `${product.name} - ${parseFloat(product.price).toFixed(2)}`;
                itemDiv.onclick = function() {
                    const productDataForModal = {
                        id: product.id,
                        name: product.name,
                        price: parseFloat(product.price),
                        image: product.image ? `${base_url}${product.image}` : ''
                    };
                    addToOrderModal(productDataForModal);
                    modalProductSearchResultsDiv.innerHTML = '';
                    if(modalProductSearchInput) modalProductSearchInput.value = '';
                };
                modalProductSearchResultsDiv.appendChild(itemDiv);
            });
        } else {
            modalProductSearchResultsDiv.innerHTML = '<div class="search-result-item">No products found.</div>';
        }
    } catch (error) {
        console.error("Error searching products for modal:", error);
        modalProductSearchResultsDiv.innerHTML = '<div class="search-result-item">Error searching.</div>';
    }
}

console.log('order_create.js: Script parsing finished.'); // Log 4
