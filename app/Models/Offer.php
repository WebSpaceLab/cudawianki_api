<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Offer extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeStatus(Builder $builder, $status)
    {
        if (!is_null($status)) {
            $builder->whereIn('status', [$status]);
        }

        return $builder;
    }

    public function scopeMonth(Builder $builder, $date)
    {
        if (!is_null($date)) {
            $builder->whereBetween('created_at', [
                Carbon::createFromFormat('d-m-Y', $date)->startOfMonth(),
                Carbon::createFromFormat('d-m-Y', $date)->endOfMonth(),
            ]);
        }

        return $builder;
    }

    public function scopeSearch(Builder $builder, $term)
    {
        if (!is_null($term)) {
            $builder->where('title', 'LIKE', "%$term%");
        }

        return $builder;
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(offerCategory::class, 'offer_id', 'id');
    }

    public function getPathAttribute()
    {
        return "/oferta/" . $this->slug;
    }

    public function galleries(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'offer_media', 'offer_id', 'media_id');
    }
}
