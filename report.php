<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/DBConnect.php';
if (!isset($_SESSION['user'])) { header('Location: login.php'); exit; }

$reportId = (int)($_GET['id'] ?? 0);
if ($reportId <= 0) die('Invalid report');

$sql = "SELECT
          r.ReportID, r.Status, r.ResolvedByUserID, r.ToolName,
          i.IncidentID, i.Description, i.IncidentType, i.Severity, i.IPAddress, i.ReportedByUserID,
          u1.Username AS ReportedByName,
          u2.Username AS ResolvedByName
        FROM reports r
        LEFT JOIN incident i ON r.IncidentID = i.IncidentID
        LEFT JOIN users u1 ON i.ReportedByUserID = u1.UserID
        LEFT JOIN users u2 ON r.ResolvedByUserID = u2.UserID
        WHERE r.ReportID = ?
        LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $reportId);
$stmt->execute();
$rep = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$rep) die('Report not found');

include __DIR__ . '/header.php';
?>
<div class="card">
  <h2>Report #<?php echo esc($rep['ReportID']); ?> — <?php echo esc($rep['Status']); ?></h2>

  <div class="grid2">
    <div>
      <h3>Incident</h3>
      <div><b>ID:</b> <?php echo esc($rep['IncidentID']); ?></div>
      <div><b>Description:</b> <?php echo esc($rep['Description']); ?></div>
      <div><b>Incident Type:</b> <?php echo esc($rep['IncidentType']); ?></div>
      <div><b>Severity:</b> <span class="sev <?php echo strtolower(esc($rep['Severity'])); ?>"><?php echo esc($rep['Severity']); ?></span></div>
      <div><b>IP Address:</b> <code><?php echo esc($rep['IPAddress']); ?></code></div>
      <div><b>Reported By (UserID):</b> <?php echo esc($rep['ReportedByUserID']); ?>
        <?php if (!empty($rep['ReportedByName'])): ?> — <i><?php echo esc($rep['ReportedByName']); ?></i><?php endif; ?>
      </div>
    </div>
    <div>
      <h3>Resolution</h3>
      <div><b>Status:</b> <?php echo esc($rep['Status']); ?></div>
      <div><b>Tool Used:</b> <?php echo $rep['ToolName']!==null ? esc($rep['ToolName']) : 'N/A'; ?></div>
      <div><b>Resolved By (UserID):</b> <?php echo esc($rep['ResolvedByUserID']); ?>
        <?php if (!empty($rep['ResolvedByName'])): ?> — <i><?php echo esc($rep['ResolvedByName']); ?></i><?php endif; ?>
      </div>
    </div>
  </div>

  <div class="mt">
    <a class="btn" href="dashboard.php">Back to Dashboard</a>
  </div>
</div>
<?php include __DIR__ . '/footer.php'; ?>
