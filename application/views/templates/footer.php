<footer class="main-footer">
  <div class="pull-right hidden-xs">
    <b>Version</b> 1.1.0
  </div>
  <strong>&copy; 2018 - <?php echo date('Y'); ?> |</strong> All rights
  reserved - Restaurant & Lounge Management System
</footer>

<!-- Add the sidebar's background. This div must be placed
     immediately after the control sidebar -->
<div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<!-- Moved jQuery loading to header.php for better dependency management -->
<!-- <script src="<?php echo base_url('assets/bower_components/jquery/dist/jquery.min.js') ?>"></script> -->
<!-- jQuery UI 1.11.4 -->
<!-- <script src="<?php echo base_url('assets/bower_components/jquery-ui/jquery-ui.min.js') ?>"></script> -->
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<!-- <script>
// $.widget.bridge('uibutton', $.ui.button); // Already in header
</script> -->
<!-- Bootstrap 3.3.7 -->
<!-- <script src="<?php echo base_url('assets/bower_components/bootstrap/dist/js/bootstrap.min.js') ?>"></script> --> <!-- Already in header -->
<!-- Morris.js charts -->
<!-- <script src="<?php echo base_url('assets/bower_components/raphael/raphael.min.js') ?>"></script> --> <!-- Already in header -->
<!-- <script src="<?php echo base_url('assets/bower_components/morris.js/morris.min.js') ?>"></script> --> <!-- Already in header -->
<!-- Sparkline -->
<!-- <script src="<?php echo base_url('assets/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js') ?>"></script> --> <!-- Already in header -->
<!-- jvectormap -->
<!-- <script src="<?php echo base_url('assets/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') ?>"></script> --> <!-- Already in header -->
<!-- <script src="<?php echo base_url('assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') ?>"></script> --> <!-- Already in header -->
<!-- jQuery Knob Chart -->
<!-- <script src="<?php echo base_url('assets/bower_components/jquery-knob/dist/jquery.knob.min.js') ?>"></script> --> <!-- Already in header -->
<!-- daterangepicker -->
<!-- <script src="<?php echo base_url('assets/bower_components/moment/min/moment.min.js') ?>"></script> --> <!-- Already in header -->
<!-- <script src="<?php echo base_url('assets/bower_components/bootstrap-daterangepicker/daterangepicker.js') ?>"></script> --> <!-- Already in header -->
<!-- datepicker -->
<!-- <script src="<?php echo base_url('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') ?>"></script> --> <!-- Already in header -->
<!-- Bootstrap WYSIHTML5 -->
<!-- <script src="<?php echo base_url('assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') ?>"></script> --> <!-- Already in header -->
<!-- Slimscroll -->
<!-- <script src="<?php echo base_url('assets/bower_components/jquery-slimscroll/jquery.slimscroll.min.js') ?>"></script> --> <!-- Already in header -->
<!-- FastClick -->
<!-- <script src="<?php echo base_url('assets/bower_components/fastclick/lib/fastclick.js') ?>"></script> --> <!-- Already in header -->
<!-- AdminLTE App -->
<script src="<?php echo base_url('assets/dist/js/adminlte.min.js') ?>"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<!-- <script src="<?php echo base_url('assets/dist/js/pages/dashboard.js') ?>"></script> -->
<!-- AdminLTE for demo purposes -->
<script src="<?php echo base_url('assets/dist/js/demo.js') ?>"></script>
<!-- ChartJS -->
<!-- <script src="<?php echo base_url('assets/bower_components/chart.js/Chart.js') ?>"></script> --> <!-- Already in header -->
<!-- Select2 -->
<!-- <script src="<?php echo base_url('assets/bower_components/select2/dist/js/select2.full.min.js') ?>"></script> --> <!-- Already in header -->
<!-- Fileinput -->
<!-- <script src="<?php echo base_url('assets/plugins/fileinput/fileinput.min.js') ?>"></script> --> <!-- Already in header -->
<!-- DataTables -->
<!-- <script src="<?php echo base_url('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') ?>"></script> --> <!-- Already in header -->
<!-- <script src="<?php echo base_url('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') ?>"></script> --> <!-- Already in header -->

<!-- Load Order Create/Edit specific JS -->
<?php if ($this->uri->segment(1) == 'orders' && (in_array($this->uri->segment(2), ['create', 'edit', '', 'index', null]))): ?>
<script src="<?php echo base_url('assets/js/order_create.js?v=1.2') ?>"></script> <!-- Incremented version for cache busting -->
<?php endif; ?>

</body>
</html>
