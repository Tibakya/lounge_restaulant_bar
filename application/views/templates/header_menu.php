<?php
// Fetch user data if not already available (adjust based on your actual data structure)
$user_firstname = $this->session->userdata('firstname') !== null ? $this->session->userdata('firstname') : 'Admin';
?>

<!-- New Header Structure based on template -->
<header class="top-nav"> <!-- Removed main-header, navbar classes -->

  <!-- Brand -->
  <div class="brand">
    <?php if(isset($company_logo) && $company_logo): ?>
      <img src="<?php echo $company_logo; ?>" alt="Logo" style="height: 25px; /* Adjust height */ display: inline-block; margin-right: 5px; vertical-align: middle;">
    <?php else: ?>
      🍪 <!-- Default icon if no logo -->
    <?php endif; ?>
    <?php echo isset($company_brand_name) ? html_escape($company_brand_name) : 'MDAU LOUNGE'; ?>
  </div>

  <!-- Main Navigation -->
  <nav>
    <?php if(!empty($user_permission) && in_array('viewDashboard', $user_permission)): ?>
      <a href="<?php echo base_url('dashboard') ?>">Dashboard</a>
    <?php endif; ?>

    <?php if(!empty($user_permission) && (in_array('createUser', $user_permission) || in_array('updateUser', $user_permission) || in_array('viewUser', $user_permission) || in_array('deleteUser', $user_permission))): ?>
      <a href="<?php echo base_url('users') ?>">Users</a>
      <!-- Consider if Add/Manage Users should be sub-menu or separate links -->
    <?php endif; ?>

    <?php if(!empty($user_permission) && (in_array('createGroup', $user_permission) || in_array('updateGroup', $user_permission) || in_array('viewGroup', $user_permission) || in_array('deleteGroup', $user_permission))): ?>
      <a href="<?php echo base_url('groups') ?>">Groups</a>
    <?php endif; ?>

    <?php if(!empty($user_permission) && (in_array('createCategory', $user_permission) || in_array('updateCategory', $user_permission) || in_array('viewCategory', $user_permission) || in_array('deleteCategory', $user_permission))): ?>
      <a href="<?php echo base_url('category/') ?>">Category</a>
    <?php endif; ?>

    <?php if(!empty($user_permission) && (in_array('createProduct', $user_permission) || in_array('updateProduct', $user_permission) || in_array('viewProduct', $user_permission) || in_array('deleteProduct', $user_permission))): ?>
      <a href="<?php echo base_url('products') ?>">Products</a>
      <!-- Consider if Add/Manage Products should be sub-menu or separate links -->
    <?php endif; ?>

    <?php if(!empty($user_permission) && (in_array('createOrder', $user_permission) || in_array('updateOrder', $user_permission) || in_array('viewOrder', $user_permission) || in_array('deleteOrder', $user_permission))): ?>
       <a href="<?php echo base_url('orders') ?>">Orders</a>
       <!-- Consider if Add/Manage Orders should be sub-menu or separate links -->
    <?php endif; ?>

    <?php if(!empty($user_permission) && (in_array('viewInventory', $user_permission) || in_array('createInventory', $user_permission) || in_array('updateInventory', $user_permission))): ?>
       <a href="<?php echo base_url('inventory') ?>">Inventory</a>
       <!-- Consider if Manage/Adjust Stock should be sub-menu or separate links -->
    <?php endif; ?>

    <?php if(!empty($user_permission) && (in_array('createTable', $user_permission) || in_array('updateTable', $user_permission) || in_array('viewTable', $user_permission) || in_array('deleteTable', $user_permission))): ?>
      <a href="<?php echo base_url('tables/') ?>">Tables</a>
    <?php endif; ?>

    <?php if(!empty($user_permission) && in_array('viewReports', $user_permission)): ?>
      <a href="<?php echo base_url('reports/') ?>">Reports</a>
    <?php endif; ?>

    <?php if(!empty($user_permission) && in_array('updateCompany', $user_permission)): ?>
      <a href="<?php echo base_url('company/') ?>">Company</a>
    <?php endif; ?>

  </nav>

  <!-- Admin Dropdown -->
  <div class="admin-dropdown">
    <button class="admin-btn"><?php echo html_escape($user_firstname); ?> ▼</button>
    <div class="admin-menu">
      <?php if(!empty($user_permission) && in_array('viewProfile', $user_permission)): ?>
        <a href="<?php echo base_url('users/profile/') ?>">Profile</a>
      <?php endif; ?>
      <?php if(!empty($user_permission) && in_array('updateSetting', $user_permission)): ?>
        <a href="<?php echo base_url('users/setting/') ?>">Settings</a>
      <?php endif; ?>
      <a href="<?php echo base_url('auth/logout') ?>">Logout</a>
    </div>
  </div>

</header>
