<?php
header('Content-Type: application/json');
$config = json_decode(file_get_contents('../../config.json'), true);
$key = $_GET['key'] ?? $_POST['key'] ?? null;
if(!$key){ echo json_encode(['valid'=>false,'message'=>'Missing key']); exit; }
foreach($config['licenses'] as &$l){
    if($l['license_key'] === $key){
        if($l['status']!='ACTIVE'){ echo json_encode(['valid'=>false,'message'=>'Revoked']); exit; }
        if(strtotime($l['expires_at'])<time()){ echo json_encode(['valid'=>false,'message'=>'Expired']); exit; }
        echo json_encode(['valid'=>true,'message'=>'OK','expires'=>$l['expires_at']]); exit;
    }
}
echo json_encode(['valid'=>false,'message'=>'Invalid key']);
?>