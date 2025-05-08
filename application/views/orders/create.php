<?php
// This is a new structure for order creation page
// It will have a two-panel layout:
// Left: Order details (table, items, summary, actions)
// Right: Product selection (categories, search, product grid)
?>

<body class="hold-transition skin-blue layout-top-nav <?php echo isset($body_class) ? html_escape($body_class) : ''; ?>">

  <main class="order-main">
    <div class="order-page-header">
      <h1>
        Add Order
        <small>Manage your orders</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo base_url('orders') ?>">Orders</a></li>
        <li class="active">Create</li>
      </ol>
    </div>

    <form action="<?php echo base_url('orders/handle_ajax_create') ?>" method="post" id="order-form">
      <div class="order-section">
        <!-- Left Panel: Order Details -->
        <div class="left-panel">
          <?php if(validation_errors()): ?>
            <div class="alert alert-danger alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <?php echo validation_errors(); ?>
            </div>
          <?php endif; ?>
          <?php if($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <?php echo $this->session->flashdata('success'); ?>
            </div>
          <?php elseif($this->session->flashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <?php echo $this->session->flashdata('errors'); ?>
            </div>
          <?php endif; ?>

          <div class="selection-area">
            <div class="table-selection-box">
              <label>Choose Table</label>
              <div id="table-selection-buttons" class="selection-buttons-grid">
                <?php foreach ($tables as $k => $v): ?>
                  <?php if (isset($v['available']) && $v['available'] == 1 && isset($v['active']) && $v['active'] == 1): ?>
                    <button type="button" class="table-button" data-table-id="<?php echo $v['id']; ?>">
                      <?php echo html_escape($v['table_name']); ?>
                    </button>
                  <?php endif; ?>
                <?php endforeach; ?>
              </div>
              <input type="hidden" id="selected_table_id_hidden" name="selected_table_id">
            </div>

            <div class="waiter-selection-box">
              <label>Assign Waiter</label>
              <div id="waiter-selection-buttons" class="selection-buttons-grid">
                <?php if(isset($waiters) && !empty($waiters)): ?>
                  <?php foreach ($waiters as $waiter): ?>
                    <button type="button" class="waiter-button" data-waiter-id="<?php echo $waiter['id']; ?>">
                      <?php echo html_escape($waiter['firstname']); ?>
                    </button>
                  <?php endforeach; ?>
                <?php else: ?>
                  <p>No waiters available.</p>
                <?php endif; ?>
              </div>
              <input type="hidden" id="selected_waiter_id_hidden" name="selected_waiter_id">
            </div>
          </div>

          <table class="order-table">
            <thead>
              <tr>
                <th>Image</th>
                <th>Product</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Total</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="order-body">
              <tr id="empty-row">
                <td colspan="6" class="empty">Empty List (Select Product)</td>
              </tr>
            </tbody>
          </table>

          <input type="hidden" name="gross_amount_value" id="gross_amount_value">
          <input type="hidden" name="service_charge_rate" id="service_charge_rate_value">
          <input type="hidden" name="service_charge_value" id="service_charge_value">
          <input type="hidden" name="vat_charge_rate" id="vat_charge_rate_value">
          <input type="hidden" name="vat_charge_value" id="vat_charge_value">
          <input type="hidden" name="net_amount_value" id="net_amount_value">
          <input type="hidden" name="discount_value" id="discount_value">

          <div class="summary" id="summary"
               data-service-charge="<?php echo isset($company_data['charge_amount']) ? $company_data['charge_amount'] : 0; ?>"
               data-vat-charge="<?php echo isset($company_data['vat_charge']) ? $company_data['vat_charge'] : 0; ?>">
            <p>Subtotal: <strong id="summary-subtotal">0.00</strong></p>
            <p>Discount: <strong id="summary-discount">0.00</strong></p>
            <hr style="margin: 5px 0;">
            <p>Net Total: <strong id="summary-net-total" style="font-size: 1.1em;">0.00</strong></p>
          </div>

          <div class="order-actions">
            <!-- Print button removed from here -->
            <button type="button" onclick="createOrder(event)">🧾 Create Order</button>
            <button type="button" onclick="goBack()">⬅️ Back</button>
          </div>
        </div>

        <div class="middle-panel">
            <div class="category-tabs">
                <button class="tab active" data-category-id="all">All</button>
                <?php if (isset($categories) && !empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                <button class="tab" data-category-id="<?php echo $category['id']; ?>">
                    <?php echo htmlspecialchars($category['name']); ?>
                </button>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="search-bar">
                <input type="text" placeholder="Search products..." />
            </div>

            <div class="product-grid" id="product-grid">
            <?php if (isset($products) && !empty($products)): ?>
                <?php foreach ($products as $product): ?>
                <div class="product-card"
                    data-product-id="<?php echo $product['id']; ?>"
                    data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                    data-product-price="<?php echo $product['price']; ?>"
                    data-category-id="<?php
                                        $cat_ids_str = isset($product['category_ids']) ? $product['category_ids'] : null;
                                        if ($cat_ids_str) {
                                            $cat_ids_arr = explode(',', $cat_ids_str);
                                            echo !empty($cat_ids_arr) ? htmlspecialchars(trim($cat_ids_arr[0])) : '';
                                        } else {
                                            echo '';
                                        }
                                    ?>">
                    <img src="<?php echo base_url($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <p><?php echo htmlspecialchars($product['name']); ?></p>
                    <p class="price"><?php echo isset($currency_symbol) ? $currency_symbol : ''; ?> <?php echo number_format($product['price'], 2); ?></p>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No products available.</p>
            <?php endif; ?>
            </div>
        </div>

        <div class="recent-orders-panel">
            <h4>Recent Orders</h4>
            <?php if (isset($recent_orders) && !empty($recent_orders)): ?>
                <ul class="recent-orders-list">
                    <?php foreach ($recent_orders as $order): ?>
                        <li>
                            <div class="order-info">
                                <strong>Bill:</strong> <?php echo html_escape($order['bill_no']); ?><br>
                                <strong>Table:</strong> <?php echo html_escape($order['table_name'] ? $order['table_name'] : 'N/A'); ?><br>
                                <strong>Waiter:</strong> <span class="waiter-name-badge"><?php echo html_escape($order['waiter_firstname'] ? $order['waiter_firstname'] : 'N/A'); ?></span><br>
                                <strong>Total:</strong> <?php echo isset($currency_symbol) ? $currency_symbol : ''; ?> <?php echo number_format($order['net_amount'], 2); ?><br>
                                <strong>Status:</strong>
                                <?php if ($order['paid_status'] == 1): ?>
                                    <span class="label label-success">Paid</span>
                                <?php else: ?>
                                    <span class="label label-danger">Unpaid</span>
                                <?php endif; ?>
                            </div>
                            <div class="order-actions-recent">
                                <button type="button" class="btn btn-info btn-xs" onclick="openEditOrderModal(<?php echo $order['id']; ?>)">Edit</button>
                                <button type="button" class="btn btn-default btn-xs" style="margin-left: 5px;" onclick="printSpecificOrder(<?php echo $order['id']; ?>)">Print</button>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No recent orders found.</p>
            <?php endif; ?>
        </div>
      </div>
    </form>
  </main>

<div id="editOrderModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <span class="close-modal-btn" onclick="closeEditOrderModal()">&times;</span>
      <h2>Edit Order - Bill #<span id="modal-bill-no"></span></h2>
    </div>
    <div class="modal-body">
      <form id="edit-order-form-modal" action="" method="post">
        <input type="hidden" name="order_id_modal" id="order_id_modal">

        <div class="form-row">
            <div class="form-group-modal">
                <label for="modal_table_name">Table:</label>
                <select class="form-control" id="modal_table_name" name="table_name" required>
                    <option value="">Select Table</option>
                    <?php foreach ($tables as $k => $v): ?>
                        <option value="<?php echo $v['id'] ?>"><?php echo $v['table_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group-modal">
                <label for="modal_waiter_id">Waiter:</label>
                <select id="modal_waiter_id" name="waiter_id" class="form-control" required>
                    <option value="">Select Waiter</option>
                    <?php if(isset($waiters) && !empty($waiters)): ?>
                        <?php foreach ($waiters as $waiter): ?>
                        <option value="<?php echo $waiter['id'] ?>">
                            <?php echo html_escape($waiter['firstname'] . ' ' . $waiter['lastname']); ?>
                        </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <h4>Order Items</h4>
        <table class="order-table" id="modal-order-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="modal-order-body">
            </tbody>
        </table>
        <div class="product-search-modal">
            <input type="text" id="modal_product_search" placeholder="Search product to add...">
            <div id="modal_product_search_results"></div>
        </div>

        <input type="hidden" name="gross_amount_value" id="modal_gross_amount_value">
        <input type="hidden" name="service_charge_rate" id="modal_service_charge_rate_value">
        <input type="hidden" name="service_charge_value" id="modal_service_charge_value">
        <input type="hidden" name="vat_charge_rate" id="modal_vat_charge_rate_value">
        <input type="hidden" name="vat_charge_value" id="modal_vat_charge_value">
        <input type="hidden" name="net_amount_value" id="modal_net_amount_value">
        <input type="hidden" name="discount" id="modal_discount_value">

        <h4>Summary</h4>
        <div class="summary" id="modal-summary">
            <p>Subtotal: <strong id="modal-summary-subtotal">0.00</strong></p>
            <p>Discount: <strong id="modal-summary-discount">0.00</strong></p>
            <hr style="margin: 5px 0;">
            <p>Net Total: <strong id="modal-summary-net-total" style="font-size: 1.1em;">0.00</strong></p>
        </div>

        <div class="form-row">
            <div class="form-group-modal">
                <label for="modal_paid_status">Paid Status:</label>
                <select class="form-control" id="modal_paid_status" name="paid_status" required>
                    <option value="2">Unpaid</option>
                    <option value="1">Paid</option>
                </select>
            </div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-primary" onclick="submitOrderUpdateFromModal(event)">Update Order</button>
      <button type="button" class="btn btn-default" onclick="closeEditOrderModal()">Close</button>
    </div>
  </div>
</div>

<script>
    // These are already set in header.php, but ensure they are available if header.php changes
    // window.base_url_php = "<?php echo base_url(); ?>";
    // window.companyServiceCharge = <?php echo floatval(isset($company_data['charge_amount']) ? $company_data['charge_amount'] : 0); ?>;
    // window.companyVatCharge = <?php echo floatval(isset($company_data['vat_charge']) ? $company_data['vat_charge'] : 0); ?>;

    document.addEventListener('DOMContentLoaded', () => {
        const summaryEl = document.getElementById("summary");
        if (summaryEl) {
            // These values are now expected to be on window object from header.php
            summaryEl.dataset.serviceCharge = window.companyServiceCharge || 0;
            summaryEl.dataset.vatCharge = window.companyVatCharge || 0;
        }
        if(document.getElementById("order-body")) { // Check if on create/edit page
             renderOrder();
        }
    });
</script>

</body>
</html>
