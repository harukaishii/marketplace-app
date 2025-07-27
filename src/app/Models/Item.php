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

    public function categories()
    {
        return $this->belongsToMany(Category::class,
        'item_categories','item_id','category_id');
    }

    public function lister()
    {
        return $this->belongsTo(User::class,'listed_by');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class,'item_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class,'item_id');
    }

    public function purchase()
    {
        return $this->hasOne(PurchaseHistory::class, 'item_id');
    }

}
