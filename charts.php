<?php
require_once 'config.php';
require_once 'auth.php';

$conn = get_db_connection();

// Logic to fetch chart data
$chart_data = [];
foreach ($CHARTS as $id => $cfg) {
    if (!isset($REPORTS[$cfg['report']])) continue;
    
    // Check if we are focusing on a single chart
    if (isset($_GET['id']) && $_GET['id'] !== $id) continue;
    
    // Use chart-specific SQL if available, otherwise fallback to report SQL
    $sql = isset($cfg['sql']) ? $cfg['sql'] : $REPORTS[$cfg['report']]['sql'];
    $result = $conn->query($sql);
    
    $labels = [];
    $data = [];
    $raw = [];
    $error = null;
    
    if (!$result) {
        $error = $conn->error;
    } else {
        while ($row = $result->fetch_assoc()) {
            if (isset($cfg['datasets'])) {
                $labels[] = $row[$cfg['label_col']];
                foreach ($cfg['datasets'] as $ds) {
                    $raw[$ds['label']][] = $row[$ds['col']];
                }
            } else {
                $labels[] = $row[$cfg['label_col']];
                $data[] = $row[$cfg['value_col']];
            }
        }
    }
    
    $chart_data[$id] = [
        'title' => $cfg['title'],
        'type' => $cfg['type'],
        'labels' => $labels,
        'data' => $data,
        'datasets' => $raw,
        'config' => $cfg,
        'error' => $error
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Gráficas - WebReporter PHP</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
        <h1><i class="fas fa-chart-line mr-2"></i>Panel de Gráficas</h1>
        <p class="text-muted">Resumen visual dinámico basado en las mismas consultas de los reportes.</p>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <?php 
          $count = count($chart_data);
          foreach ($chart_data as $cid => $data): 
              $grid_class = ($count == 1) ? 'col-12' : 'col-md-6';
              $container_class = ($count == 1) ? 'full-chart' : '';
          ?>
          <div class="<?php echo $grid_class; ?>">
            <div id="<?php echo $cid; ?>" class="card chart-card">
              <div class="card-header border-0 bg-white">
                <h3 class="card-title font-weight-bold">
                    <?php echo htmlspecialchars($data['title']); ?>
                    <br><small class="text-muted font-weight-normal"><?php echo htmlspecialchars($data['config']['description'] ?? ''); ?></small>
                </h3>
                <?php if ($count == 1): ?>
                <div class="card-tools">
                   <a href="charts.php" class="btn btn-tool" title="Ver todos"><i class="fas fa-th mr-1"></i> Ver Todos</a>
                </div>
                <?php endif; ?>
              </div>
              <div class="card-body">
                <?php if ($data['error']): ?>
                    <div class="alert alert-danger">
                        <h5><i class="icon fas fa-ban"></i> Error SQL</h5>
                        <p class="mb-0"><?php echo $data['error']; ?></p>
                    </div>
                <?php else: ?>
                    <div class="chart-container <?php echo $container_class; ?>">
                      <canvas id="canvas_<?php echo $cid; ?>"></canvas>
                    </div>
                <?php endif; ?>
              </div>
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

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  $(document).ready(function() {
      // Load Theme
      var savedTheme = localStorage.getItem('webreporter_theme');
      if (savedTheme && savedTheme !== 'theme-corporate') {
          $('body').addClass(savedTheme);
      }

      <?php foreach ($chart_data as $cid => $cdata): ?>
      <?php if (!$cdata['error'] && !empty($cdata['labels'])): ?>
      new Chart(document.getElementById('canvas_<?php echo $cid; ?>').getContext('2d'), {
          type: '<?php echo $cdata['type']; ?>',
          data: {
              labels: <?php echo json_encode($cdata['labels']); ?>,
              datasets: <?php 
                if (isset($cdata['config']['datasets'])) {
                    $ds_list = [];
                    foreach ($cdata['config']['datasets'] as $ds_cfg) {
                        $ds_list[] = [
                            'label' => $ds_cfg['label'],
                            'data' => $cdata['datasets'][$ds_cfg['label']],
                            'backgroundColor' => $ds_cfg['color'],
                            'borderWidth' => 0
                        ];
                    }
                    echo json_encode($ds_list);
                } else {
                    echo json_encode([[
                        'label' => 'Total',
                        'data' => $cdata['data'],
                        'backgroundColor' => ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6610f2', '#e83e8c', '#fd7e14'],
                        'borderWidth' => 0
                    ]]);
                }
              ?>
          },
          options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                  legend: { display: <?php echo $cdata['type'] === 'pie' ? 'true' : 'false'; ?> }
              },
              scales: <?php echo $cdata['type'] === 'bar' ? "{ y: { beginAtZero: true } }" : "{}"; ?>
          }
      });
      <?php endif; ?>
      <?php endforeach; ?>
  });

  function changeTheme(themeName) {
      $('body').removeClass(function (index, className) {
          return (className.match (/(^|\s)theme-\S+/g) || []).join(' ');
      });
      if (themeName !== 'theme-corporate') {
          $('body').addClass(themeName);
      }
      localStorage.setItem('webreporter_theme', themeName);
      $('#themeSelect').val(themeName);
  }
</script>
</body>
</html>
