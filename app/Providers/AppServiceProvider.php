<?php

namespace App\Providers;

use App\Listeners\AutoReplyOnKeyword;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Kstmostofa\LaravelWhatsApp\Events\MessageReceived as CloudMessageReceived;
use Kstmostofa\LaravelWhatsApp\Events\Web\MessageReceived as WebMessageReceived;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(WebMessageReceived::class, [AutoReplyOnKeyword::class, 'handleWeb']);
        Event::listen(CloudMessageReceived::class, [AutoReplyOnKeyword::class, 'handleCloud']);
    }
}
