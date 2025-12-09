<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ItemCondition;
use App\Enums\ItemStatus;

/**
 * App\Models\Item
 *
 * @property int $id
 * @property string $name
 * @property string|null $brand_name
 * @property int $price
 * @property string|null $color
 * @property string $detail
 * @property ItemCondition $condition
 * @property ItemStatus $status
 * @property int $listed_by
 * @property string $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Favorite> $favorites
 * @property-read int|null $favorites_count
 * @property-read \App\Models\User $lister
 * @property-read \App\Models\PurchaseHistory|null $purchase
 * @method static \Illuminate\Database\Eloquent\Builder|Item newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Item newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Item query()
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereBrandName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereListedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand_name',
        'price',
        'detail',
        'condition',
        'status',
        'listed_by',
        'image'
    ];

    protected $casts = [
        'condition' => ItemCondition::class,
        'status' => ItemStatus::class,
    ];

    // ============================================
    // 既存のリレーション
    // ============================================

    public function categories()
    {
        return $this->belongsToMany(
            Category::class,
            'item_categories',
            'item_id',
            'category_id'
        );
    }

    public function lister()
    {
        return $this->belongsTo(User::class, 'listed_by');
    }

    /**
     * 商品の出品者を取得（listerのエイリアス）
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'listed_by');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'item_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'item_id');
    }

    public function favoritedUsers()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    public function purchase()
    {
        return $this->hasOne(PurchaseHistory::class, 'item_id');
    }

    // ============================================
    // 取引機能用の新しいリレーション
    // ============================================

    /**
     * 商品の取引メッセージ
     */
    public function transactionMessages()
    {
        return $this->hasMany(TransactionMessage::class)->orderBy('created_at', 'desc');
    }

    /**
     * 商品の最新メッセージ
     */
    public function latestMessage()
    {
        return $this->hasOne(TransactionMessage::class)->latestOfMany();
    }

    /**
     * 商品の評価
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * 商品の購入者を取得
     */
    public function buyer()
    {
        return $this->hasOneThrough(
            User::class,
            PurchaseHistory::class,
            'item_id',    // purchase_historiesテーブルの外部キー
            'id',         // usersテーブルの主キー
            'id',         // itemsテーブルの主キー
            'user_id'     // purchase_historiesテーブルのローカルキー
        );
    }

    // ============================================
    // ヘルパーメソッド
    // ============================================

    /**
     * 特定ユーザーにとっての相手ユーザーを取得
     * （出品者から見たら購入者、購入者から見たら出品者）
     *
     * @param int $currentUserId
     * @return User|null
     */
    public function getOtherUser($currentUserId)
    {
        if ($this->listed_by == $currentUserId) {
            // 現在のユーザーが出品者の場合、購入者を返す
            return $this->buyer;
        } else {
            // 現在のユーザーが購入者の場合、出品者を返す
            return $this->seller;
        }
    }

    /**
     * 未読メッセージ数を取得
     *
     * @param int $userId
     * @return int
     */
    public function getUnreadCount($userId)
    {
        return $this->transactionMessages()
            ->where('user_id', '!=', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * 取引中かどうか
     *
     * @return bool
     */
    public function isInTransaction()
    {
        return $this->status->value === ItemStatus::IN_TRANSACTION->value;
    }

    /**
     * 売却済みかどうか
     *
     * @return bool
     */
    public function isSold()
    {
        return $this->status === ItemStatus::SOLD;
    }

    /**
     * 販売中かどうか
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->status === ItemStatus::AVAILABLE;
    }
}
