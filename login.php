<?php
require_once 'config.php';
session_start();

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Standard MD5 password
    
    $conn = get_db_connection();
    
    // Use user_table as the source of truth for reports access
    $stmt = $conn->prepare("SELECT id, username, is_admin FROM user_table WHERE username = ? AND password = ? AND enabled = 1 AND has_report_access = 1");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = $user['is_admin'];
        header('Location: index.php');
        exit;
    } else {
        $error = __('access_denied');
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('login_title'); ?> WebReporter PHP</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
<link rel="stylesheet" href="style.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <img src="logo.png" alt="WebReporter Logo" style="width: 80px; margin-bottom: 10px;"><br>
      <a href="https://github.com/cristobalmontenegro" class="h1"><b>Web</b>Reporter</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg"><?php echo __('login_msg'); ?></p>

      <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
      <?php endif; ?>

      <form action="login.php" method="post">
        <div class="input-group mb-3">
          <input type="text" name="username" class="form-control" placeholder="<?php echo __('username'); ?>" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="<?php echo __('password'); ?>" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block"><?php echo __('login_btn'); ?></button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
