<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FrontClients extends Model
{
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return ($this->image) ? asset_url('front/client/' . $this->image) : asset('saas/img/home/client-'.($this->id%5).'.png');
    }
}
