<?php

namespace App\Services;

use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ImapService
{
    protected $connection;
    protected $host;
    protected $port;
    protected $username;
    protected $password;
    protected $encryption;

    public function __construct()
    {
        $this->host = env('IMAP_HOST');
        $this->port = env('IMAP_PORT', 993);
        $this->username = env('IMAP_USERNAME');
        $this->password = env('IMAP_PASSWORD');
        $this->encryption = env('IMAP_ENCRYPTION', 'ssl');
    }

    /**
     * IMAP bağlantısı kur
     */
    protected function connect()
    {
        $mailbox = '{' . $this->host . ':' . $this->port . '/imap';
        
        if ($this->encryption === 'ssl') {
            $mailbox .= '/ssl';
        }
        
        // SSL sertifika doğrulamasını kapat
        $mailbox .= '/novalidate-cert';
        
        $mailbox .= '}INBOX';
        
        Log::info('IMAP bağlantı denemesi: ' . $mailbox);
        Log::info('IMAP kullanıcı: ' . $this->username);
        
        $this->connection = imap_open($mailbox, $this->username, $this->password);
        
        if (!$this->connection) {
            $errors = imap_errors();
            $lastError = imap_last_error();
            Log::error('IMAP hatası: ' . $lastError);
            throw new \Exception('IMAP bağlantısı kurulamadı: ' . $lastError);
        }
        
        return $this->connection;
    }

    /**
     * IMAP bağlantısını kapat
     */
    protected function disconnect()
    {
        if ($this->connection) {
            imap_close($this->connection);
            $this->connection = null;
        }
    }

    /**
     * Yeni e-postaları çek ve veritabanına kaydet
     */
    public function fetchNewEmails()
    {
        try {
            $this->connect();
            
            // Son 7 gündeki e-postaları ara
            $since = date('d-M-Y', strtotime('-7 days'));
            $emails = imap_search($this->connection, 'SINCE "' . $since . '"', SE_UID);
            
            $newMessages = [];
            
            if ($emails) {
                foreach ($emails as $uid) {
                    $msgno = imap_msgno($this->connection, $uid);
                    $header = imap_headerinfo($this->connection, $msgno);
                    
                    // E-posta bilgilerini al
                    $subject = isset($header->subject) ? imap_utf8($header->subject) : 'Konu Yok';
                    $from = isset($header->from[0]) ? $header->from[0] : null;
                    $fromEmail = $from ? $from->mailbox . '@' . $from->host : 'bilinmiyor@example.com';
                    $fromName = isset($from->personal) ? imap_utf8($from->personal) : $fromEmail;
                    
                    // Benzersiz Message-ID al
                    $messageId = isset($header->message_id) ? $header->message_id : null;
                    
                    // Thread ID oluştur (subject bazlı)
                    $threadId = md5(preg_replace('/^(Re:|RE:|Fwd:|FWD:)\s*/i', '', trim($subject)));
                    
                    // Message type belirle (tunahan@akduhan.com'dan geliyorsa outgoing)
                    $messageType = ($fromEmail === 'tunahan@akduhan.com') ? 'outgoing' : 'incoming';
                    
                    // Veritabanında zaten var mı kontrol et (Message-ID ile)
                    $existingMessage = null;
                    if ($messageId) {
                        $existingMessage = Message::where('message_id', $messageId)->first();
                    }
                    
                    // Message-ID yoksa eski yöntemle kontrol et
                    if (!$existingMessage && !$messageId) {
                        $existingMessage = Message::where('email', $fromEmail)
                            ->where('subject', $subject)
                            ->where('created_at', '>=', Carbon::parse($header->date)->subMinutes(5))
                            ->where('created_at', '<=', Carbon::parse($header->date)->addMinutes(5))
                            ->first();
                    }
                    
                    if (!$existingMessage) {
                        // Email yapısını kontrol et
                        $structure = imap_fetchstructure($this->connection, $msgno);
                        $body = '';
                        
                        if ($structure->type == 1) { // Multipart
                            // HTML veya text parçası ara
                            for ($i = 1; $i <= $structure->parts[0]->parts ?? 1; $i++) {
                                $partBody = imap_fetchbody($this->connection, $msgno, $i);
                                if (!empty($partBody)) {
                                    $body = $partBody;
                                    break;
                                }
                            }
                            if (empty($body)) {
                                $body = imap_fetchbody($this->connection, $msgno, '1.1');
                            }
                        } else {
                            $body = imap_fetchbody($this->connection, $msgno, 1);
                        }
                        
                        // Encoding kontrolü ve decode
                        if (isset($structure->encoding)) {
                            switch ($structure->encoding) {
                                case 3: // Base64
                                    $body = base64_decode($body);
                                    break;
                                case 4: // Quoted-printable
                                    $body = quoted_printable_decode($body);
                                    break;
                            }
                        }
                        
                        // HTML taglerini temizle
                        $body = strip_tags($body);
                        
                        // Reply kısımlarını temizle
                        $lines = explode("\n", $body);
                        $cleanLines = [];
                        
                        foreach ($lines as $line) {
                            $line = trim($line);
                            
                            // > ile başlayan satırları atla
                            if (strpos($line, '>') === 0) {
                                continue;
                            }
                            
                            // "yazd" veya "wrote" içeren satırları atla
                            if (strpos($line, 'yazd') !== false || strpos($line, 'wrote') !== false) {
                                break;
                            }
                            
                            // "On " ile başlayan reply formatını atla
                            if (strpos($line, 'On ') === 0 && strpos($line, 'wrote:') !== false) {
                                break;
                            }
                            
                            $cleanLines[] = $line;
                        }
                        
                        $body = implode(' ', $cleanLines);
                        
                        // Fazla boşlukları temizle
                        $body = preg_replace('/\r\n|\r|\n/', ' ', $body);
                        $body = preg_replace('/\s+/', ' ', $body);
                        
                        // UTF-8 encoding garantisi
                        if (!mb_check_encoding($body, 'UTF-8')) {
                            $body = mb_convert_encoding($body, 'UTF-8', 'auto');
                        }
                        
                        $newMessage = Message::create([
                            'name' => $fromName,
                            'email' => $fromEmail,
                            'subject' => $subject,
                            'thread_id' => $threadId,
                            'message_id' => $messageId,
                            'message' => trim($body),
                            'source' => 'email',
                            'message_type' => $messageType,
                            'message_date' => Carbon::parse($header->date),
                            'is_read' => false,
                            'created_at' => Carbon::parse($header->date)
                        ]);
                        
                        $newMessages[] = $newMessage;
                        
                        Log::info('Yeni e-posta kaydedildi: ' . $subject);
                    }
                }
            }
            
            $this->disconnect();
            
            return $newMessages;
            
        } catch (\Exception $e) {
            $this->disconnect();
            Log::error('IMAP e-posta çekme hatası: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * IMAP bağlantısını test et
     */
    public function testConnection()
    {
        // IMAP extension kontrol et
        if (!extension_loaded('imap')) {
            return [
                'success' => false,
                'message' => 'PHP IMAP extension yüklü değil. Lütfen php-imap extension\'ını yükleyin.'
            ];
        }
        
        try {
            $this->connect();
            
            // Mailbox bilgilerini al
            $check = imap_check($this->connection);
            $info = [
                'success' => true,
                'message' => 'IMAP bağlantısı başarılı',
                'mailbox' => $check->Mailbox ?? '',
                'messages' => $check->Nmsgs ?? 0,
                'recent' => $check->Recent ?? 0
            ];
            
            $this->disconnect();
            
            return $info;
            
        } catch (\Exception $e) {
            $this->disconnect();
            
            return [
                'success' => false,
                'message' => 'IMAP bağlantı hatası: ' . $e->getMessage()
            ];
        }
    }
}