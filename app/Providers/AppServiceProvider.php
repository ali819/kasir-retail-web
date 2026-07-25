<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\ServiceProvider;

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
        $host = request()->getHost();
        // Cek apakah domain mengandung ".id"
        if (str_contains($host, '.id')) {
            URL::forceScheme('https');
        }

        // Terapkan pengaturan SMTP dari database (menu Pengaturan Email),
        // menimpa konfigurasi .env bila sudah diisi.
        $this->loadMailSettingsFromDb();
    }

    private function loadMailSettingsFromDb(): void
    {
        try {
            if (!Schema::hasTable('pengaturan')) {
                return;
            }
            $rows = DB::table('pengaturan')
                ->whereIn('key', ['mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name'])
                ->pluck('value', 'key');

            $host = $rows['mail_host'] ?? null;
            if (!$host) {
                return; // belum dikonfigurasi -> pakai .env
            }

            $password = '';
            if (!empty($rows['mail_password'])) {
                try { $password = Crypt::decryptString($rows['mail_password']); } catch (\Exception $e) { $password = ''; }
            }

            $encryption = $rows['mail_encryption'] ?? 'tls';
            if ($encryption === 'none') { $encryption = null; }

            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.host' => $host,
                'mail.mailers.smtp.port' => (int) ($rows['mail_port'] ?? 587),
                'mail.mailers.smtp.username' => $rows['mail_username'] ?? null,
                'mail.mailers.smtp.password' => $password,
                'mail.mailers.smtp.encryption' => $encryption,
            ]);

            if (!empty($rows['mail_from_address'])) {
                config(['mail.from.address' => $rows['mail_from_address']]);
            }
            if (!empty($rows['mail_from_name'])) {
                config(['mail.from.name' => $rows['mail_from_name']]);
            }
        } catch (\Exception $e) {
            // abaikan -> fallback ke .env
        }
    }
}
