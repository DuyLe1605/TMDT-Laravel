<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

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
        // Tùy chỉnh giao diện Email xác thực tài khoản sang template tiếng Việt cao cấp
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('[' . config('app.name') . '] Xác thực tài khoản của bạn')
                ->view('emails.verify-email', [
                    'url' => $url,
                    'user' => $notifiable,
                ]);
        });
    }
}
