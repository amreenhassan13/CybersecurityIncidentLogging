<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (isset($_SESSION['user'])) { header('Location: dashboard.php'); exit; }
header('Location: login.php'); exit;
