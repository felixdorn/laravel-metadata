<?php


namespace Felix\Metadata;


use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

final class MetaServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        /** @phpstan-ignore */
        Blueprint::macro('metadata', function () {
            $this->json('metadata')->nullable();
        });
    }
}
