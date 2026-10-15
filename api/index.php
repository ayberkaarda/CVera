<?php

// The front controller is mounted at the site root, including /api routes.
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

// Writable runtime files live outside Vercel's read-only deployment bundle.
$runtimePath = sys_get_temp_dir().'/cvera';
foreach (['views', 'cache', 'sessions'] as $directory) {
    $path = $runtimePath.'/'.$directory;
    if (! is_dir($path)) {
        mkdir($path, 0700, true);
    }
}

$_ENV['VIEW_COMPILED_PATH'] = $runtimePath.'/views';
$_SERVER['VIEW_COMPILED_PATH'] = $runtimePath.'/views';
putenv('VIEW_COMPILED_PATH='.$runtimePath.'/views');

require __DIR__.'/../public/index.php';
