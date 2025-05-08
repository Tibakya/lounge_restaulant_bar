<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Company
      <small>Information</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Company</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-md-12 col-xs-12">

        <?php if($this->session->flashdata('success')): ?>
          <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('success'); ?>
          </div>
        <?php elseif($this->session->flashdata('error')): ?>
          <div class="alert alert-error alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('error'); ?>
          </div>
        <?php endif; ?>

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Manage Company Information</h3>
          </div>
          <!-- IMPORTANT: Added enctype for file uploads -->
          <form role="form" action="<?php echo base_url('company') ?>" method="post" enctype="multipart/form-data">
            <div class="box-body">

              <?php echo validation_errors(); ?>

              <div class="form-group">
                <label for="company_name">Company Name</label>
                <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Enter company name" value="<?php echo $company_data['company_name'] ?>" autocomplete="off">
              </div>

              <!-- Logo Upload Section -->
              <div class="form-group">
                <label for="logo">Company Logo</label>
                <div class="kv-avatar">
                    <div class="file-loading">
                        <!-- Make sure the input name matches the controller check: name="logo" -->
                        <input id="logo" name="logo" type="file" accept="image/*">
                    </div>
                </div>
                <?php if(isset($company_data['logo']) && $company_data['logo'] && file_exists('./assets/images/company_logo/' . $company_data['logo'])): ?>
                  <p style="margin-top: 10px;">Current Logo:</p>
                  <img src="<?php echo base_url('assets/images/company_logo/' . $company_data['logo']); ?>" alt="Company Logo" class="img-thumbnail" style="max-height: 100px; margin-top: 5px;">
                <?php else: ?>
                  <p style="margin-top: 10px;">No logo uploaded yet.</p>
                <?php endif; ?>
                <small class="text-muted">Allowed types: gif, jpg, png, jpeg, webp. Max size: 2MB.</small>
              </div>
              <!-- End Logo Upload Section -->


              <div class="form-group">
                <label for="service_charge_value">Charge Amount (%)</label>
                <input type="text" class="form-control" id="service_charge_value" name="service_charge_value" placeholder="Enter charge amount %" value="<?php echo $company_data['charge_amount'] ?>" autocomplete="off">
              </div>
              <div class="form-group">
                <label for="vat_charge_value">Vat Charge (%)</label>
                <input type="text" class="form-control" id="vat_charge_value" name="vat_charge_value" placeholder="Enter vat charge %" value="<?php echo $company_data['vat_charge'] ?>" autocomplete="off">
              </div>
              <div class="form-group">
                <label for="address">Address</label>
                <input type="text" class="form-control" id="address" name="address" placeholder="Enter address" value="<?php echo $company_data['address'] ?>" autocomplete="off">
              </div>
              <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter phone" value="<?php echo $company_data['phone'] ?>" autocomplete="off">
              </div>
              <div class="form-group">
                <label for="country">Country</label>
                <input type="text" class="form-control" id="country" name="country" placeholder="Enter country" value="<?php echo $company_data['country'] ?>" autocomplete="off">
              </div>
              <div class="form-group">
                <label for="currency">Currency</label>
                <?php ?>
                <select class="form-control" id="currency" name="currency">
                  <option value="">~~SELECT~~</option>

                  <?php foreach ($currency_symbols as $k => $v): ?>
                    <option value="<?php echo $k; ?>" <?php if($company_data['currency'] == $k) {
                      echo "selected";
                    } ?>><?php echo $k ?></option>
                  <?php endforeach ?>
                </select>
              </div>
              <div class="form-group">
                <label for="message">Message (Displayed on Receipt)</label>
                <textarea class="form-control" id="message" name="message" placeholder="Enter message" autocomplete="off"><?php echo $company_data['message'] ?></textarea>
              </div>


            </div>
            <!-- /.box-body -->

            <div class="box-footer">
              <button type="submit" class="btn btn-success">Update Changes</button>
            </div>
          </form>
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
    $("#companyNav").addClass('active');
    $("#message").wysihtml5();

    // Initialize FileInput (ensure fileinput JS/CSS are loaded in header/footer)
    // This provides a better file input experience
    $("#logo").fileinput({
        overwriteInitial: true,
        maxFileSize: 2048, // Max size in KB (matches controller config)
        showClose: false,
        showCaption: false,
        browseLabel: 'Browse',
        removeLabel: 'Remove',
        browseIcon: '<i class="glyphicon glyphicon-folder-open"></i>',
        removeIcon: '<i class="glyphicon glyphicon-remove"></i>',
        removeTitle: 'Cancel or reset changes',
        elErrorContainer: '#kv-avatar-errors-1',
        msgErrorClass: 'alert alert-block alert-danger',
        defaultPreviewContent: '<img src="<?php echo base_url('assets/images/default_avatar.png'); ?>" alt="Your Avatar" style="width:160px">', // Optional default placeholder
        layoutTemplates: {main2: '{preview} {remove} {browse}'},
        allowedFileExtensions: ["jpg", "png", "gif", "jpeg", "webp"] // Matches controller config
    });

  });
</script>
