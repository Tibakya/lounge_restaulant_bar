<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Inventory
      <small>Manage Stock</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Inventory</li>
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
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('success'); ?>
          </div>
        <?php elseif($this->session->flashdata('error')): ?>
          <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('error'); ?>
          </div>
        <?php endif; ?>

        <?php // Button to add stock - Link to be created later ?>
        <?php if(in_array('createInventory', $this->permission)): ?>
          <a href="<?php echo base_url('inventory/adjust') ?>" class="btn btn-primary" style="margin-bottom: 15px;">Add/Adjust Stock</a>
          <br /> <br />
        <?php endif; ?>

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Current Stock Levels</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <div class="table-responsive">
                <table id="inventoryTable" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Product Name</th>
                    <!-- <th>SKU</th> --> <!-- Removed SKU column -->
                    <th>Location</th>
                    <th>Current Quantity</th>
                    <th>Low Stock Threshold</th>
                    <th>Last Updated</th>
                    <?php if(in_array('updateInventory', $this->permission) || in_array('deleteInventory', $this->permission)): ?>
                    <th>Action</th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody>
                    <?php if(isset($inventory_data) && !empty($inventory_data)): ?>
                    <?php foreach ($inventory_data as $v): ?>
                        <tr>
                        <td><?php echo html_escape($v['product_name']); ?></td>
                        <!-- <td><?php echo html_escape($v['sku']); ?></td> --> <!-- Removed SKU data -->
                        <td><?php echo $v['location_name'] ? html_escape($v['location_name']) : 'N/A'; ?></td>
                        <td><?php echo $v['quantity']; // Use 'quantity' as aliased in the model ?></td>
                        <td><?php echo isset($v['low_stock_threshold']) ? $v['low_stock_threshold'] : 'N/A'; ?></td>
                        <td><?php echo date('d-m-Y H:i', strtotime($v['last_updated'])); ?></td>
                        <?php if(in_array('updateInventory', $this->permission) || in_array('deleteInventory', $this->permission)): ?>
                            <td>
                            <?php if(in_array('updateInventory', $this->permission)): ?>
                                <!-- Edit button - Link to be created later -->
                                <a href="<?php echo base_url('inventory/edit/'.$v['inventory_id']) ?>" class="btn btn-info btn-sm"><i class="fa fa-pencil"></i></a> <!-- Changed to btn-info and added btn-sm -->
                            <?php endif; ?>
                            <?php if(in_array('deleteInventory', $this->permission)): ?>
                                <!-- Delete button - Functionality to be added later -->
                                <button type="button" class="btn btn-danger btn-sm" onclick="removeFunc(<?php echo $v['inventory_id'] ?>)"><i class="fa fa-trash"></i></button> <!-- Changed to btn-danger and added btn-sm -->
                            <?php endif; ?>
                            </td>
                        <?php endif; ?>
                        </tr>
                    <?php endforeach ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="<?php echo (in_array('updateInventory', $this->permission) || in_array('deleteInventory', $this->permission)) ? '6' : '5'; ?>" class="text-center">No inventory data found.</td> <!-- Adjusted colspan -->
                    </tr>
                    <?php endif; ?>
                </tbody>
                </table>
            </div>
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

<script type="text/javascript">
$(document).ready(function() {
  // Activate the main and sub-menu items
  $('#mainInventoryNav').addClass('active');
  $('#manageInventoryNav').addClass('active');

  // Initialize DataTables
  $('#inventoryTable').DataTable({
      'order': [], // Optional: disable initial sorting
      'responsive': true // Make table responsive
  });

});

// Function for removing inventory item (to be implemented later)
// function removeFunc(id) {
//   if(id) {
//     // Confirmation dialog logic here
//   }
// }
</script>
