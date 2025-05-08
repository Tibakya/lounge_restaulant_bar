<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Manage
      <small>Groups</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?php echo base_url('groups/') ?>">Groups</a></li>
      <li class="active">Edit</li>
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
        <?php elseif($this->session->flashdata('errors')): ?>
          <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('errors'); ?>
          </div>
        <?php endif; ?>

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Edit Group</h3>
          </div>
          <form role="form" action="<?php base_url('groups/update') ?>" method="post">
            <div class="box-body">

              <?php echo validation_errors(); ?>

              <div class="form-group">
                <label for="group_name">Group Name</label>
                <input type="text" class="form-control" id="group_name" name="group_name" placeholder="Enter group name" value="<?php echo $group_data['group_name']; ?>" autocomplete="off">
              </div>
              <div class="form-group">
                <label for="permission">Permission</label>

                <?php $serialize_permission = unserialize($group_data['permission']); ?>

                <div class="row">
                    <div class="col-md-3">
                      <!-- Added Dashboard Section -->
                      <label>Dashboard</label>
                      <ul style="list-style: none;">
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="viewDashboard" class="minimal" <?php if($serialize_permission && in_array('viewDashboard', $serialize_permission)) { echo "checked"; } ?>> View
                        </li>
                      </ul>
                      <br/> <!-- Optional: Add some space -->
                      <!-- End Added Dashboard Section -->
                      <label>Users</label>
                      <ul style="list-style: none;">
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="createUser" class="minimal" <?php if(in_array('createUser', $serialize_permission)) { echo "checked"; } ?>> Create
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="updateUser" class="minimal" <?php if(in_array('updateUser', $serialize_permission)) { echo "checked"; } ?>> Update
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="viewUser" class="minimal" <?php if(in_array('viewUser', $serialize_permission)) { echo "checked"; } ?>> View
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="deleteUser" class="minimal" <?php if(in_array('deleteUser', $serialize_permission)) { echo "checked"; } ?>> Delete
                        </li>
                      </ul>

                      <label>Groups</label>
                      <ul style="list-style: none;">
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="createGroup" class="minimal" <?php if(in_array('createGroup', $serialize_permission)) { echo "checked"; } ?>> Create
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="updateGroup" class="minimal" <?php if(in_array('updateGroup', $serialize_permission)) { echo "checked"; } ?>> Update
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="viewGroup" class="minimal" <?php if(in_array('viewGroup', $serialize_permission)) { echo "checked"; } ?>> View
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="deleteGroup" class="minimal" <?php if(in_array('deleteGroup', $serialize_permission)) { echo "checked"; } ?>> Delete
                        </li>
                      </ul>

                      <label>Category</label>
                      <ul style="list-style: none;">
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="createCategory" class="minimal" <?php if(in_array('createCategory', $serialize_permission)) { echo "checked"; } ?>> Create
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="updateCategory" class="minimal" <?php if(in_array('updateCategory', $serialize_permission)) { echo "checked"; } ?>> Update
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="viewCategory" class="minimal" <?php if(in_array('viewCategory', $serialize_permission)) { echo "checked"; } ?>> View
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="deleteCategory" class="minimal" <?php if(in_array('deleteCategory', $serialize_permission)) { echo "checked"; } ?>> Delete
                        </li>
                      </ul>

                      <label>Tables</label>
                      <ul style="list-style: none;">
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="createTable" class="minimal" <?php if(in_array('createTable', $serialize_permission)) { echo "checked"; } ?>> Create
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="updateTable" class="minimal" <?php if(in_array('updateTable', $serialize_permission)) { echo "checked"; } ?>> Update
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="viewTable" class="minimal" <?php if(in_array('viewTable', $serialize_permission)) { echo "checked"; } ?>> View
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="deleteTable" class="minimal" <?php if(in_array('deleteTable', $serialize_permission)) { echo "checked"; } ?>> Delete
                        </li>
                      </ul>
                    </div>

                    <div class="col-md-3">
                      <label>Products</label>
                      <ul style="list-style: none;">
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="createProduct" class="minimal" <?php if(in_array('createProduct', $serialize_permission)) { echo "checked"; } ?>> Create
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="updateProduct" class="minimal" <?php if(in_array('updateProduct', $serialize_permission)) { echo "checked"; } ?>> Update
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="viewProduct" class="minimal" <?php if(in_array('viewProduct', $serialize_permission)) { echo "checked"; } ?>> View
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="deleteProduct" class="minimal" <?php if(in_array('deleteProduct', $serialize_permission)) { echo "checked"; } ?>> Delete
                        </li>
                      </ul>

                      <label>Orders</label>
                      <ul style="list-style: none;">
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="createOrder" class="minimal" <?php if(in_array('createOrder', $serialize_permission)) { echo "checked"; } ?>> Create
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="updateOrder" class="minimal" <?php if(in_array('updateOrder', $serialize_permission)) { echo "checked"; } ?>> Update
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="viewOrder" class="minimal" <?php if(in_array('viewOrder', $serialize_permission)) { echo "checked"; } ?>> View
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="deleteOrder" class="minimal" <?php if(in_array('deleteOrder', $serialize_permission)) { echo "checked"; } ?>> Delete
                        </li>
                      </ul>

                      <label>Inventory</label> <!-- Added Inventory Section -->
                      <ul style="list-style: none;">
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="viewInventory" class="minimal" <?php if(in_array('viewInventory', $serialize_permission)) { echo "checked"; } ?>> View
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="createInventory" class="minimal" <?php if(in_array('createInventory', $serialize_permission)) { echo "checked"; } ?>> Create/Add Stock
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="updateInventory" class="minimal" <?php if(in_array('updateInventory', $serialize_permission)) { echo "checked"; } ?>> Update/Adjust Stock
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="deleteInventory" class="minimal" <?php if(in_array('deleteInventory', $serialize_permission)) { echo "checked"; } ?>> Delete Stock Record
                        </li>
                      </ul>
                    </div>

                    <div class="col-md-3">
                      <label>Reports</label>
                      <ul style="list-style: none;">
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="viewReports" class="minimal" <?php if(in_array('viewReports', $serialize_permission)) { echo "checked"; } ?>> View
                        </li>
                      </ul>

                      <label>Company</label>
                      <ul style="list-style: none;">
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="updateCompany" class="minimal" <?php if(in_array('updateCompany', $serialize_permission)) { echo "checked"; } ?>> Update
                        </li>
                      </ul>

                      <label>Profile</label>
                      <ul style="list-style: none;">
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="viewProfile" class="minimal" <?php if(in_array('viewProfile', $serialize_permission)) { echo "checked"; } ?>> View
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="updateProfile" class="minimal" <?php if(in_array('updateProfile', $serialize_permission)) { echo "checked"; } ?>> Update
                        </li>
                      </ul>
                    </div>

                    <div class="col-md-3">
                      <label>Setting</label>
                      <ul style="list-style: none;">
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="updateSetting" class="minimal" <?php if(in_array('updateSetting', $serialize_permission)) { echo "checked"; } ?>> Update
                        </li>
                      </ul>
                      <!-- Stores/Locations (If you create a module for this) -->
                      <!--
                      <label>Locations</label>
                      <ul style="list-style: none;">
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="createLocation" class="minimal" <?php if(in_array('createLocation', $serialize_permission)) { echo "checked"; } ?>> Create
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="updateLocation" class="minimal" <?php if(in_array('updateLocation', $serialize_permission)) { echo "checked"; } ?>> Update
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="viewLocation" class="minimal" <?php if(in_array('viewLocation', $serialize_permission)) { echo "checked"; } ?>> View
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="deleteLocation" class="minimal" <?php if(in_array('deleteLocation', $serialize_permission)) { echo "checked"; } ?>> Delete
                        </li>
                      </ul>
                      -->
                    </div>
                </div> <!-- /.row -->


              </div>
              <!-- /.box-body -->

            <div class="box-footer">
              <button type="submit" class="btn btn-primary">Update Changes</button>
              <a href="<?php echo base_url('groups/') ?>" class="btn btn-warning">Back</a>
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
    $("#mainGroupNav").addClass('active');
    $("#manageGroupNav").addClass('active');

    $('input[type="checkbox"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue',
      radioClass   : 'iradio_minimal-blue'
    });
  });
</script>
