<?php
$config = json_decode(file_get_contents('config.json'), true);
$valid_codes = ['ZENIN2024','BLACKBOX','ZCORE'];
$error = $success = '';
if($_POST){
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    $code = $_POST['code'] ?? '';
    if(in_array($code, $valid_codes)){
        if(!isset($config['users'][$user])){
            $config['users'][$user] = ['password'=>password_hash($pass,PASSWORD_DEFAULT),'role'=>'user','created_at'=>date('Y-m-d H:i:s')];
            file_put_contents('config.json', json_encode($config, JSON_PRETTY_PRINT));
            $success = "Registration successful! You can now login.";
        } else $error = "Username exists";
    } else $error = "Invalid registration code";
}
?>
<!DOCTYPE html>
<html><head><title>Register</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body class="login-page"><div class="login-container"><div class="login-card">
<h2>Register</h2>
<?php if($error) echo "<div class='error'>$error</div>"; if($success) echo "<div class='success'>$success</div>"; ?>
<form method="POST"><input type="text" name="username" placeholder="Username" required><input type="password" name="password" placeholder="Password" required><input type="text" name="code" placeholder="Registration Code" required><button type="submit">Register</button></form>
<div class="register-link"><a href="index.php">Back to Login</a></div>
</div></div></body></html>