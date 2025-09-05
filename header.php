<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/DBConnect.php';
function esc($v){ return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Cybersecurity Incident Logging</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="assets/style.css" rel="stylesheet">
</head>
<body>
<header class="nav">
  <div class="brand"> Cybersecurity Incident Logging</div>
  <nav>
    <?php if(isset($_SESSION['user'])): $u=$_SESSION['user']; ?>
      <span class="badge <?php echo strtolower(esc($u['role'])); ?>"><?php echo esc($u['role']); ?></span>
      <span class="user">User: <?php echo esc($u['username']); ?> (ID: <?php echo esc($u['id']); ?>)</span>
      <a class="btn" href="dashboard.php">Dashboard</a>
      <a class="btn outline" href="logout.php">Logout</a>
    <?php else: ?>
      <a class="btn" href="login.php">Login</a>
    <?php endif; ?>
  </nav>
</header>
<main class="container">
