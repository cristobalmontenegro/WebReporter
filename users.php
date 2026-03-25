<?php
require_once 'config.php';
require_once 'auth.php';
restrict_to_admin();

$conn = get_db_connection();

// Handle AJAX toggle
if (isset($_POST['toggle_user_id'])) {
    validate_csrf($_POST['csrf_token'] ?? '');
    $uid = intval($_POST['toggle_user_id']);
    $status = intval($_POST['status']);
    $stmt = $conn->prepare("UPDATE user_table SET has_report_access = ? WHERE id = ?");
    $stmt->bind_param("ii", $status, $uid);
    $success = $stmt->execute();
    echo json_encode(['success' => $success]);
    exit;
}

$users = $conn->query("SELECT id, username, realname, email, is_admin, has_report_access, enabled FROM user_table ORDER BY username ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('users_title'); ?> - WebReporter PHP</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="style.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Main Sidebar Container -->
  <?php include 'sidebar.php'; ?>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <h1><?php echo __('users_title'); ?></h1>
        <p class="text-muted"><?php echo __('users_subtitle'); ?></p>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="card user-card">
          <div class="card-header border-0 bg-white">
            <h3 class="card-title font-weight-bold"><?php echo __('users_list'); ?></h3>
            <div class="card-tools">
              <div class="input-group input-group-sm" style="width: 250px;">
                <input type="text" id="customSearchInput" class="form-control float-right" placeholder="<?php echo __('users_search'); ?>">
                <div class="input-group-append">
                  <button type="submit" class="btn btn-default">
                    <i class="fas fa-search"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="card-body p-0">
            <table id="usersTable" class="table table-hover mb-0 w-100">
              <thead class="thead-light">
                <tr>
                  <th><?php echo __('users_col_username'); ?></th>
                  <th><?php echo __('users_col_realname'); ?></th>
                  <th><?php echo __('users_col_email'); ?></th>
                   <th><?php echo __('users_col_admin'); ?></th>
                  <th><?php echo __('users_col_status'); ?></th>
                  <th class="text-center"><?php echo __('users_col_access'); ?></th>
                </tr>
              </thead>
              <tbody>
                <?php while ($u = $users->fetch_assoc()): ?>
                <tr>
                  <td><strong><?php echo htmlspecialchars($u['username']); ?></strong></td>
                  <td><?php echo htmlspecialchars($u['realname']); ?></td>
                  <td><a href="mailto:<?php echo htmlspecialchars($u['email']); ?>"><?php echo htmlspecialchars($u['email']); ?></a></td>
                   <td><span class="badge badge-<?php echo $u['is_admin'] ? 'primary' : 'secondary'; ?>"><?php echo $u['is_admin'] ? __('users_col_admin') : 'User'; ?></span></td>
                  <td>
                    <span class="badge badge-<?php echo $u['enabled'] ? 'success' : 'danger'; ?>">
                        <?php echo $u['enabled'] ? __('users_active') : __('users_inactive'); ?>
                    </span>
                  </td>
                  <td class="text-center">
                    <?php if ($u['id'] == $_SESSION['user_id']): ?>
                        <span class="badge badge-info" title="<?php echo __('users_admin_hint'); ?>"><?php echo __('users_you_admin'); ?></span>
                    <?php else: ?>
                        <label class="switch">
                          <input type="checkbox" <?php echo $u['has_report_access'] ? 'checked' : ''; ?> 
                                 onchange="toggleAccess(<?php echo $u['id']; ?>, this.checked ? 1 : 0)">
                          <span class="slider"></span>
                        </label>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
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
<!-- DataTables & Plugins -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script>
function toggleAccess(userId, status) {
    $.post('users.php', { 
        toggle_user_id: userId, 
        status: status,
        csrf_token: '<?php echo $csrf_token; ?>'
    }, function(response) {
        console.log("Status updated for user " + userId + ": " + status);
    }, 'json');
}

$(document).ready(function() {
    // Initialize DataTable
    var table = $('#usersTable').DataTable({
        "dom": "lrtip", // Removed 'f' (filter) to use our custom one
        "responsive": true,
        "autoWidth": false,
        "language": {
            "url": "<?php echo (APP_LANG == 'es') ? '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json' : ''; ?>"
        },
        "order": [[0, "asc"]]
    });

    // Custom Advanced Search Logic
    var customSearchText = '';
    
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            if (!customSearchText) return true;

            var keywords = customSearchText.toLowerCase().split(/\s+/).filter(k => k.length > 0);
            var rowContent = data.join(' ').toLowerCase();

            for (var i = 0; i < keywords.length; i++) {
                var k = keywords[i];
                if (k.startsWith('!')) {
                    var exclude = k.substring(1).trim();
                    if (exclude && rowContent.includes(exclude)) return false;
                } else {
                    if (!rowContent.includes(k)) return false;
                }
            }
            return true;
        }
    );

    // Custom search input event
    $('#customSearchInput').on('keyup input', function() {
        customSearchText = $(this).val();
        table.draw();
    });
    var savedTheme = localStorage.getItem('webreporter_theme');
    if (savedTheme && savedTheme !== 'theme-corporate') {
        $('body').addClass(savedTheme);
    }
    $('#themeSelect').val(savedTheme || 'theme-corporate');
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
