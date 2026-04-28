<?php
// No database needed – using JSON file
function loadConfig(){ return json_decode(file_get_contents(__DIR__.'/../config.json'), true); }
function saveConfig($data){ file_put_contents(__DIR__.'/../config.json', json_encode($data, JSON_PRETTY_PRINT)); }
?>