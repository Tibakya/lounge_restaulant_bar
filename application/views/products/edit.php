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
            <h3 class="box-title">Edit Product</h3>
          </div>
          <!-- /.box-header -->
          <form role="form" action="<?php base_url('users/update') ?>" method="post" enctype="multipart/form-data">
              <div class="box-body">

                <?php echo validation_errors(); ?>

                <div class="form-group">
                  <label>Image Preview:</label>
                  <img src="<?php echo base_url() . $product_data['image'] ?>" width="150" height="150" class="img-circle">
                </div>

                <div class="form-group">
                  <label for="product_image">Update Image (Optional):</label>
                  <div class="kv-avatar">
                      <div class="file-loading">
                          <input id="product_image" name="product_image" type="file">
                      </div>
                  </div>
                </div>

                <div class="form-group">
                  <label for="product_name">Product Name *</label>
                  <input type="text" class="form-control" id="product_name" name="product_name" placeholder="Enter product name" autocomplete="off" value="<?php echo !empty($this->input->post('product_name')) ? $this->input->post('product_name') : $product_data['name'] ?>" />
                </div>

                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="price">Price *</label>
                      <input type="text" class="form-control" id="price" name="price" placeholder="Enter price" autocomplete="off" value="<?php echo !empty($this->input->post('price')) ? $this->input->post('price') : $product_data['price'] ?>" />
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="cost_price">Cost Price (Optional)</label>
                      <input type="text" class="form-control" id="cost_price" name="cost_price" placeholder="Enter cost price" autocomplete="off" value="<?php echo !empty($this->input->post('cost_price')) ? $this->input->post('cost_price') : $product_data['cost_price'] ?>" />
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="unit_of_measure">Unit of Measure</label>
                      <input type="text" class="form-control" id="unit_of_measure" name="unit_of_measure" placeholder="e.g., Plate, Bottle, Shot, Glass, Kg" autocomplete="off" value="<?php echo !empty($this->input->post('unit_of_measure')) ? $this->input->post('unit_of_measure') : $product_data['unit_of_measure'] ?>" />
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label for="description">Description</label>
                  <textarea type="text" class="form-control" id="description" name="description" placeholder="Enter description" autocomplete="off"><?php echo !empty($this->input->post('description')) ? $this->input->post('description') : $product_data['description'] ?></textarea>
                </div>

                <?php $category_ids = !empty($this->input->post('category')) ? $this->input->post('category') : $product_data['category_ids']; ?>
                <div class="form-group">
                  <label for="category">Category *</label>
                  <select class="form-control select_group" id="category" name="category[]" multiple="multiple" required>
                    <?php foreach ($category as $k => $v): ?>
                      <option value="<?php echo $v['id'] ?>" <?php if(in_array($v['id'], $category_ids)) { echo 'selected="selected"'; } ?>><?php echo $v['name'] ?></option>
                    <?php endforeach ?>
                  </select>
                </div>

                <?php $product_type_val = !empty($this->input->post('product_type')) ? $this->input->post('product_type') : $product_data['product_type']; ?>
                <div class="form-group">
                  <label for="product_type">Product Type *</label>
                  <div class="radio">
                    <label>
                      <input type="radio" name="product_type" id="product_type_food" value="food" <?php if($product_type_val == 'food') { echo "checked='checked'"; } ?> >
                      Food (Goes to Kitchen)
                    </label>
                  </div>
                  <div class="radio">
                    <label>
                      <input type="radio" name="product_type" id="product_type_beverage" value="beverage" <?php if($product_type_val == 'beverage') { echo "checked='checked'"; } ?>>
                      Beverage (Goes to Bar)
                    </label>
                  </div>
                </div>

                <!-- Store selection removed -->
                <!--
                <?php $store_ids = $product_data['store_id']; ?>
                <div class="form-group">
                  <label for="store">Store</label>
                  <select class="form-control select_group" id="store" name="store[]" multiple="multiple">
                    <?php foreach ($stores as $k => $v): ?>
                      <option value="<?php echo $v['id'] ?>" <?php if(in_array($v['id'], $store_ids)) { echo 'selected="selected"'; } ?>><?php echo $v['name'] ?></option>
                    <?php endforeach ?>
                  </select>
                </div>
                -->

                <?php $active_val = !empty($this->input->post('active')) ? $this->input->post('active') : $product_data['active']; ?>
                <div class="form-group">
                  <label for="active">Status *</label>
                  <select class="form-control" id="active" name="active" required>
                    <option value="1" <?php if($active_val == 1) { echo "selected='selected'"; } ?>>Active</option>
                    <option value="2" <?php if($active_val == 2) { echo "selected='selected'"; } ?>>Inactive</option>
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
    $("#manageProductNav").addClass('active'); // Keep manage active as we are editing

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
