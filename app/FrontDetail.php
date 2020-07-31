<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FrontDetail extends BaseModel
{
    protected $table = 'front_details';

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return ($this->image) ? asset_url('front/' . $this->image) : asset('saas/img/home/banner-2.png');
    }
}
