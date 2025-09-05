<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/DBConnect.php';
if (!isset($_SESSION['user']) || strcasecmp($_SESSION['user']['role'],'Specialist')!==0) {
  header('Location: login.php'); exit;
}

$incidentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($incidentId <= 0) { die('Invalid incident'); }

// confirm the incident exists in *incident* table
$chk = $conn->prepare("SELECT IncidentID FROM incident WHERE IncidentID = ?");
if (!$chk) { die("SQL prepare failed: " . $conn->error); }
$chk->bind_param('i', $incidentId);
$chk->execute();
$exists = $chk->get_result()->fetch_row();
$chk->close();

if (!$exists) { die('Incident not found'); }

$status = 'Flagged';
$toolName = NULL; // string column, NULL when just flagged
$resolvedBy = (int)$_SESSION['user']['id'];

$ins = $conn->prepare("INSERT INTO reports (IncidentID, ToolName, Status, ResolvedByUserID)
                       VALUES (?, ?, ?, ?)");
if (!$ins) { die("SQL prepare failed: " . $conn->error); }
$ins->bind_param('issi', $incidentId, $toolName, $status, $resolvedBy);
$ins->execute();
$reportId = $conn->insert_id;
$ins->close();

header('Location: report.php?id=' . $reportId);
exit;
