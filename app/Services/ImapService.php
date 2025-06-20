<?php

namespace App\Services;

use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
        $this->host = config('mail.imap.host') ?? env('IMAP_HOST');
        $this->port = config('mail.imap.port') ?? env('IMAP_PORT', 993);
        $this->username = config('mail.imap.username') ?? env('IMAP_USERNAME');
        $this->password = config('mail.imap.password') ?? env('IMAP_PASSWORD');
        $this->encryption = config('mail.imap.encryption') ?? env('IMAP_ENCRYPTION', 'ssl');
        
        // Debug için null değerleri kontrol et
        if (empty($this->username) || empty($this->password)) {
            Log::warning('IMAP credentials boş', [
                'host' => $this->host,
                'username' => $this->username ? 'SET' : 'NULL',
                'password' => $this->password ? 'SET' : 'NULL'
            ]);
        }
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
            $lastError = imap_last_error();
            $errors = imap_errors();
            Log::error('IMAP hatası: ' . $lastError, ['errors' => $errors]);
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
     * Benzersiz thread ID oluştur
     */
    protected function generateThreadId($subject, $fromEmail)
    {
        // Subject'i temizle (Re:, Fwd: etc.)
        $cleanSubject = preg_replace('/^(Re:|RE:|Fwd:|FWD:|AW:|Aw:)\s*/i', '', trim($subject));
        $cleanSubject = trim($cleanSubject);

        // Boş subject kontrolü
        if (empty($cleanSubject)) {
            $cleanSubject = 'no-subject';
        }

        // Email domainini al
        $emailParts = explode('@', $fromEmail);
        $emailDomain = isset($emailParts[1]) ? $emailParts[1] : 'unknown';

        // Benzersiz thread ID oluştur: subject + email domain kombinasyonu
        $threadId = md5(strtolower($cleanSubject) . '|' . strtolower($emailDomain));

        Log::debug("Thread ID oluşturuldu - Subject: '{$cleanSubject}', Email: {$fromEmail}, Thread ID: {$threadId}");

        return $threadId;
    }

    /**
     * Benzersiz message ID oluştur
     */
    protected function generateMessageId($header, $fromEmail, $subject)
    {
        // Önce email header'dan message-id'yi al
        if (isset($header->message_id) && !empty(trim($header->message_id))) {
            return trim($header->message_id);
        }

        // Message-ID yoksa benzersiz bir tane oluştur
        $timestamp = isset($header->udate) ? $header->udate : time();
        $uniqueId = md5($fromEmail . $subject . $timestamp . microtime(true));

        return '<' . $uniqueId . '@' . parse_url($this->host, PHP_URL_HOST) . '>';
    }

    /**
     * E-posta duplikasyon kontrolü
     */
    protected function isDuplicateMessage($messageId, $fromEmail, $subject, $messageDate)
    {
        // 1. Message-ID ile kontrol (en güvenilir)
        if ($messageId) {
            $existing = Message::where('message_id', $messageId)->first();
            if ($existing) {
                Log::info("Duplikasyon tespit edildi (Message-ID): {$messageId} - {$subject}");
                return true;
            }
        }

        // 2. Email + Subject + Date kombinasyonu ile kontrol
        $dateWindow = 5; // 5 dakika tolerans
        $existing = Message::where('email', $fromEmail)
            ->where('subject', $subject)
            ->where('message_date', '>=', $messageDate->copy()->subMinutes($dateWindow))
            ->where('message_date', '<=', $messageDate->copy()->addMinutes($dateWindow))
            ->first();

        if ($existing) {
            Log::info("Duplikasyon tespit edildi (Email+Subject+Date): {$fromEmail} - {$subject}");
            return true;
        }

        // 3. Subject hash ile kontrol (case-insensitive, Re: vs olmadan)
        $cleanSubject = preg_replace('/^(Re:|RE:|Fwd:|FWD:|AW:|Aw:)\s*/i', '', trim($subject));
        $subjectHash = md5(strtolower($cleanSubject));

        $existing = Message::where('email', $fromEmail)
            ->whereRaw('MD5(LOWER(TRIM(REGEXP_REPLACE(subject, "^(Re:|RE:|Fwd:|FWD:|AW:|Aw:)[[:space:]]*", "")))) = ?', [$subjectHash])
            ->where('message_date', '>=', $messageDate->copy()->subHours(12))
            ->first();

        if ($existing) {
            Log::info("Duplikasyon tespit edildi (Subject Hash): {$fromEmail} - {$cleanSubject}");
            return true;
        }

        return false;
    }

    /**
     * Email body'sini temizle ve düzenle
     */
    protected function cleanEmailBody($body, $structure)
    {
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

            // Boş satırları atla
            if (empty($line)) {
                continue;
            }

            // > ile başlayan satırları atla (quoted text)
            if (strpos($line, '>') === 0) {
                continue;
            }

            // Reply indicators
            if (preg_match('/^(On .* wrote:|.*yazd.*:|.*schrieb.*:)/i', $line)) {
                break;
            }

            // Email signatures
            if (preg_match('/^(--|__)/i', $line)) {
                break;
            }

            $cleanLines[] = $line;
        }

        $body = implode(' ', $cleanLines);

        // Fazla boşlukları temizle
        $body = preg_replace('/\r\n|\r|\n/', ' ', $body);
        $body = preg_replace('/\s+/', ' ', $body);
        $body = trim($body);

        // UTF-8 encoding garantisi
        if (!mb_check_encoding($body, 'UTF-8')) {
            $body = mb_convert_encoding($body, 'UTF-8', 'auto');
        }

        // Minimum uzunluk kontrolü
        if (strlen($body) < 10) {
            $body = '[Email içeriği okunamadı veya çok kısa]';
        }

        return $body;
    }

    /**
     * Email yapısından body'yi al
     */
    protected function getEmailBody($msgno, $structure)
    {
        $body = '';

        if ($structure->type == 1) { // Multipart
            // HTML veya text parçası ara
            if (isset($structure->parts) && is_array($structure->parts)) {
                foreach ($structure->parts as $partIndex => $part) {
                    $partNumber = $partIndex + 1;

                    // Text/plain önceliği
                    if ($part->subtype === 'PLAIN') {
                        $body = imap_fetchbody($this->connection, $msgno, $partNumber);
                        break;
                    }

                    // HTML alternatifi
                    if (empty($body) && $part->subtype === 'HTML') {
                        $body = imap_fetchbody($this->connection, $msgno, $partNumber);
                    }
                }
            }

            // Hiçbir part bulunamazsa ilk parçayı al
            if (empty($body)) {
                $body = imap_fetchbody($this->connection, $msgno, '1.1');
            }
        } else {
            // Single part message
            $body = imap_fetchbody($this->connection, $msgno, 1);
        }

        return $this->cleanEmailBody($body, $structure);
    }

    /**
     * Yeni e-postaları çek ve veritabanına kaydet
     */
    public function fetchNewEmails()
    {
        try {
            $this->connect();

            // Son 3 gündeki e-postaları ara (duplikasyon riskini azaltmak için daha kısa süre)
            $since = date('d-M-Y', strtotime('-3 days'));
            $emails = imap_search($this->connection, 'SINCE "' . $since . '"', SE_UID);

            $newMessages = [];
            $processedCount = 0;
            $duplicateCount = 0;

            if ($emails) {
                Log::info('IMAP: ' . count($emails) . ' email bulundu, işleniyor...');

                foreach ($emails as $uid) {
                    $processedCount++;

                    try {
                        $msgno = imap_msgno($this->connection, $uid);
                        $header = imap_headerinfo($this->connection, $msgno);

                        if (!$header) {
                            Log::warning("Email header alınamadı: UID {$uid}");
                            continue;
                        }

                        // E-posta bilgilerini al
                        $subject = isset($header->subject) ? imap_utf8($header->subject) : 'Konu Yok';
                        $from = isset($header->from[0]) ? $header->from[0] : null;

                        if (!$from) {
                            Log::warning("Email from bilgisi alınamadı: UID {$uid}");
                            continue;
                        }

                        $fromEmail = $from->mailbox . '@' . $from->host;
                        $fromName = isset($from->personal) ? imap_utf8($from->personal) : $fromEmail;

                        // Date parsing
                        $messageDate = isset($header->date) ? Carbon::parse($header->date) : Carbon::now();

                        // Benzersiz Message-ID oluştur/al
                        $messageId = $this->generateMessageId($header, $fromEmail, $subject);

                        // Duplikasyon kontrolü
                        if ($this->isDuplicateMessage($messageId, $fromEmail, $subject, $messageDate)) {
                            $duplicateCount++;
                            continue;
                        }

                        // Thread ID oluştur
                        $threadId = $this->generateThreadId($subject, $fromEmail);

                        // Message type belirle
                        $messageType = ($fromEmail === 'tunahan@akduhan.com') ? 'outgoing' : 'incoming';

                        // Email body'sini al
                        $structure = imap_fetchstructure($this->connection, $msgno);
                        $body = $this->getEmailBody($msgno, $structure);

                        // Database transaction ile kaydet
                        DB::beginTransaction();

                        try {
                            $newMessage = Message::create([
                                'name' => $fromName,
                                'email' => $fromEmail,
                                'subject' => $subject,
                                'thread_id' => $threadId,
                                'message_id' => $messageId,
                                'message' => $body,
                                'source' => 'email',
                                'message_type' => $messageType,
                                'message_date' => $messageDate,
                                'is_read' => false,
                                'created_at' => $messageDate
                            ]);

                            DB::commit();

                            $newMessages[] = $newMessage;
                            Log::info("Yeni e-posta kaydedildi: {$fromEmail} - {$subject}");

                        } catch (\Illuminate\Database\QueryException $e) {
                            DB::rollBack();

                            // Unique constraint violation kontrolü
                            if (strpos($e->getMessage(), 'Duplicate entry') !== false ||
                                strpos($e->getMessage(), 'UNIQUE constraint') !== false) {
                                Log::info("Database duplikasyon tespit edildi: {$fromEmail} - {$subject}");
                                $duplicateCount++;
                                continue;
                            } else {
                                throw $e;
                            }
                        }

                    } catch (\Exception $e) {
                        Log::error("Email işleme hatası (UID: {$uid}): " . $e->getMessage());
                        continue;
                    }
                }

                Log::info("IMAP Sync tamamlandı - İşlenen: {$processedCount}, Yeni: " . count($newMessages) . ", Duplikasyon: {$duplicateCount}");
            } else {
                Log::info('IMAP: Yeni email bulunamadı.');
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
                'recent' => $check->Recent ?? 0,
                'host' => $this->host,
                'username' => $this->username
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

    /**
     * Belirli bir email'i işle (test amaçlı)
     */
    public function processSpecificEmail($uid)
    {
        try {
            $this->connect();

            $msgno = imap_msgno($this->connection, $uid);
            $header = imap_headerinfo($this->connection, $msgno);

            if (!$header) {
                throw new \Exception("Email header alınamadı: UID {$uid}");
            }

            $subject = isset($header->subject) ? imap_utf8($header->subject) : 'Konu Yok';
            $from = isset($header->from[0]) ? $header->from[0] : null;
            $fromEmail = $from ? $from->mailbox . '@' . $from->host : 'unknown@example.com';

            $messageId = $this->generateMessageId($header, $fromEmail, $subject);
            $threadId = $this->generateThreadId($subject, $fromEmail);

            $this->disconnect();

            return [
                'uid' => $uid,
                'subject' => $subject,
                'from' => $fromEmail,
                'message_id' => $messageId,
                'thread_id' => $threadId,
                'date' => $header->date ?? 'Unknown'
            ];

        } catch (\Exception $e) {
            $this->disconnect();
            throw $e;
        }
    }
}