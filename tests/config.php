<?php
$config = [
    'hbase' => [
        'host' => 'localhost',
        'port' => '20550',
    ]
];

if (is_file(__DIR__ . '/config.local.php')) {
    include(__DIR__ . '/config.local.php');
}

return $config;
