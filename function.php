<?php
function generateLicenseKey($len=12){
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    return substr(str_shuffle(str_repeat($chars, $len)), 0, $len);
}
function logActivity($action,$user,$details=''){
    $log = date('Y-m-d H:i:s')." | $action | $user | $details\n";
    file_put_contents(__DIR__.'/../logs/activity.log', $log, FILE_APPEND);
}
?>