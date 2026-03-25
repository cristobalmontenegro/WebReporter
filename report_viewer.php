<?php
require_once 'config.php';
require_once 'auth.php';

$report_id = $_GET['id'] ?? '';
if (!$report_id || !isset($REPORTS[$report_id])) {
    die(__('report_not_found'));
}

$report = $REPORTS[$report_id];
$conn = get_db_connection();
// 1. Get columns efficiently (LIMIT 0)

$columns = [];
$error_message = '';
$col_query = $conn->query($report['sql'] . " LIMIT 0");
if ($col_query) {
    $finfo = $col_query->fetch_fields();
    foreach ($finfo as $val) {
        $columns[] = $val->name;
    }
} else {
    $error_message = __('report_db_error') . ": " . $conn->error;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $report['title']; ?> - WebReporter PHP</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="hold-transition sidebar-mini">
<div id="loading-overlay">
    <div class="text-center">
        <i class="fas fa-3x fa-sync fa-spin text-primary"></i>
        <div class="mt-2"><?php echo __('report_processing'); ?></div>
    </div>
</div>
<div class="wrapper">

  <!-- Main Sidebar Container -->
  <?php include 'sidebar.php'; ?>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><?php echo $report['title']; ?></h1>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <?php if (!empty($error_message)): ?>
          <div class="alert alert-danger">
            <h5><i class="icon fas fa-ban"></i> <?php echo __('report_db_error'); ?></h5>
            <?php echo htmlspecialchars($error_message); ?>
            <br><br>
            <p><?php echo __('report_db_error_msg'); ?></p>
          </div>
          <script>
            window.onload = function() {
              document.getElementById('loading-overlay').style.display = 'none';
            }
          </script>
        <?php else: ?>
          <div id="reportTableContainer" class="card">
            <div class="card-header">
              <h3 class="card-title"><?php echo __('report_list_data'); ?></h3>
              <div id="export-buttons" class="float-right btn-group"></div>
            </div>
            <div class="card-body">
              <table id="reportTable" class="table table-bordered table-striped" style="width:100%">
                <thead>
                  <tr>
                    <th style="width: 30px;"></th>
                    <?php foreach ($columns as $col): ?>
                    <th><?php echo htmlspecialchars($col); ?></th>
                    <?php endforeach; ?>
                  </tr>
                </thead>
                <tbody>
                  <!-- Contenido cargado dinámicamente -->
                </tbody>
              </table>
            </div>
            <!-- AdminLTE Overlay for processing -->
            <div id="card-overlay" class="overlay d-none">
              <i class="fas fa-2x fa-sync-alt fa-spin text-primary"></i>
              <div class="text-bold ml-2"><?php echo __('report_processing'); ?></div>
            </div>
          </div>

          <div id="debug-info" class="card card-outline card-info mt-3 d-none">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-code"></i> <?php echo __('report_debug'); ?></h3>
            </div>
            <div class="card-body">
              <pre id="debug-content" style="background: #f8f9fa; padding: 10px; border: 1px solid #ddd;"></pre>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </section>
  </div>

  <footer class="main-footer">
    <strong>Copyright &copy; 2026 <a href="https://github.com/cristobalmontenegro" target="_blank">Cristobal Montenegro</a>.</strong>
  </footer>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
<?php if (empty($error_message)): ?>
<!-- DataTables & Plugins -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.colVis.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<script>
  $(function () {
    // 1. Initialize Debug Info FIRST so we can see what happens even if DataTables crashes
    $('#debug-info').removeClass('d-none');
    $('#debug-content').text("Iniciando tabla...");

    var table = $("#reportTable").DataTable({
      "processing": true,
      "serverSide": true,
      "ajax": {
          "url": "fetch_data.php?id=<?php echo $report_id; ?>",
          "type": "POST", // POST is safer for large DataTables metadata
          "timeout": 60000, 
          "data": function ( d ) {
              // Standard mapping but explicit to avoid overhead
              return {
                  draw: d.draw,
                  start: d.start,
                  length: d.length,
                  search: d.search,
                  order: d.order
              };
          },
          "error": function (xhr, error, thrown) {
              $('#loading-overlay').fadeOut();
              $('#card-overlay').addClass('d-none');
              var msg = "Error AJAX: " + error + " (" + thrown + ")";
              if (xhr.responseText) {
                  msg += "\n\nRespuesta del servidor:\n" + xhr.responseText.substring(0, 500);
              }
              $('#debug-content').text(msg);
          }
      },
      "columns": [
        { "data": null, "defaultContent": "", "orderable": false, "className": 'dtr-control' },
        <?php foreach ($columns as $index => $col): ?>
        { 
            "title": <?php echo json_encode($col); ?>, 
            "data": <?php echo $index; ?>,
            "defaultContent": ""
        }<?php echo ($index < count($columns)-1) ? ',' : ''; ?>
        <?php endforeach; ?>
      ],
      "responsive": true,
      "autoWidth": false,
      "deferRender": true,
      "pageLength": 25,
      "dom": 'lBfrtip',
      "language": {
          "url": "<?php echo (APP_LANG == 'es') ? '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json' : ''; ?>"
      },
      "buttons": [
        { 
          text: '<i class="fas fa-file-excel"></i> Excel', 
          className: 'btn btn-success btn-sm',
          action: function ( e, dt, node, config ) {
              var search = dt.search();
              window.location.href = 'fetch_data.php?id=<?php echo $report_id; ?>&export=excel&search=' + encodeURIComponent(search);
          }
        },
        { 
          text: '<i class="fas fa-print"></i> PDF / Imprimir', 
          className: 'btn btn-danger btn-sm',
          action: function ( e, dt, node, config ) {
              var search = dt.search();
              window.open('fetch_data.php?id=<?php echo $report_id; ?>&export=print&search=' + encodeURIComponent(search), '_blank');
          }
        },
        { extend: 'colvis', text: 'Columnas', className: 'btn btn-secondary btn-sm' }
      ],
      "drawCallback": function(settings) {
          $('#loading-overlay').fadeOut();
          $('#card-overlay').addClass('d-none');
          var json = table.ajax.json();
          if (json) {
              $('#debug-content').text("Estado: OK\nRegistros totales: " + json.recordsTotal + "\nMostrando: " + json.data.length);
          }
      }
    });

    // Fix column alignment on window resize
    $(window).on('resize', function () {
        if (table) table.columns.adjust().responsive.recalc();
    });

    // Also adjust on initial load and draw
    table.on('column-visibility.dt', function () {
        table.columns.adjust().responsive.recalc();
    });

    // Final Failsafe
    setTimeout(function() {
        if (table) table.columns.adjust();
        if ($('#loading-overlay').is(':visible')) {
            $('#loading-overlay').fadeOut();
            $('#debug-content').append("\n\n(Aviso: Carga lenta detectada - ver consola)");
        }
    }, 15000);
  });

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
<?php endif; ?>
</body>
</html>
