<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/DBConnect.php';
if (!isset($_SESSION['user']) || strcasecmp($_SESSION['user']['role'],'Specialist')!==0) {
  header('Location: login.php'); exit;
}

$incidentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($incidentId <= 0) { die('Invalid incident'); }

$stmt = $conn->prepare("SELECT IncidentID, Description, IncidentType, Severity, IPAddress
                        FROM incident WHERE IncidentID = ?");
if (!$stmt) { die("SQL prepare failed: " . $conn->error); }
$stmt->bind_param('i', $incidentId);
$stmt->execute();
$incident = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$incident) { die('Incident not found'); }

include __DIR__ . '/header.php';
?>
<div class="card">
  <h2>Fix Incident #<?php echo esc($incident['IncidentID']); ?></h2>
  <div class="muted">
    <?php echo esc($incident['Description']); ?> —
    Incident Type: <b><?php echo esc($incident['IncidentType']); ?></b>,
    Severity: <span class="sev <?php echo strtolower(esc($incident['Severity'])); ?>"><?php echo esc($incident['Severity']); ?></span>,
    IP: <code><?php echo esc($incident['IPAddress']); ?></code>
  </div>

  <form method="post" action="apply_tool.php" class="tool-grid mt">
    <input type="hidden" name="incident_id" value="<?php echo esc($incident['IncidentID']); ?>">
    <button class="btn tool" name="tool_name" value="Malwarebytes" type="submit">🛠 Malwarebytes</button>
    <button class="btn tool" name="tool_name" value="McAfee" type="submit">🛠 McAfee</button>
    <button class="btn tool" name="tool_name" value="Kaspersky" type="submit">🛠 Kaspersky</button>
  </form>
</div>
<?php include __DIR__ . '/footer.php'; ?>
