<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Inventory
      <small>Adjust Stock</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?php echo base_url('inventory') ?>">Inventory</a></li>
      <li class="active">Adjust Stock</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-md-8 col-md-offset-2 col-xs-12">

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


        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Add or Adjust Stock Quantity</h3>
          </div>
          <!-- /.box-header -->
          <form role="form" action="<?php echo base_url('inventory/adjust') ?>" method="post">
              <div class="box-body">

                <?php echo validation_errors('<p class="text-danger">', '</p>'); ?>

                <div class="form-group">
                  <label for="product">Product <span class="text-danger">*</span></label>
                  <select class="form-control select_group" id="product" name="product" required>
                    <option value="">Select Product</option>
                    <?php if(isset($products) && !empty($products)): ?>
                      <?php foreach ($products as $p): ?>
                        <option value="<?php echo $p['id']; ?>" <?php echo set_select('product', $p['id']); ?>><?php echo html_escape($p['name']); ?></option>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </select>
                </div>

                <div class="form-group">
                  <label for="location">Location <span class="text-danger">*</span></label>
                  <select class="form-control select_group" id="location" name="location" required>
                    <option value="">Select Location</option>
                     <?php if(isset($locations) && !empty($locations)): ?>
                      <?php foreach ($locations as $l): ?>
                        <option value="<?php echo $l['id']; ?>" <?php echo set_select('location', $l['id']); ?>><?php echo html_escape($l['name']); ?></option>
                      <?php endforeach; ?>
                    <?php else: ?>
                        <option value="1" <?php echo set_select('location', '1', TRUE); ?>>Main Store (Default)</option> <!-- Fallback if no locations loaded -->
                    <?php endif; ?>
                  </select>
                </div>

                <div class="form-group">
                    <label for="adjustment_type">Adjustment Type <span class="text-danger">*</span></label>
                    <select class="form-control" id="adjustment_type" name="adjustment_type" required>
                        <option value="set" <?php echo set_select('adjustment_type', 'set', TRUE); ?>>Set New Quantity</option>
                        <option value="add" <?php echo set_select('adjustment_type', 'add'); ?>>Add to Current Quantity</option>
                        <option value="subtract" <?php echo set_select('adjustment_type', 'subtract'); ?>>Subtract from Current Quantity</option>
                    </select>
                </div>

                <div class="form-group">
                  <label for="quantity">Quantity <span class="text-danger">*</span></label>
                  <input type="number" step="any" class="form-control" id="quantity" name="quantity" placeholder="Enter quantity" value="<?php echo set_value('quantity'); ?>" autocomplete="off" required>
                  <small class="text-muted">Enter the quantity to set, add, or subtract.</small>
                </div>

                <div class="form-group">
                  <label for="unit_cost">Unit Cost (Optional)</label>
                  <input type="number" step="any" class="form-control" id="unit_cost" name="unit_cost" placeholder="Enter cost per unit" value="<?php echo set_value('unit_cost'); ?>" autocomplete="off">
                   <small class="text-muted">Used for inventory valuation (if applicable).</small>
                </div>

                 <div class="form-group">
                  <label for="reason">Reason / Note (Optional)</label>
                  <textarea class="form-control" id="reason" name="reason" placeholder="e.g., Initial stock count, Received new shipment, Spoilage, etc."><?php echo set_value('reason'); ?></textarea>
                </div>

              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Adjust Stock</button>
                <a href="<?php echo base_url('inventory') ?>" class="btn btn-warning">Back</a>
              </div>
            </form>
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
    // Initialize Select2 if you are using it
    $('.select_group').select2();

    // Activate the main and sub-menu items
    $('#mainInventoryNav').addClass('active');
    // Decide which sub-menu to activate, maybe none for 'adjust' or create a new one
    // $('#manageInventoryNav').addClass('active');
    // Or create a new one: $('#adjustInventoryNav').addClass('active');
  });
</script>
