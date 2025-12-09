<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionMessage extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'item_id',
        'user_id',
        'content',
        'image',
        'is_read',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * 取引メッセージが属する商品
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * 取引メッセージの送信者
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 特定商品の未読メッセージ数を取得
     *
     * @param int $itemId
     * @param int $userId
     * @return int
     */
    public static function getUnreadCount($itemId, $userId)
    {
        return self::where('item_id', $itemId)
            ->where('user_id', '!=', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * メッセージを既読にする
     *
     * @param int $itemId
     * @param int $userId
     * @return void
     */
    public static function markAsRead($itemId, $userId)
    {
        self::where('item_id', $itemId)
            ->where('user_id', '!=', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    /**
     * 最新のメッセージを取得
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
