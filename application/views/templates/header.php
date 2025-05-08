<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $page_title; ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo base_url('assets/bower_components/bootstrap/dist/css/bootstrap.min.css') ?>">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url('assets/bower_components/font-awesome/css/font-awesome.min.css') ?>">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo base_url('assets/bower_components/Ionicons/css/ionicons.min.css') ?>">
  <!-- Theme style (AdminLTE base) -->
  <link rel="stylesheet" href="<?php echo base_url('assets/dist/css/AdminLTE.min.css') ?>">
  <!-- AdminLTE Skins -->
  <link rel="stylesheet" href="<?php echo base_url('assets/dist/css/skins/_all-skins.min.css') ?>">
  
  <!-- Morris chart -->
  <link rel="stylesheet" href="<?php echo base_url('assets/bower_components/morris.js/morris.css') ?>">
  <!-- jvectormap (Commented out as per previous discussion, uncomment if needed) -->
  <!-- <link rel="stylesheet" href="<?php echo base_url('assets/bower_components/jvectormap/jquery-jvectormap.css') ?>"> -->
  <!-- Date Picker -->
  <link rel="stylesheet" href="<?php echo base_url('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') ?>">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?php echo base_url('assets/bower_components/bootstrap-daterangepicker/daterangepicker.css') ?>">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') ?>">
  <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo base_url('assets/bower_components/select2/dist/css/select2.min.css') ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/fileinput/fileinput.min.css') ?>">

  <!-- Custom CSS for the new theme (includes top-nav and order page styles) -->
  <!-- This should be loaded AFTER AdminLTE CSS to allow overrides -->
  <link rel="stylesheet" href="<?php echo base_url('assets/css/order_create.css?v=1.2'); ?>"> <!-- Added version for cache busting -->


  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font (Keep if needed for other pages) -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

  <!-- jQuery 3 -->
  <!-- Make sure jQuery is loaded before AdminLTE and other plugins -->
  <script src="<?php echo base_url('assets/bower_components/jquery/dist/jquery.min.js') ?>"></script>
  <!-- jQuery UI 1.11.4 -->
  <script src="<?php echo base_url('assets/bower_components/jquery-ui/jquery-ui.min.js') ?>"></script>
  <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script>
    $.widget.bridge('uibutton', $.ui.button);
  </script>
  <!-- Bootstrap 3.3.7 -->
  <script src="<?php echo base_url('assets/bower_components/bootstrap/dist/js/bootstrap.min.js') ?>"></script>
  <!-- Morris.js charts -->
  <script src="<?php echo base_url('assets/bower_components/raphael/raphael.min.js') ?>"></script>
  <script src="<?php echo base_url('assets/bower_components/morris.js/morris.min.js') ?>"></script>
  <!-- Sparkline -->
  <script src="<?php echo base_url('assets/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js') ?>"></script>
  <!-- jvectormap (Commented out, uncomment if needed) -->
  <!-- <script src="<?php echo base_url('assets/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') ?>"></script> -->
  <!-- <script src="<?php echo base_url('assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') ?>"></script> -->
  <!-- jQuery Knob Chart -->
  <script src="<?php echo base_url('assets/bower_components/jquery-knob/dist/jquery.knob.min.js') ?>"></script>
  <!-- daterangepicker -->
  <script src="<?php echo base_url('assets/bower_components/moment/min/moment.min.js') ?>"></script>
  <script src="<?php echo base_url('assets/bower_components/bootstrap-daterangepicker/daterangepicker.js') ?>"></script>
  <!-- datepicker -->
  <script src="<?php echo base_url('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') ?>"></script>
  <!-- Bootstrap WYSIHTML5 -->
  <script src="<?php echo base_url('assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') ?>"></script>
  <!-- Slimscroll -->
  <script src="<?php echo base_url('assets/bower_components/jquery-slimscroll/jquery.slimscroll.min.js') ?>"></script>
  <!-- FastClick -->
  <script src="<?php echo base_url('assets/bower_components/fastclick/lib/fastclick.js') ?>"></script>
  <script src="<?php echo base_url('assets/plugins/fileinput/fileinput.min.js') ?>"></script>
  <!-- Select2 -->
  <script src="<?php echo base_url('assets/bower_components/select2/dist/js/select2.full.min.js') ?>"></script>
  <!-- ChartJS -->
  <!-- <script src="<?php echo base_url('assets/bower_components/chart.js/Chart.js') ?>"></script> -->

  <!-- DataTables -->
<script src="<?php echo base_url('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?php echo base_url('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') ?>"></script>

<script>
    // Make base_url available to all JavaScript files
    window.base_url_php = "<?php echo base_url(); ?>";
    // Pass company charge rates if they are set (for order_create.js)
    <?php if(isset($company_data['charge_amount']) || isset($service_charge_rate)): ?>
        window.companyServiceCharge = <?php echo floatval(isset($company_data['charge_amount']) ? $company_data['charge_amount'] : (isset($service_charge_rate) ? $service_charge_rate : 0)); ?>;
    <?php endif; ?>
    <?php if(isset($company_data['vat_charge']) || isset($vat_charge_rate)): ?>
        window.companyVatCharge = <?php echo floatval(isset($company_data['vat_charge']) ? $company_data['vat_charge'] : (isset($vat_charge_rate) ? $vat_charge_rate : 0)); ?>;
    <?php endif; ?>
</script>

</head>
<body class="hold-transition skin-blue layout-top-nav <?php echo isset($body_class) ? html_escape($body_class) : ''; ?>">
<div class="wrapper">

  <!-- The actual header content will be loaded from header_menu.php by Admin_Controller's render_template -->

