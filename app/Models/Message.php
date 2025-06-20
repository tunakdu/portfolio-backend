<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'name',
        'email',
        'subject',
        'thread_id',
        'message_id',
        'message',
        'source',
        'message_type',
        'message_date',
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'message_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Aynı thread'deki mesajları getir
     */
    public function threadMessages()
    {
        return $this->where('thread_id', $this->thread_id)
                   ->orderBy('message_date', 'asc')
                   ->get();
    }

    /**
     * Thread'deki son mesajı getir
     */
    public function latestInThread()
    {
        return $this->where('thread_id', $this->thread_id)
                   ->orderBy('message_date', 'desc')
                   ->first();
    }
}
