<?php require_once '../includes/auth.php'; $config = json_decode(file_get_contents('../config.json'), true);
if($_POST){
    $key = generateLicenseKey(12);
    $expiry = $_POST['expiry_days']??30;
    $config['licenses'][] = [
        'license_key'=>$key,
        'package'=>$_POST['package']??'com.example.app',
        'created_by'=>$_SESSION['username'],
        'created_at'=>date('Y-m-d H:i:s'),
        'expiry_days'=>$expiry,
        'expires_at'=>date('Y-m-d H:i:s', strtotime("+$expiry days")),
        'status'=>'ACTIVE',
        'note'=>$_POST['note']??'',
        'devices'=>[]
    ];
    file_put_contents('../config.json', json_encode($config, JSON_PRETTY_PRINT));
    $success = "License generated: $key";
}
function generateLicenseKey($len){ $c='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'; return substr(str_shuffle(str_repeat($c,$len)),0,$len); }
?><!DOCTYPE html><html><head><title>Generate</title><link rel="stylesheet" href="../assets/css/style.css"></head>
<body><div class="sidebar">...</div><div class="main-content"><div class="card"><h3>Generate License</h3>
<?php if(isset($success)) echo "<div class='success'>$success</div>"; ?>
<form method="POST"><div class="form-group"><label>Package Name</label><input type="text" name="package" value="com.abuse.voidloader"></div>
<div class="form-group"><label>Expiry Days</label><select name="expiry_days"><option value="30">30 days</option><option value="60">60 days</option><option value="365">1 year</option></select></div>
<div class="form-group"><label>Note</label><input type="text" name="note"></div>
<button type="submit">Generate</button></form></div></div></body></html>