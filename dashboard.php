<?php
session_start();
if (!isset($_SESSION['loggedin'])) { header('Location: index.php'); exit; }
$config = json_decode(file_get_contents('config.json'), true);
$username = $_SESSION['username'];
$total = count($config['licenses'] ?? []);
$active = 0; $expired = 0;
foreach ($config['licenses'] ?? [] as $l) {
    if ($l['status'] == 'ACTIVE') strtotime($l['expires_at']) > time() ? $active++ : $expired++;
}
?>
<!DOCTYPE html>
<html>
<head><title>ZCore Dashboard</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body>
<div class="sidebar">
    <div class="sidebar-header"><h2>ZCore</h2><p>License Management</p></div>
    <div class="user-info"><div class="name"><?php echo htmlspecialchars($username); ?></div><div class="role">Administrator</div></div>
    <ul class="nav-menu">
        <li><a href="dashboard.php" class="active">📊 Dashboard</a></li>
        <li><a href="pages/licenses.php">🔑 License Management</a></li>
        <li><a href="pages/generate.php">➕ Generate License</a></li>
        <li><a href="pages/analytics.php">📈 Analytics</a></li>
        <li><a href="pages/users.php">👥 Manage Users</a></li>
        <li><a href="pages/referrals.php">🔗 Referrals</a></li>
    </ul>
</div>
<div class="main-content">
    <div class="top-bar"><h1>Dashboard</h1><a href="?logout=1" class="logout-btn">Logout</a></div>
    <div class="stats">
        <div class="stat-card"><div class="number"><?php echo $total; ?></div><div class="label">Total Licenses</div></div>
        <div class="stat-card"><div class="number" style="color:#0f0"><?php echo $active; ?></div><div class="label">Active</div></div>
        <div class="stat-card"><div class="number" style="color:#fa0"><?php echo $expired; ?></div><div class="label">Expired</div></div>
    </div>
    <div class="card"><h3>Recent Licenses</h3>
        <table><thead><tr><th>License Key</th><th>Package</th><th>Expires</th><th>Status</th></tr></thead><tbody>
        <?php foreach(array_reverse(array_slice($config['licenses'] ?? [], 0, 5)) as $lic): ?>
        <tr><td><code><?php echo $lic['license_key']; ?></code></td><td><?php echo $lic['package']; ?></td><td><?php echo date('Y-m-d', strtotime($lic['expires_at'])); ?></td>
        <td class="status-<?php echo strtolower($lic['status']); ?>"><?php echo $lic['status']; ?></td></tr>
        <?php endforeach; ?>
        </tbody></table>
    </div>
</div>
<?php if(isset($_GET['logout'])) { session_destroy(); setcookie('zcore_auth','',time()-3600,'/'); header('Location: index.php'); exit; } ?>
</body>
</html>