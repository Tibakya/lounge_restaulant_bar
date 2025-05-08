<?php
// This is a new structure for order editing page
// It will have a two-panel layout:
// Left: Order details (table, items, summary, actions) for UPDATE
// Right: Product selection (categories, search, product grid)
?>

<body class="hold-transition skin-blue layout-top-nav <?php echo isset($body_class) ? html_escape($body_class) : ''; ?>">

  <main class="order-main">
    <div class="order-page-header">
      <h1>
        Update Order - <?php echo isset($order_data['bill_no']) ? htmlspecialchars($order_data['bill_no']) : 'N/A'; ?>
        <small>Manage your orders</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo base_url('orders') ?>">Orders</a></li>
        <li class="active">Update</li>
      </ol>
    </div>

    <form role="form" action="<?php echo base_url('orders/update/'.$order_data['id']) ?>" method="post" id="order-form">
      <div class="order-section">
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

          <div class="table-box">
            <label for="table">Select Table:</label>
            <select class="form-control" id="table" name="table_name" required>
              <option value="">Select Table</option>
              <?php foreach ($tables as $k => $v): ?>
                <option value="<?php echo $v['id'] ?>" <?php if($order_data['table_id'] == $v['id']) { echo "selected='selected'"; } ?> ><?php echo $v['table_name'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="table-box" style="margin-top: 10px;">
            <label for="waiter">Assign Waiter</label>
            <select id="waiter" name="waiter_id" class="form-control" required>
              <option value="">Select Waiter</option>
              <?php if(isset($waiters) && !empty($waiters)): ?>
                <?php foreach ($waiters as $waiter_user): ?>
                  <option value="<?php echo $waiter_user['id'] ?>" <?php if(isset($order_data['waiter_id']) && $order_data['waiter_id'] == $waiter_user['id']) { echo "selected='selected'"; } ?>>
                    <?php echo html_escape($waiter_user['firstname'] . ' ' . $waiter_user['lastname']); ?>
                  </option>
                <?php endforeach; ?>
              <?php else: ?>
                <option value="" disabled>No waiters available</option>
              <?php endif; ?>
            </select>
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
        <input type="hidden" name="discount" id="discount_value">

        <div class="summary" id="summary"
             data-service-charge="<?php echo isset($service_charge_rate) ? $service_charge_rate : 0; ?>"
             data-vat-charge="<?php echo isset($vat_charge_rate) ? $vat_charge_rate : 0; ?>">
          <p>Subtotal: <strong id="summary-subtotal">0.00</strong></p>
          <p>Discount: <strong id="summary-discount">0.00</strong></p>
          <hr style="margin: 5px 0;">
          <p>Net Total: <strong id="summary-net-total" style="font-size: 1.1em;">0.00</strong></p>
        </div>

        <div class="form-group" style="margin-top: 15px;">
            <label for="paid_status" class="col-sm-4 control-label" style="text-align:left; padding-left:0;">Paid Status</label>
            <div class="col-sm-8" style="padding-left:0; padding-right:0;">
                <select class="form-control" id="paid_status" name="paid_status" required>
                  <option value="2" <?php if($order_data['paid_status'] == 2) { echo "selected='selected'"; } ?> >Unpaid</option>
                  <option value="1" <?php if($order_data['paid_status'] == 1) { echo "selected='selected'"; } ?> >Paid</option>
                </select>
            </div>
        </div>
        <div class="order-actions">
          <button type="button" onclick="printSpecificOrder(<?php echo $order_data['id']; ?>)">🖨️ Print</button>
          <button type="button" onclick="submitOrderForUpdate()">🧾 Update Order</button>
          <button type="button" onclick="goBack()">⬅️ Back</button>
        </div>
      </div>

      <div class="right-panel">
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
          <input type="text" placeholder="Search products...">
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
    </div>
  </form>
</main>

<script>
    // These are now expected to be set in header.php via window object
    // const initialOrderItems = <?php echo isset($js_order_items_json) ? $js_order_items_json : '{}'; ?>;
    // const companyServiceCharge = <?php echo floatval(isset($service_charge_rate) ? $service_charge_rate : 0); ?>;
    // const companyVatCharge = <?php echo floatval(isset($vat_charge_rate) ? $vat_charge_rate : 0); ?>;
    const orderIdForUpdate = <?php echo isset($order_data['id']) ? $order_data['id'] : 'null'; ?>;

    // Pass initial items for edit page specifically
    const initialOrderItems = <?php echo isset($js_order_items_json) ? $js_order_items_json : '{}'; ?>;

    document.addEventListener('DOMContentLoaded', () => {
        if (typeof initialOrderItems !== 'undefined' && Object.keys(initialOrderItems).length > 0) {
            orderList = initialOrderItems; // Use the main orderList for the edit page
        }
        const summaryEl = document.getElementById("summary");
        if (summaryEl) {
            // Values are now expected to be on window object from header.php
            summaryEl.dataset.serviceCharge = window.companyServiceCharge || 0;
            summaryEl.dataset.vatCharge = window.companyVatCharge || 0;
        }
        if(document.getElementById("order-body")) { // Check if on create/edit page
             renderOrder();
        }
    });
</script>
<!-- order_create.js is loaded conditionally in footer.php -->

</body>
</html>
