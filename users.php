<?php require_once '../includes/auth.php'; $c = json_decode(file_get_contents('../config.json'), true);
if($_POST && $_POST['new_user']){
    $c['users'][$_POST['username']] = ['password'=>password_hash($_POST['password'],PASSWORD_DEFAULT), 'role'=>$_POST['role'], 'created_at'=>date('Y-m-d H:i:s')];
    file_put_contents('../config.json', json_encode($c, JSON_PRETTY_PRINT));
}
?><!DOCTYPE html><html><head><title>Users</title><link rel="stylesheet" href="../assets/css/style.css"></head>
<body><div class="sidebar">...</div><div class="main-content"><h1>User Management</h1><form method="POST"><input type="text" name="username" placeholder="Username"><input type="password" name="password"><select name="role"><option>admin</option><option>user</option></select><button type="submit" name="new_user">Add User</button></form>
<table><tr><th>Username</th><th>Role</th><th>Created</th></tr><?php foreach($c['users'] as $u=>$d): ?><tr><td><?php echo $u; ?></td><td><?php echo $d['role']; ?></td><td><?php echo $d['created_at']; ?></td></tr><?php endforeach; ?></table></div></body></html>