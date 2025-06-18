<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessage;
use App\Services\ImapService;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $messages = Message::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'data' => $messages
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $message = Message::create([
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
            'subject' => 'Web Sitesi İletişim Formu',
        ]);

        // Mail gönder
        try {
            Mail::to(env('MAIL_FROM_ADDRESS'))->send(new ContactMessage($message));
        } catch (\Exception $e) {
            \Log::error('Mail gönderme hatası: ' . $e->getMessage());
            // Mail hata verse bile mesaj kaydedilsin
        }

        return response()->json([
            'message' => 'Mesajınız başarıyla gönderildi!',
            'data' => $message
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $message = Message::findOrFail($id);
        
        // Mesajı okundu olarak işaretle
        $message->update(['is_read' => true]);
        
        return response()->json([
            'data' => $message
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $message = Message::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|in:UNREAD,read,READ,REPLIED',
            'is_read' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Status field'ı güncellenirse is_read field'ını da güncelle
        if ($request->has('status')) {
            $status = $request->status;
            $message->update([
                'is_read' => in_array($status, ['read', 'READ', 'REPLIED'])
            ]);
        }

        // is_read field'ı doğrudan güncellenirse
        if ($request->has('is_read')) {
            $message->update(['is_read' => $request->is_read]);
        }

        return response()->json([
            'message' => 'Mesaj durumu güncellendi.',
            'data' => $message->fresh()
        ]);
    }

    /**
     * E-posta kutusundan yeni mesajları manuel senkronize et
     */
    public function syncEmails()
    {
        try {
            $imapService = new ImapService();
            
            $newMessages = $imapService->fetchNewEmails();
            
            return response()->json([
                'message' => count($newMessages) . ' yeni mesaj senkronize edildi.',
                'new_messages_count' => count($newMessages),
                'data' => $newMessages
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Manuel e-posta senkronizasyon hatası: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'E-posta senkronizasyonu sırasında hata oluştu.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $message = Message::findOrFail($id);
        $message->delete();
        
        return response()->json([
            'message' => 'Mesaj başarıyla silindi.'
        ]);
    }
}
