<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Manage
      <small>Products</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Products</li>
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
        <?php elseif($this->session->flashdata('errors')): ?>
          <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('errors'); ?>
          </div>
        <?php endif; ?>


        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Add Product</h3>
          </div>
          <!-- /.box-header -->
          <form role="form" action="<?php base_url('users/create') ?>" method="post" enctype="multipart/form-data">
              <div class="box-body">

                <?php echo validation_errors(); ?>

                <div class="form-group">
                  <label for="product_image">Image:</label>
                  <div class="kv-avatar">
                      <div class="file-loading">
                          <input id="product_image" name="product_image" type="file">
                      </div>
                  </div>
                </div>

                <div class="form-group">
                  <label for="product_name">Product Name *</label>
                  <input type="text" class="form-control" id="product_name" name="product_name" placeholder="Enter product name" autocomplete="off" value="<?php echo $this->input->post('product_name') ?>" />
                </div>

                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="price">Price *</label>
                      <input type="text" class="form-control" id="price" name="price" placeholder="Enter price (e.g., 1500.00)" autocomplete="off" value="<?php echo $this->input->post('price') ?>" />
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="cost_price">Cost Price (Optional)</label>
                      <input type="text" class="form-control" id="cost_price" name="cost_price" placeholder="Enter cost price (e.g., 1000.00)" autocomplete="off" value="<?php echo $this->input->post('cost_price') ?>" />
                    </div>
                  </div>
                   <div class="col-md-4">
                    <div class="form-group">
                      <label for="unit_of_measure">Unit of Measure</label>
                      <input type="text" class="form-control" id="unit_of_measure" name="unit_of_measure" placeholder="e.g., Plate, Bottle, Shot, Glass, Kg" autocomplete="off" value="<?php echo $this->input->post('unit_of_measure') ?: 'Plate'; ?>" />
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label for="description">Description</label>
                  <textarea type="text" class="form-control" id="description" name="description" placeholder="Enter description" autocomplete="off"><?php echo $this->input->post('description') ?></textarea>
                </div>

                <div class="form-group">
                  <label for="category">Category *</label>
                  <select class="form-control select_group" id="category" name="category[]" multiple="multiple" required>
                    <?php foreach ($category as $k => $v): ?>
                      <option value="<?php echo $v['id'] ?>" <?php echo set_select('category[]', $v['id']); ?>><?php echo $v['name'] ?></option>
                    <?php endforeach ?>
                  </select>
                </div>

                <div class="form-group">
                  <label for="product_type">Product Type *</label>
                  <div class="radio">
                    <label>
                      <input type="radio" name="product_type" id="product_type_food" value="food" <?php echo set_radio('product_type', 'food', TRUE); ?>>
                      Food (Goes to Kitchen)
                    </label>
                  </div>
                  <div class="radio">
                    <label>
                      <input type="radio" name="product_type" id="product_type_beverage" value="beverage" <?php echo set_radio('product_type', 'beverage'); ?>>
                      Beverage (Goes to Bar)
                    </label>
                  </div>
                </div>

                <!-- Store selection removed -->
                <!--
                <div class="form-group">
                  <label for="store">Store</label>
                  <select class="form-control select_group" id="store" name="store[]" multiple="multiple">
                    <?php foreach ($stores as $k => $v): ?>
                      <option value="<?php echo $v['id'] ?>"><?php echo $v['name'] ?></option>
                    <?php endforeach ?>
                  </select>
                </div>
                -->

                <div class="form-group">
                  <label for="active">Status *</label>
                  <select class="form-control" id="active" name="active" required>
                    <option value="1" <?php echo set_select('active', '1', TRUE); ?>>Active</option>
                    <option value="2" <?php echo set_select('active', '2'); ?>>Inactive</option>
                  </select>
                </div>

              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="<?php echo base_url('products/') ?>" class="btn btn-warning">Back</a>
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
    $(".select_group").select2({
      // placeholder: "Select Categories", // Optional placeholder
      // allowClear: true // Optional: Adds a clear button
    });
    $("#description").wysihtml5();

    $("#mainProductNav").addClass('active');
    $("#addProductNav").addClass('active');

    var btnCust = '<button type="button" class="btn btn-secondary" title="Add picture tags" ' +
        'onclick="alert(\'Call your custom code here.\')">' +
        '<i class="glyphicon glyphicon-tag"></i>' +
        '</button>';
    $("#product_image").fileinput({
        overwriteInitial: true,
        maxFileSize: 1500,
        showClose: false,
        showCaption: false,
        browseLabel: '',
        removeLabel: '',
        browseIcon: '<i class="glyphicon glyphicon-folder-open"></i>',
        removeIcon: '<i class="glyphicon glyphicon-remove"></i>',
        removeTitle: 'Cancel or reset changes',
        elErrorContainer: '#kv-avatar-errors-1',
        msgErrorClass: 'alert alert-block alert-danger',
        // defaultPreviewContent: '<img src="/uploads/default_avatar_male.jpg" alt="Your Avatar">',
        layoutTemplates: {main2: '{preview} ' +  btnCust + ' {remove} {browse}'},
        allowedFileExtensions: ["jpg", "png", "gif", "jpeg", "webp"] // Added webp
    });

  });
</script>
