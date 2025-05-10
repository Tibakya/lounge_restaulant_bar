<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Manage
            <small>Orders</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Orders</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-md-12 col-xs-12">

                <div id="messages"></div>

                <?php if($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <?php echo $this->session->flashdata('success'); ?>
                </div>
                <?php elseif($this->session->flashdata('errors')): ?>
                <div class="alert alert-error alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <?php echo $this->session->flashdata('errors'); ?>
                </div>
                <?php endif; ?>

                <?php if(in_array('createOrder', $user_permission)): ?>
                <a href="<?php echo base_url('orders/create') ?>" class="btn btn-success">Add Order</a>
                <br /> <br />
                <?php endif; ?>

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Manage Orders</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="manageTable" class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Bill no</th>
                                    <th>Waiter</th> <!-- Added Waiter Column -->
                                    <th>Date Time</th>
                                    <th>Total Products</th>
                                    <th>Total Amount</th>
                                    <th>Paid status</th>
                                    <?php if(in_array('updateOrder', $user_permission) || in_array('viewOrder', $user_permission) || in_array('deleteOrder', $user_permission)): ?>
                                    <th>Action</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>

                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- col-md-12 -->
        </div>
        <!-- /.row -->


    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php if(in_array('deleteOrder', $user_permission)): ?>
<!-- remove brand modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="removeModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Remove Order</h4>
            </div>

            <form role="form" action="<?php echo base_url('orders/remove') ?>" method="post" id="removeForm">
                <div class="modal-body">
                    <p>Do you really want to remove?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save changes</button>
                </div>
            </form>


        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php endif; ?>


<!-- Edit Order Modal -->
<!-- Hakikisha hii div ina id="editOrderModal" -->
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
                    <?php
                    // Hii $tables_data_for_modal inatoka kwenye controller Orders.php, index() method
                    if(isset($tables_data_for_modal) && !empty($tables_data_for_modal)): ?>
                        <?php foreach ($tables_data_for_modal as $tbl): ?>
                            <option value="<?php echo $tbl['id'] ?>"><?php echo html_escape($tbl['table_name']); ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>No tables loaded</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group-modal">
                <label for="modal_waiter_id">Waiter:</label>
                <select id="modal_waiter_id" name="waiter_id" class="form-control" required>
                    <option value="">Select Waiter</option>
                     <?php
                     // Hii $waiters_data_for_modal inatoka kwenye controller Orders.php, index() method
                     if(isset($waiters_data_for_modal) && !empty($waiters_data_for_modal)): ?>
                        <?php foreach ($waiters_data_for_modal as $wt): ?>
                        <option value="<?php echo $wt['id'] ?>">
                            <?php echo html_escape($wt['firstname'] . ' ' . $wt['lastname']); ?>
                        </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>No waiters loaded</option>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <h4>Order Items</h4>
        <table class="order-table" id="modal-order-table">
            <thead>
                <tr><th>Product</th><th>Price</th><th>Qty</th><th>Total</th><th>Action</th></tr>
            </thead>
            <tbody id="modal-order-body"></tbody>
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
                <select class="form-control" id="modal_paid_status" name="paid_status" required><option value="2">Unpaid</option><option value="1">Paid</option></select>
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


<script type="text/javascript">
var manageTable;
var base_url = "<?php echo base_url(); ?>";

$(document).ready(function() {

    $("#OrderMainNav").addClass('active');
    $("#manageOrderSubMenu").addClass('active');

    // initialize the datatable 
    manageTable = $('#manageTable').DataTable({
        'ajax': base_url + 'orders/fetchOrdersData',
        'order': []
    });

});

// remove functions 
function removeFunc(id) {
    if (id) {
        $("#removeForm").on('submit', function() {

            var form = $(this);

            // remove the text-danger
            $(".text-danger").remove();

            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: {
                    order_id: id
                },
                dataType: 'json',
                success: function(response) {

                    manageTable.ajax.reload(null, false);

                    if (response.success === true) {
                        $("#messages").html(
                            '<div class="alert alert-success alert-dismissible" role="alert">' +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                            '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>' +
                            response.messages +
                            '</div>');

                        // hide the modal
                        $("#removeModal").modal('hide');

                    } else {

                        $("#messages").html(
                            '<div class="alert alert-warning alert-dismissible" role="alert">' +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                            '<strong> <span class="glyphicon glyphicon-exclamation-sign"></span> </strong>' +
                            response.messages +
                            '</div>');
                    }
                }
            });

            return false;
        });
    }
}
</script>