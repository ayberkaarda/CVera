<?php

use Illuminate\Contracts\Console\Kernel;

require dirname(__DIR__).'/vendor/autoload.php';

$app = require dirname(__DIR__).'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

if (! getenv('DATABASE_URL') && ! getenv('DB_URL')) {
    fwrite(STDERR, "A persistent PostgreSQL connection is required for deployment.\n");
    exit(1);
}

$app['config']->set('database.default', 'pgsql');
$app['config']->set('database.connections.pgsql.sslmode', 'require');

exit($kernel->call('migrate', ['--force' => true]));
