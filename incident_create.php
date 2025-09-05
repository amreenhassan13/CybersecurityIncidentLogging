<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/DBConnect.php';

// Only Admin can create incidents
if (!isset($_SESSION['user']) || strcasecmp($_SESSION['user']['role'], 'Admin') !== 0) {
  header('Location: login.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: dashboard.php');
  exit;
}

$desc = trim($_POST['description'] ?? '');
$type = trim($_POST['type'] ?? '');
$sev  = trim($_POST['severity'] ?? '');
$ip   = trim($_POST['ip'] ?? '');

// ▶ If you still see "Missing fields", temporarily uncomment the next line:
// die("DEBUG => desc='$desc' | type='$type' | sev='$sev' | ip='$ip'");

if ($desc === '' || $type === '' || $sev === '' || $ip === '') {
  die('Missing fields');
}

if (!isset($_SESSION['user']['id'])) {
  die('Error: not logged in. Please log in again.');
}
$reportedBy = (int)$_SESSION['user']['id'];


$sql = "INSERT INTO incident (Description, IncidentType, Severity, IPAddress, ReportedByUserID)
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) { die("SQL prepare failed: " . $conn->error); }
$stmt->bind_param('ssssi', $desc, $type, $sev, $ip, $reportedBy);
$stmt->execute();
$stmt->close();

header('Location: dashboard.php');
exit;
