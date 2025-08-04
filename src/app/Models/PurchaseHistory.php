<?php

namespace App\Models;

use App\Enums\PaymentType;
use Faker\Provider\ar_EG\Payment;
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserAddress> $addresses
 * @property-read int|null $addresses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
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

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_id');
    }

    public function items()
    {
        return $this->belongsToOne(Item::class, 'item_id');
    }

    public function addresses()
    {
        return $this->belongsToMany(UserAddress::class, 'use_address_id');
    }

}
