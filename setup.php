<?php
$config = [
    'users' => [
        'admin' => ['password' => password_hash('admin123', PASSWORD_DEFAULT), 'role' => 'admin', 'created_at' => date('Y-m-d H:i:s')],
        'zenin' => ['password' => password_hash('zenin123', PASSWORD_DEFAULT), 'role' => 'admin', 'created_at' => date('Y-m-d H:i:s')]
    ],
    'licenses' => [],
    'settings' => ['panel_name' => 'ZCore', 'version' => '2.0']
];
file_put_contents('config.json', json_encode($config, JSON_PRETTY_PRINT));
echo "Setup complete!<br>Login: admin / admin123<br><strong>Delete setup.php now!</strong>";
?>