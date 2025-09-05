<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/DBConnect.php';
if (!isset($_SESSION['user'])) {
  header('Location: login.php');
  exit;
}
$u = $_SESSION['user'];
include __DIR__ . '/header.php';
?>

<?php if (strcasecmp($u['role'], 'Admin') === 0): ?>
  <div class="grid">
    <div class="card">
      <h2>Create New Incident</h2>
      <form method="post" action="incident_create.php" class="form">
        <label>Description
          <textarea name="description" required placeholder="Short description of the incident"></textarea>
        </label>
        <label>Incident Type
          <select name="type" required>
            <option value="Ransomware">Ransomware</option>
            <option value="Keylogger">Keylogger</option>
            <option value="Trojan">Trojan</option>
          </select>
        </label>
        <label>Severity
          <select name="severity" required>
            <option value="High">High</option>
            <option value="Medium">Medium</option>
            <option value="Low">Low</option>
          </select>
        </label>
        <label>IP Address
          <input type="text" name="ip" required placeholder="e.g., 192.168.1.10">
        </label>
        <button class="btn" type="submit">Save Incident</button>
      </form>
    </div>

    <div class="card">
      <h2>Existing Incidents</h2>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Description</th>
              <th>Incident Type</th>
              <th>Severity</th>
              <th>IP</th>
              <th>Reported By</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $q = "SELECT IncidentID, Description, IncidentType, Severity, IPAddress, ReportedByUserID
                  FROM incident
                  ORDER BY FIELD(Severity,'High','Medium','Low'), IncidentID DESC";
            $res = $conn->query($q);
            while ($row = $res->fetch_assoc()):
            ?>
              <tr>
                <td><?php echo esc($row['IncidentID']); ?></td>
                <td><?php echo esc($row['Description']); ?></td>
                <td><?php echo esc($row['IncidentType']); ?></td>
                <td>
                  <span class="sev <?php echo strtolower(esc($row['Severity'])); ?>">
                    <?php echo esc($row['Severity']); ?>
                  </span>
                </td>
                <td><?php echo esc($row['IPAddress']); ?></td>
                <td><?php echo esc($row['ReportedByUserID']); ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

<?php else: /* Specialist */ ?>

  <div class="grid">
    <div class="card highlight">
      <h2>Highest Priority Incident</h2>
      <?php
      $top = $conn->query("SELECT IncidentID, Description, IncidentType, Severity, IPAddress, ReportedByUserID
                           FROM incident
                           ORDER BY FIELD(Severity,'High','Medium','Low'), IncidentID DESC
                           LIMIT 1")->fetch_assoc();
      if ($top):
      ?>
        <div class="incident">
          <div>
            <b>#<?php echo esc($top['IncidentID']); ?></b> —
            <?php echo esc($top['Description']); ?>
          </div>
          <div>
            Incident Type: <b><?php echo esc($top['IncidentType']); ?></b> ·
            Severity:
            <span class="sev <?php echo strtolower(esc($top['Severity'])); ?>">
              <?php echo esc($top['Severity']); ?>
            </span> ·
            IP: <code><?php echo esc($top['IPAddress']); ?></code>
          </div>
          <div class="actions mt">
            <a class="btn warn" href="flag_incident.php?id=<?php echo (int)$top['IncidentID']; ?>">Flag</a>
            <a class="btn" href="choose_tool.php?id=<?php echo (int)$top['IncidentID']; ?>">Fix with Tools</a>
          </div>
        </div>
      <?php else: ?>
        <div class="muted">No incidents yet.</div>
      <?php endif; ?>
    </div>

    <div class="card">
      <h2>All Incidents</h2>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Description</th>
              <th>Incident Type</th>
              <th>Severity</th>
              <th>IP</th>
              <th>Reported By</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $q = "SELECT IncidentID, Description, IncidentType, Severity, IPAddress, ReportedByUserID
                  FROM incident
                  ORDER BY FIELD(Severity,'High','Medium','Low'), IncidentID DESC";
            $res = $conn->query($q);
            while ($row = $res->fetch_assoc()):
            ?>
              <tr>
                <td><?php echo esc($row['IncidentID']); ?></td>
                <td><?php echo esc($row['Description']); ?></td>
                <td><?php echo esc($row['IncidentType']); ?></td>
                <td>
                  <span class="sev <?php echo strtolower(esc($row['Severity'])); ?>">
                    <?php echo esc($row['Severity']); ?>
                  </span>
                </td>
                <td><code><?php echo esc($row['IPAddress']); ?></code></td>
                <td><?php echo esc($row['ReportedByUserID']); ?></td>
                <td class="nowrap">
                  <a class="btn sm warn" href="flag_incident.php?id=<?php echo (int)$row['IncidentID']; ?>">Flag</a>
                  <a class="btn sm" href="choose_tool.php?id=<?php echo (int)$row['IncidentID']; ?>">Fix</a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

<?php endif; ?>

<?php include __DIR__ . '/footer.php'; ?>
