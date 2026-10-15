<?php

$root = dirname(__DIR__);
chdir($root);
if (! file_exists('.env')) {
    copy('.env.example', '.env');
    echo ".env created.\n";
}
if (! file_exists('database/database.sqlite')) {
    touch('database/database.sqlite');
}
$env = file_get_contents('.env');
$commands = [];
if (! preg_match('/^APP_KEY=.+$/m', $env)) {
    $commands[] = ['key:generate', '--ansi'];
}
$commands[] = ['migrate', '--force', '--ansi'];
foreach ($commands as $args) {
    $command = escapeshellarg(PHP_BINARY).' artisan '.implode(' ', array_map('escapeshellarg', $args));
    passthru($command, $code);
    if ($code !== 0) {
        exit($code);
    }
}
echo "Ready. Run: php artisan serve\n";
