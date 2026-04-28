<?php require_once '../includes/auth.php'; $c = json_decode(file_get_contents('../config.json'), true);
$total = count($c['licenses']??[]);
$active = 0; foreach($c['licenses']??[] as $l) if($l['status']=='ACTIVE' && strtotime($l['expires_at'])>time()) $active++;
?><!DOCTYPE html><html><head><title>Analytics</title><link rel="stylesheet" href="../assets/css/style.css"><script src="../assets/js/charts.js"></script></head>
<body><div class="sidebar">...</div><div class="main-content"><h1>Analytics</h1><div class="stats"><div class="stat-card"><div class="number"><?php echo $total; ?></div><div class="label">Total</div></div>
<div class="stat-card"><div class="number"><?php echo $active; ?></div><div class="label">Active</div></div></div></div></body></html>