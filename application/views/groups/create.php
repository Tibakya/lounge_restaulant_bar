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
      <li class="active">Groups</li>
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
            <h3 class="box-title">Add Group</h3>
          </div>
          <form role="form" action="<?php base_url('groups/create') ?>" method="post">
            <div class="box-body">

              <?php echo validation_errors(); ?>

              <div class="form-group">
                <label for="group_name">Group Name</label>
                <input type="text" class="form-control" id="group_name" name="group_name" placeholder="Enter group name" autocomplete="off">
              </div>
              <div class="form-group">
                <label for="permission">Permission</label>

                <?php $serialize_permission = unserialize($this->config->item('permissions')); ?>

                <div class="row">
                    <div class="col-md-3">
                      <label>Users</label>
                      <ul style="list-style: none;">
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="createUser" class="minimal"> Create
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="updateUser" class="minimal"> Update
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="viewUser" class="minimal"> View
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="deleteUser" class="minimal"> Delete
                        </li>
                      </ul>

                      <label>Groups</label>
                      <ul style="list-style: none;">
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="createGroup" class="minimal"> Create
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="updateGroup" class="minimal"> Update
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="viewGroup" class="minimal"> View
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="deleteGroup" class="minimal"> Delete
                        </li>
                      </ul>

                      <label>Category</label>
                      <ul style="list-style: none;">
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="createCategory" class="minimal"> Create
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="updateCategory" class="minimal"> Update
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="viewCategory" class="minimal"> View
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="deleteCategory" class="minimal"> Delete
                        </li>
                      </ul>

                      <label>Tables</label>
                      <ul style="list-style: none;">
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="createTable" class="minimal"> Create
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="updateTable" class="minimal"> Update
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="viewTable" class="minimal"> View
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="deleteTable" class="minimal"> Delete
                        </li>
                      </ul>
                    </div>

                    <div class="col-md-3">
                      <label>Products</label>
                      <ul style="list-style: none;">
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="createProduct" class="minimal"> Create
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="updateProduct" class="minimal"> Update
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="viewProduct" class="minimal"> View
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="deleteProduct" class="minimal"> Delete
                        </li>
                      </ul>

                      <label>Orders</label>
                      <ul style="list-style: none;">
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="createOrder" class="minimal"> Create
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="updateOrder" class="minimal"> Update
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="viewOrder" class="minimal"> View
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="deleteOrder" class="minimal"> Delete
                        </li>
                      </ul>

                      <label>Inventory</label> <!-- Added Inventory Section -->
                      <ul style="list-style: none;">
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="viewInventory" class="minimal"> View
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="createInventory" class="minimal"> Create/Add Stock
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="updateInventory" class="minimal"> Update/Adjust Stock
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="deleteInventory" class="minimal"> Delete Stock Record
                        </li>
                      </ul>
                    </div>

                    <div class="col-md-3">
                      <label>Reports</label>
                      <ul style="list-style: none;">
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="viewReports" class="minimal"> View
                        </li>
                      </ul>

                      <label>Company</label>
                      <ul style="list-style: none;">
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="updateCompany" class="minimal"> Update
                        </li>
                      </ul>

                      <label>Profile</label>
                      <ul style="list-style: none;">
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="viewProfile" class="minimal"> View
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="updateProfile" class="minimal"> Update
                        </li>
                      </ul>
                    </div>

                    <div class="col-md-3">
                      <label>Setting</label>
                      <ul style="list-style: none;">
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="updateSetting" class="minimal"> Update
                        </li>
                      </ul>
                       <!-- Stores/Locations (If you create a module for this) -->
                      <!--
                      <label>Locations</label>
                      <ul style="list-style: none;">
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="createLocation" class="minimal"> Create
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="updateLocation" class="minimal"> Update
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="viewLocation" class="minimal"> View
                        </li>
                        <li>
                          <input type="checkbox" name="permission[]" id="permission" value="deleteLocation" class="minimal"> Delete
                        </li>
                      </ul>
                       -->
                    </div>
                </div> <!-- /.row -->

              </div>
              <!-- /.box-body -->

            <div class="box-footer">
              <button type="submit" class="btn btn-primary">Save Changes</button>
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
    $("#addGroupNav").addClass('active');

    $('input[type="checkbox"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue',
      radioClass   : 'iradio_minimal-blue'
    });
  });
</script>
