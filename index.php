<?php
require_once 'config.php';
require_once 'auth.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebReporter PHP</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- Main Sidebar Container -->
  <?php include 'sidebar.php'; ?>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <h1><?php echo __('nav_home'); ?></h1>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <!-- SECCIÓN DE REPORTES -->
        <h5 class="mb-3 mt-4 text-muted"><i class="fas fa-table mr-1"></i> <?php echo __('dash_reports_title'); ?></h5>
        <div class="row">
          <?php foreach ($REPORTS as $id => $report): ?>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info report-card">
              <div class="inner">
                <h5><?php echo htmlspecialchars($report['title']); ?></h5>
                <p><?php echo htmlspecialchars($report['description'] ?? 'Ver reporte detallado'); ?></p>
              </div>
              <div class="icon">
                <i class="<?php echo $report['icon']; ?>"></i>
              </div>
              <a href="report_viewer.php?id=<?php echo $id; ?>" class="small-box-footer">
                <?php echo __('dash_open'); ?> <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
          <?php endforeach; ?>
          
          <!-- Botón para nuevo reporte (Solo Admin) -->
          <?php if ($is_admin): ?>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-success report-card">
              <div class="inner">
                <h5><?php echo __('dash_new_report'); ?></h5>
    <p><?php echo __('dash_add_table'); ?></p>
              </div>
              <div class="icon">
                <i class="fas fa-plus-circle"></i>
              </div>
              <a href="https://github.com/cristobalmontenegro" class="small-box-footer" onclick="alert('Para añadir un reporte, simplemente agrega la consulta SQL en config.php'); return false;">
                <?php echo __('dash_instructions'); ?> <i class="fas fa-info-circle"></i>
              </a>
            </div>
          </div>
          <?php endif; ?>
        </div>

        <!-- SECCIÓN DE GRÁFICAS -->
        <h5 class="mb-3 mt-4 text-muted"><i class="fas fa-chart-pie mr-1"></i> <?php echo __('dash_charts_title'); ?></h5>
        <div class="row">
          <?php foreach ($CHARTS as $cid => $chart): ?>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-maroon report-card">
              <div class="inner">
                <h5><?php echo htmlspecialchars($chart['title']); ?></h5>
                <p><?php echo htmlspecialchars($chart['description'] ?? 'Ver análisis visual'); ?></p>
              </div>
              <div class="icon">
                <i class="fas fa-chart-line"></i>
              </div>
              <a href="charts.php?id=<?php echo $cid; ?>" class="small-box-footer">
                <?php echo __('dash_visualize'); ?> <i class="fas fa-eye"></i>
              </a>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

      </div>
    </section>
  </div>

  <footer class="main-footer">
    <strong>Copyright &copy; 2026 <a href="https://github.com/cristobalmontenegro" target="_blank">Cristobal Montenegro</a>.</strong>
  </footer>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script>
  // Theme Management
  function changeTheme(themeName) {
      // Remove all theme classes first
      $('body').removeClass(function (index, className) {
          return (className.match (/(^|\s)theme-\S+/g) || []).join(' ');
      });
      
      // Add new theme class
      if (themeName !== 'theme-corporate') {
          $('body').addClass(themeName);
      }
      
      // Save preference
      localStorage.setItem('webreporter_theme', themeName);
      $('#themeSelect').val(themeName);
  }

  // Load saved theme on startup
  $(document).ready(function() {
      var savedTheme = localStorage.getItem('webreporter_theme');
      if (savedTheme) {
          changeTheme(savedTheme);
      }
  });
</script>
</body>
</html>
