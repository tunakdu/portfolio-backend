<?php

namespace App\Services;

use Google\Client;
use Google\Service\Gmail;
use App\Models\Message;
use Illuminate\Support\Facades\Log;

class GmailService
{
    private $client;
    private $service;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setClientId(env('GMAIL_CLIENT_ID'));
        $this->client->setClientSecret(env('GMAIL_CLIENT_SECRET'));
        $this->client->setRedirectUri('urn:ietf:wg:oauth:2.0:oob');
        $this->client->setScopes([Gmail::GMAIL_READONLY]);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');

        // Refresh token varsa set et
        if (env('GMAIL_REFRESH_TOKEN')) {
            $this->client->refreshToken(env('GMAIL_REFRESH_TOKEN'));
        }

        $this->service = new Gmail($this->client);
    }

    /**
     * Gmail'den yeni mesajları çek
     */
    public function fetchNewEmails()
    {
        try {
            // Son kontrol edilmiş mesaj ID'sini al (cache veya database'den)
            $lastMessageId = cache('gmail_last_message_id', null);
            
            // Gmail API ile mesajları listele - daha geniş query
            $query = 'to:' . env('GMAIL_USER') . ' OR to:akduhancontact@gmail.com';
            
            $optParams = [
                'q' => $query,
                'maxResults' => 20 // Daha fazla mesaj getir
            ];
            
            $results = $this->service->users_messages->listUsersMessages('me', $optParams);
            $messages = $results->getMessages();
            
            Log::info('Gmail API: Bulunan mesaj sayısı', ['count' => $messages ? count($messages) : 0]);

            if (!$messages) {
                Log::info('Gmail: Hiç mesaj bulunamadı');
                return [];
            }

            $newMessages = [];
            $newLastMessageId = $lastMessageId;

            foreach ($messages as $messageInfo) {
                $messageId = $messageInfo->getId();
                
                // Eğer bu mesaj daha önce işlendiyse skip et
                if ($lastMessageId && $messageId === $lastMessageId) {
                    break;
                }

                // İlk mesajı yeni son mesaj ID olarak kaydet
                if (!$newLastMessageId) {
                    $newLastMessageId = $messageId;
                }

                // Mesaj detaylarını al
                $message = $this->service->users_messages->get('me', $messageId);
                $processedMessage = $this->processGmailMessage($message);
                
                if ($processedMessage) {
                    $newMessages[] = $processedMessage;
                }
            }

            // Son mesaj ID'yi cache'le
            if ($newLastMessageId) {
                cache(['gmail_last_message_id' => $newLastMessageId], 3600); // 1 saat cache
            }

            Log::info('Gmail: ' . count($newMessages) . ' yeni mesaj işlendi');
            return $newMessages;

        } catch (\Exception $e) {
            Log::error('Gmail API Hatası', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return [];
        }
    }

    /**
     * Gmail mesajını işle ve database format'ına çevir
     */
    private function processGmailMessage($gmailMessage)
    {
        try {
            $headers = $gmailMessage->getPayload()->getHeaders();
            $from = null;
            $subject = null;
            $date = null;

            // Header'lardan gerekli bilgileri çıkar
            foreach ($headers as $header) {
                switch ($header->getName()) {
                    case 'From':
                        $from = $header->getValue();
                        break;
                    case 'Subject':
                        $subject = $header->getValue();
                        break;
                    case 'Date':
                        $date = $header->getValue();
                        break;
                }
            }

            // From email'ini parse et
            $email = $this->extractEmailFromHeader($from);
            $name = $this->extractNameFromHeader($from);

            // Mesaj içeriğini al
            $body = $this->getMessageBody($gmailMessage->getPayload());

            // Portfolio ile ilgili mesajları filtrele
            if (!$this->isPortfolioRelated($subject, $body, $email)) {
                return null;
            }

            // Email adres kontrolü
            if (!$email) {
                return null;
            }
            
            // Database'de zaten var mı kontrol et
            $existingMessage = Message::where('email', $email)
                ->where('message', 'LIKE', '%' . substr($body, 0, 50) . '%')
                ->first();

            if ($existingMessage) {
                return null;
            }

            // Yeni mesaj oluştur
            $message = Message::create([
                'name' => $name ?: 'Gmail Kullanıcısı',
                'email' => $email,
                'subject' => $subject ?: 'Konu yok',
                'message' => $body,
                'is_read' => false,
                'created_at' => $date ? \Carbon\Carbon::parse($date) : now(),
            ]);

            Log::info('Gmail: Yeni mesaj eklendi - ' . $email);
            return $message;

        } catch (\Exception $e) {
            Log::error('Gmail mesaj işleme hatası', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return null;
        }
    }

    /**
     * Email header'ından email adresini çıkar
     */
    private function extractEmailFromHeader($fromHeader)
    {
        if (preg_match('/<(.+?)>/', $fromHeader, $matches)) {
            return $matches[1];
        }
        
        // Eğer < > yok ise doğrudan email olabilir
        if (filter_var($fromHeader, FILTER_VALIDATE_EMAIL)) {
            return $fromHeader;
        }
        
        // Son çare: boşluk ile ayrılmış kelimelerden son email'i bul
        $words = explode(' ', $fromHeader);
        foreach (array_reverse($words) as $word) {
            if (filter_var($word, FILTER_VALIDATE_EMAIL)) {
                return $word;
            }
        }

        return null;
    }

    /**
     * Email header'ından ismi çıkar
     */
    private function extractNameFromHeader($fromHeader)
    {
        if (preg_match('/^(.+?)\s*<.+?>/', $fromHeader, $matches)) {
            return trim($matches[1], '"');
        }
        
        return null;
    }

    /**
     * Gmail mesaj body'sini al
     */
    private function getMessageBody($payload)
    {
        $body = '';

        if ($payload->getParts()) {
            foreach ($payload->getParts() as $part) {
                if ($part->getMimeType() === 'text/plain') {
                    $data = $part->getBody()->getData();
                    $body .= base64url_decode($data);
                } elseif ($part->getMimeType() === 'text/html') {
                    $data = $part->getBody()->getData();
                    $htmlBody = base64url_decode($data);
                    // HTML'den plain text'e çevir
                    $body .= strip_tags($htmlBody);
                }
            }
        } else {
            // Tek part mesaj
            $data = $payload->getBody()->getData();
            if ($data) {
                $body = base64url_decode($data);
            }
        }

        return trim($body);
    }

    /**
     * Mesajın portfolio ile ilgili olup olmadığını kontrol et
     */
    private function isPortfolioRelated($subject, $body, $email)
    {
        $portfolioKeywords = [
            'portfolio', 'portföy', 'website', 'web site', 'proje', 'project',
            'iletişim', 'contact', 'hire', 'iş', 'work', 'collaboration',
            'akduhan', 'tunahan', 'developer', 'geliştirici'
        ];

        $text = strtolower($subject . ' ' . $body . ' ' . $email);
        
        foreach ($portfolioKeywords as $keyword) {
            if (strpos($text, strtolower($keyword)) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gmail API erişim token'ını yenile
     */
    public function refreshAccessToken()
    {
        try {
            $refreshToken = env('GMAIL_REFRESH_TOKEN');
            
            if ($refreshToken) {
                $this->client->refreshToken($refreshToken);
                return true;
            }
            
            Log::warning('Gmail: Refresh token bulunamadı');
            return false;
        } catch (\Exception $e) {
            Log::error('Gmail token yenileme hatası: ' . $e->getMessage());
            return false;
        }
    }
}

// Base64 URL decode helper function
if (!function_exists('base64url_decode')) {
    function base64url_decode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}