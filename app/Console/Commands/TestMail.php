<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMail extends Command
{
    protected $signature = 'mail:test {email}';
    protected $description = 'E-posta servisi test komutu';

    public function handle()
    {
        $email = $this->argument('email');
        
        try {
            $this->info('E-posta gönderiliyor...');
            
            Mail::raw('Bu bir test e-postasıdır. E-posta servisi başarıyla çalışıyor!', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Pazaryeri - E-posta Test');
            });
            
            $this->info('✅ E-posta başarıyla gönderildi: ' . $email);
            
        } catch (\Exception $e) {
            $this->error('❌ E-posta gönderim hatası: ' . $e->getMessage());
            
            // Detaylı hata bilgisi
            $this->warn('E-posta konfigürasyonunuzu kontrol edin:');
            $this->line('MAIL_MAILER: ' . config('mail.default'));
            $this->line('MAIL_HOST: ' . config('mail.mailers.smtp.host'));
            $this->line('MAIL_PORT: ' . config('mail.mailers.smtp.port'));
        }
    }
}
