<?php require_once '../includes/auth.php'; $config = json_decode(file_get_contents('../config.json'), true); ?>
<!DOCTYPE html><html><head><title>Licenses</title><link rel="stylesheet" href="../assets/css/style.css"></head>
<body><div class="sidebar"><!-- same sidebar as dashboard --></div><div class="main-content"><h1>License List</h1>
<table><thead><tr><th>Key</th><th>Package</th><th>Expires</th><th>Status</th><th>Devices</th><th>Actions</th></tr></thead>
<tbody><?php foreach(array_reverse($config['licenses']??[]) as $k=>$l): ?>
<tr><td><code><?php echo $l['license_key']; ?></code></td><td><?php echo $l['package']; ?></td><td><?php echo $l['expires_at']; ?></td>
<td class="status-<?php echo strtolower($l['status']); ?>"><?php echo $l['status']; ?></td>
<td><?php echo count($l['devices']??[]); ?></td>
<td><a href="?revoke=<?php echo $k; ?>" onclick="return confirm('Revoke?')">Revoke</a> | <a href="?delete=<?php echo $k; ?>" onclick="return confirm('Delete?')">Delete</a></td></tr>
<?php endforeach; ?></tbody></table></div></body></html>
<?php if(isset($_GET['revoke'])){ $i=$_GET['revoke']; $config['licenses'][$i]['status']='REVOKED'; file_put_contents('../config.json', json_encode($config,JSON_PRETTY_PRINT)); header('Location: licenses.php'); }
if(isset($_GET['delete'])){ array_splice($config['licenses'], $_GET['delete'], 1); file_put_contents('../config.json', json_encode($config,JSON_PRETTY_PRINT)); header('Location: licenses.php'); } ?>