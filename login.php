<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/DBConnect.php';

if (isset($_SESSION['user'])) { header('Location: dashboard.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = trim($_POST['password'] ?? '');

  $sql = "SELECT UserID, Username, Password, Role
          FROM Users
          WHERE Username = ? AND Password = ?
          LIMIT 1";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('ss', $username, $password);
  $stmt->execute();
  $res = $stmt->get_result();

  if ($row = $res->fetch_assoc()) {
    $_SESSION['user'] = [
      'id'       => (int)$row['UserID'],
      'username' => $row['Username'],
      'role'     => $row['Role']
    ];
    header('Location: dashboard.php'); exit;
  } else {
    $error = 'Invalid username or password';
  }
  $stmt->close();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login • Cybersecurity Incident Logging</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="assets/style.css" rel="stylesheet">
</head>
<body>
<div class="container auth">
  <div class="card">
    <h1>Sign in</h1>
    <?php if($error): ?>
      <div class="alert error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <form method="post" class="form" autocomplete="off">
      <label>Username
        <input type="text" name="username" autocomplete="username" required value="<?php echo isset($_POST['username'])?htmlspecialchars($_POST['username'],ENT_QUOTES,'UTF-8'):''; ?>">
      </label>
      <label>Password
        <input type="password" name="password" autocomplete="current-password" required>
      </label>
      <button class="btn" type="submit">Sign in</button>
    </form>
  </div>
</div>
</body>
</html>
