<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'item_id',
        'from_user_id',
        'to_user_id',
        'rating',
        'comment',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * 評価が属する商品
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * 評価をした人
     */
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    /**
     * 評価を受けた人
     */
    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    /**
     * 特定ユーザーの評価平均を取得（四捨五入）
     *
     * @param int $userId
     * @return int|null
     */
    public static function getAverageRating($userId)
    {
        $average = self::where('to_user_id', $userId)->avg('rating');

        // 評価がない場合はnullを返す
        if ($average === null) {
            return null;
        }

        // 四捨五入して返す
        return round($average);
    }

    /**
     * 特定ユーザーの評価数を取得
     *
     * @param int $userId
     * @return int
     */
    public static function getRatingCount($userId)
    {
        return self::where('to_user_id', $userId)->count();
    }

    /**
     * 既に評価済みかチェック
     *
     * @param int $itemId
     * @param int $fromUserId
     * @return bool
     */
    public static function hasRated($itemId, $fromUserId)
    {
        return self::where('item_id', $itemId)
            ->where('from_user_id', $fromUserId)
            ->exists();
    }
}
