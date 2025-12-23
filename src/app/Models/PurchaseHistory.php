<?php

namespace App\Models;

use App\Enums\PaymentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PurchaseHistory
 *
 * @property int $id
 * @property int $user_id
 * @property int $item_id
 * @property int $user_address_id
 * @property PaymentType $payment_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Item $item
 * @property-read \App\Models\UserAddress $userAddress
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseHistory whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseHistory wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseHistory whereUserAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseHistory whereUserId($value)
 * @mixin \Eloquent
 */
class PurchaseHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'user_address_id',
        'payment_type',
    ];

    protected $casts = [
        'payment_type' => PaymentType::class,
    ];

    /**
     * 購入者とのリレーション
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 商品とのリレーション
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * 配送先住所とのリレーション
     */
    public function userAddress()
    {
        return $this->belongsTo(UserAddress::class, 'user_address_id');
    }
}
