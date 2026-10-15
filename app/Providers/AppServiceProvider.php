<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Connectors\PostgresConnector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind('db.connector.pgsql', fn () => new class extends PostgresConnector
        {
            protected function getDsn(array $config)
            {
                $dsn = parent::getDsn($config);
                $endpoint = $config['neon_endpoint'] ?? null;

                return $endpoint && preg_match('/^ep-[a-z0-9-]+$/', $endpoint)
                    ? $dsn.";options='endpoint={$endpoint}'" : $dsn;
            }
        });
    }

    public function boot(): void
    {
        RateLimiter::for('analysis', fn (Request $r) => Limit::perMinute(30)->by('analysis:'.$r->ip()));
        RateLimiter::for('ai', fn (Request $r) => Limit::perMinute(10)->by('ai:'.$r->ip()));
        RateLimiter::for('account', fn (Request $r) => Limit::perMinute(5)->by('account:'.$r->ip()));
        RateLimiter::for('documents', fn (Request $r) => Limit::perMinute(60)->by('documents:'.($r->user()?->id ?? $r->ip())));
    }
}
