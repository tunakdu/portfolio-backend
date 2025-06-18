<?php

namespace App\Services;

use App\Models\Message;
use Illuminate\Support\Facades\Log;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Client;

class ImapService
{
    private $client;

    public function __construct()
    {
        $cm = new ClientManager();
        
        // cPanel IMAP bağlantı ayarları
        $this->client = $cm->make([
            'host' => env('IMAP_HOST'),
            'port' => env('IMAP_PORT', 993),
            'encryption' => env('IMAP_ENCRYPTION', 'ssl'),
            'validate_cert' => true,
            'username' => env('IMAP_USERNAME'),
            'password' => env('IMAP_PASSWORD'),
            'protocol' => 'imap'
        ]);
    }

    /**
     * Yeni e-postaları getir ve veritabanına kaydet
     */
    public function fetchNewEmails()
    {
        try {
            // IMAP sunucusuna bağlan
            $this->client->connect();
            
            Log::info('IMAP sunucusuna başarıyla bağlanıldı');
            
            // INBOX klasörünü al
            $folder = $this->client->getFolder('INBOX');
            
            // Son 7 gün içindeki okunmamış mesajları getir
            $messages = $folder->query()
                ->unseen()
                ->since(now()->subDays(7))
                ->get();
            
            $newMessages = [];
            
            foreach ($messages as $message) {
                $messageData = $this->processMessage($message);
                
                if ($messageData) {
                    // Aynı mesajın daha önce kaydedilip kaydedilmediğini kontrol et
                    $existingMessage = Message::where('email', $messageData['email'])
                        ->where('subject', $messageData['subject'])
                        ->where('created_at', '>', now()->subHours(1))
                        ->first();
                    
                    if (!$existingMessage) {
                        $savedMessage = Message::create($messageData);
                        $newMessages[] = $savedMessage;
                        
                        Log::info('Yeni e-posta kaydedildi: ' . $messageData['subject']);
                    }
                }
            }
            
            $this->client->disconnect();
            
            Log::info(count($newMessages) . ' yeni e-posta işlendi');
            
            return $newMessages;
            
        } catch (\Exception $e) {
            Log::error('IMAP işleme hatası: ' . $e->getMessage());
            
            try {
                if ($this->client) {
                    $this->client->disconnect();
                }
            } catch (\Exception $disconnectError) {
                // Disconnection hatası önemli değil
            }
            
            return [];
        }
    }

    /**
     * Tek bir mesajı işle
     */
    private function processMessage($message)
    {
        try {
            // Gönderen bilgilerini al
            $from = $message->getFrom()[0] ?? null;
            $fromEmail = $from ? $from->mail : 'bilinmeyen@email.com';
            $fromName = $from ? ($from->personal ?: $fromEmail) : 'Bilinmeyen';
            
            // Konu bilgisini al
            $subject = $message->getSubject()[0] ?? 'Konu yok';
            
            // Mesaj içeriğini al (text veya html)
            $textBody = $message->getTextBody();
            $htmlBody = $message->getHTMLBody();
            
            // Önce text body'yi dene, yoksa html body'yi kullan
            $messageBody = '';
            if (!empty($textBody)) {
                $messageBody = $textBody;
            } elseif (!empty($htmlBody)) {
                // HTML'den basit text'e çevir
                $messageBody = strip_tags($htmlBody);
            } else {
                $messageBody = 'Mesaj içeriği okunamadı';
            }
            
            // Tarih bilgisini al
            $date = $message->getDate();
            $createdAt = $date ? $date->format('Y-m-d H:i:s') : now();
            
            return [
                'name' => $fromName,
                'email' => $fromEmail,
                'subject' => $subject,
                'message' => trim($messageBody),
                'is_read' => false,
                'created_at' => $createdAt,
                'updated_at' => now()
            ];
            
        } catch (\Exception $e) {
            Log::error('Mesaj işleme hatası: ' . $e->getMessage());
            return null;
        }
    }
}