<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
$config = json_decode(file_get_contents('config.json'), true);
$key = $_GET['license_key'] ?? $_POST['license_key'] ?? null;
if(!$key) { echo json_encode(['status'=>'error','message'=>'License key required']); exit; }
foreach($config['licenses'] as &$lic) {
    if($lic['license_key'] === $key) {
        if($lic['status'] !== 'ACTIVE') { echo json_encode(['status'=>'revoked','message'=>'Revoked']); exit; }
        if(strtotime($lic['expires_at']) < time()) { echo json_encode(['status'=>'expired','message'=>'Expired']); exit; }
        $lic['last_used'] = date('Y-m-d H:i:s');
        file_put_contents('config.json', json_encode($config, JSON_PRETTY_PRINT));
        echo json_encode(['status'=>'active','message'=>'OK','license_key'=>$lic['license_key'],'expires_at'=>$lic['expires_at']]);
        exit;
    }
}
echo json_encode(['status'=>'invalid','message'=>'Invalid key']);
?>