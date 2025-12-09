<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\ItemStatus;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $two_factor_confirmed_at
 * @property string|null $image
 * @property bool $profile_completed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Favorite> $favorites
 * @property-read int|null $favorites_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Item> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PurchaseHistory> $purchase
 * @property-read int|null $purchase_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \App\Models\UserAddress|null $user_addresses
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProfileCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'image',
        'profile_completed',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'profile_completed' => 'boolean',
    ];

    // ============================================
    // 既存のリレーション
    // ============================================

    /**
     * ユーザーが出品した商品（itemsリレーションのエイリアス）
     */
    public function items()
    {
        return $this->hasMany(Item::class, 'listed_by');
    }

    /**
     * ユーザーが出品した商品
     */
    public function listedItems()
    {
        return $this->hasMany(Item::class, 'listed_by');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'user_id');
    }

    public function isFavorited(Item $item)
    {
        return $this->favorites()->where('item_id', $item->id)->exists();
    }

    public function address()
    {
        return $this->hasOne(UserAddress::class, 'user_id');
    }

    public function purchase()
    {
        return $this->hasMany(PurchaseHistory::class, 'user_id');
    }

    /**
     * このユーザーが購入した商品を取得
     */
    public function purchasedItems()
    {
        return $this->hasManyThrough(
            Item::class,
            PurchaseHistory::class,
            'user_id',    // purchase_historiesテーブルの外部キー
            'id',         // itemsテーブルの主キー
            'id',         // usersテーブルの主キー
            'item_id'     // purchase_historiesテーブルのローカルキー
        );
    }

    // ============================================
    // 取引機能用の新しいリレーション
    // ============================================

    /**
     * ユーザーが送信した取引メッセージ
     */
    public function transactionMessages()
    {
        return $this->hasMany(TransactionMessage::class);
    }

    /**
     * ユーザーが評価したレーティング
     */
    public function givenRatings()
    {
        return $this->hasMany(Rating::class, 'from_user_id');
    }

    /**
     * ユーザーが受けた評価
     */
    public function receivedRatings()
    {
        return $this->hasMany(Rating::class, 'to_user_id');
    }

    // ============================================
    // 評価関連のヘルパーメソッド
    // ============================================

    /**
     * ユーザーの評価平均を取得（四捨五入）
     *
     * @return int|null
     */
    public function getAverageRating()
    {
        return Rating::getAverageRating($this->id);
    }

    /**
     * ユーザーの評価数を取得
     *
     * @return int
     */
    public function getRatingCount()
    {
        return Rating::getRatingCount($this->id);
    }

    // ============================================
    // 取引機能用のヘルパーメソッド
    // ============================================

    /**
     * 取引中の商品を取得（購入した商品 + 出品した商品）
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTransactionItems()
    {
        // 購入した取引中の商品
        $purchasedItems = $this->purchasedItems()
            ->where('status', ItemStatus::IN_TRANSACTION->value)
            ->with(['latestMessage', 'seller'])
            ->get();

        // 出品した取引中の商品
        $listedItems = $this->listedItems()
            ->where('status', ItemStatus::IN_TRANSACTION->value)
            ->with(['latestMessage', 'buyer'])
            ->get();

        // 2つを結合して、最新メッセージの日時でソート
        return $purchasedItems->concat($listedItems)
            ->sortByDesc(function ($item) {
                return $item->latestMessage ? $item->latestMessage->created_at : $item->created_at;
            });
    }

    /**
     * 未読メッセージの総数を取得
     *
     * @return int
     */
    public function getTotalUnreadCount()
    {
        $transactionItems = $this->getTransactionItems();

        return $transactionItems->sum(function ($item) {
            return $item->getUnreadCount($this->id);
        });
    }
}
