<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerPlacement extends Model
{
    use HasFactory;

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function banners()
    {
        return $this->hasMany(Banner::class, 'placement_id');
    }

    protected function creatable(): Attribute
    {
        return new Attribute(function () {
            $maxBannerCount = $this->max_banner_count;

            if ($maxBannerCount === null) {
                return true;
            }

            return $this->banners()->count() < $maxBannerCount;
        });
    }
}
