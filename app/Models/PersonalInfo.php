<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalInfo extends Model
{
    protected $table = 'personal_info';
    
    protected $fillable = [
        'key',
        'value',
        'type',
        'category'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Belirli bir key'e göre değer al
     */
    public static function getValue($key, $default = null)
    {
        $info = static::where('key', $key)->first();
        return $info ? $info->value : $default;
    }

    /**
     * Belirli bir key'e değer set et
     */
    public static function setValue($key, $value, $type = 'text', $category = null)
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'category' => $category
            ]
        );
    }

    /**
     * Kategori bazında bilgileri getir
     */
    public static function getByCategory($category)
    {
        return static::where('category', $category)->get()->pluck('value', 'key');
    }

    /**
     * Tüm bilgileri key-value pair olarak getir
     */
    public static function getAllKeyValue()
    {
        return static::all()->pluck('value', 'key');
    }
}
