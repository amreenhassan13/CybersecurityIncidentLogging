<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/DBConnect.php';
if (!isset($_SESSION['user']) || strcasecmp($_SESSION['user']['role'],'Specialist')!==0) {
  header('Location: login.php'); exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: dashboard.php'); exit; }

$incidentId = isset($_POST['incident_id']) ? (int)$_POST['incident_id'] : 0;
$toolName   = trim($_POST['tool_name'] ?? '');
if ($incidentId <= 0 || $toolName === '') { die('Invalid incident'); }

// confirm the incident exists in *incident* table
$chk = $conn->prepare("SELECT IncidentID FROM incident WHERE IncidentID = ?");
if (!$chk) { die("SQL prepare failed: " . $conn->error); }
$chk->bind_param('i', $incidentId);
$chk->execute();
$exists = $chk->get_result()->fetch_row();
$chk->close();

if (!$exists) { die('Incident not found'); }

// optional: record chosen tool in tools table (your schema)
$insTool = $conn->prepare("INSERT INTO tools (ToolName, IncidentID) VALUES (?, ?)");
if (!$insTool) { die("SQL prepare failed: " . $conn->error); }
$insTool->bind_param('si', $toolName, $incidentId);
$insTool->execute();
$insTool->close();

$status = 'Fixed';
$resolvedBy = (int)$_SESSION['user']['id'];

$insRep = $conn->prepare("INSERT INTO reports (IncidentID, ToolName, Status, ResolvedByUserID)
                          VALUES (?, ?, ?, ?)");
if (!$insRep) { die("SQL prepare failed: " . $conn->error); }
$insRep->bind_param('issi', $incidentId, $toolName, $status, $resolvedBy);
$insRep->execute();
$reportId = $conn->insert_id;
$insRep->close();

header('Location: report.php?id=' . $reportId);
exit;
