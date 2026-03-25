<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo and Hamburger -->
    <div class="brand-link d-flex align-items-center justify-content-between p-0" style="height: auto;">
      <a href="index.php" class="brand-link flex-grow-1 border-0">
        <img src="logo.png" alt="WebReporter Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">WebReporter PHP</span>
      </a>
      <a class="nav-link text-white px-3" data-widget="pushmenu" href="https://github.com/cristobalmontenegro" role="button">
        <i class="fas fa-bars"></i>
      </a>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
          
          <li class="nav-item">
            <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-home"></i>
              <p><?php echo __('nav_home'); ?></p>
            </a>
          </li>

          <!-- SECCIÓN DE REPORTES -->
          <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'report_viewer.php' ? 'menu-open' : ''; ?>">
            <a href="https://github.com/cristobalmontenegro" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'report_viewer.php' ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-table"></i>
              <p>
                <?php echo __('nav_reports'); ?>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <?php foreach ($REPORTS as $rid => $r): ?>
              <li class="nav-item">
                <a href="report_viewer.php?id=<?php echo $rid; ?>" 
                   class="nav-link <?php echo (isset($_GET['id']) && $_GET['id'] == $rid && basename($_SERVER['PHP_SELF']) == 'report_viewer.php') ? 'active' : ''; ?>"
                   style="padding-top: 2px; padding-bottom: 2px;">
                  <i class="nav-icon <?php echo $r['icon']; ?> text-xs"></i>
                  <p style="font-size: 0.85rem;"><?php echo htmlspecialchars($r['title']); ?></p>
                </a>
              </li>
              <?php endforeach; ?>
            </ul>
          </li>

          <!-- SECCIÓN DE GRÁFICAS -->
          <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'charts.php' ? 'menu-open' : ''; ?>">
            <a href="https://github.com/cristobalmontenegro" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'charts.php' ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-chart-pie"></i>
              <p>
                <?php echo __('nav_charts'); ?>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <?php foreach ($CHARTS as $cid => $c): ?>
              <li class="nav-item">
                <a href="charts.php?id=<?php echo $cid; ?>" 
                   class="nav-link <?php echo (isset($_GET['id']) && $_GET['id'] == $cid && basename($_SERVER['PHP_SELF']) == 'charts.php') ? 'active' : ''; ?>"
                   style="padding-top: 2px; padding-bottom: 2px;">
                  <i class="nav-icon fas fa-chart-line text-xs"></i>
                  <p style="font-size: 0.85rem;"><?php echo $c['title']; ?></p>
                </a>
              </li>
              <?php endforeach; ?>
            </ul>
          </li>

          <?php if ($is_admin): ?>
          <li class="nav-header"><?php echo __('nav_admin'); ?></li>
          <li class="nav-item">
            <a href="users.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-users-cog"></i>
              <p><?php echo __('nav_users'); ?></p>
            </a>
          </li>
          <?php endif; ?>

          <li class="nav-header"><?php echo __('nav_session'); ?></li>
          <li class="nav-item">
            <a href="logout.php" class="nav-link text-danger">
              <i class="nav-icon fas fa-sign-out-alt"></i>
              <p><?php echo __('nav_logout'); ?></p>
            </a>
          </li>
          <li class="nav-header personalization-header">PERSONALIZACIÓN</li>
          <li class="nav-item">
            <div class="nav-link" style="cursor: default;">
              <i class="nav-icon fas fa-palette text-warning"></i>
              <p>
                <?php echo __('nav_theme'); ?>
                <select id="themeSelect" class="form-control form-control-sm d-inline-block ml-2 bg-dark text-white border-secondary" style="width: auto; font-size: 0.8rem; height: 25px; padding: 0 5px; vertical-align: middle;" onchange="changeTheme(this.value)">
                    <option value="theme-corporate"><?php echo __('theme_blue'); ?></option>
                    <option value="theme-midnight"><?php echo __('theme_dark'); ?></option>
                    <option value="theme-emerald"><?php echo __('theme_green'); ?></option>
                    <option value="theme-classic"><?php echo __('theme_grey'); ?></option>
                    <option value="theme-vibrant"><?php echo __('theme_vibrant'); ?></option>
                </select>
              </p>
            </div>
          </li>
          <!-- Ko-fi Donation Button -->
          <li class="nav-item mt-3 text-center">
            <script type='text/javascript' src='https://storage.ko-fi.com/cdn/widget/Widget_2.js'></script><script type='text/javascript'>kofiwidget2.init('Support me on Ko-fi', '#72a4f2', 'U7U41W95CU');kofiwidget2.draw();</script> 
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

<style>
/* 1. Hide headers in mini mode to avoid "PERSONA" text overflow */
.sidebar-mini.sidebar-collapse .main-sidebar:not(.sidebar-focused):not(.sidebar-hover) .nav-header {
    display: none !important;
}

/* 2. Style adjustments for the theme selector select box */
.nav-link select {
    outline: none;
    box-shadow: none;
}

/* 3. Ensure palette icon is clearly visible */
.text-warning {
    color: #ffc107 !important;
}

/* 4. Fix brand text visibility during collapse animation */
.sidebar-collapse .brand-text {
    display: none;
}
</style>
