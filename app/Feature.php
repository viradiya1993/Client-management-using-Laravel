<?php

namespace App;

class Feature extends BaseModel
{
    protected $table = 'features';
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if ($this->type == 'image' && is_null($this->image)) {
            if ($this->id == 1) {
                return asset('saas/img/svg/mock-3.png');
            }
            if ($this->id == 2) {
                return asset('saas/img/svg/mock-2.svg');
            }
            if ($this->id == 3) {
                return asset('saas/img/svg/mock-1.svg');
            }
        }
        if ($this->type == 'apps') {
            return ($this->image) ? asset_url('front/feature/' . $this->image) : asset('saas/img/pages/app-' . (($this->id) % 6) . '.png');
        }

        return ($this->image) ? asset_url('front/feature/' . $this->image) : asset('front/img/tools.png');
    }
}
