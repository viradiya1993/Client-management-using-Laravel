<?php

namespace App;

use App\Observers\ClientDetailObserver;
use App\Scopes\CompanyScope;
use App\Traits\CustomFieldsTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ClientDetails extends BaseModel
{
    use Notifiable, CustomFieldsTrait;

    protected $table = 'client_details';
    protected $fillable = ['company_name','user_id','address','website','note','skype','facebook','twitter','linkedin','gst_number', 'shipping_address'];

    protected $default = ['id','company_name','address','website','note','skype','facebook','twitter','linkedin','gst_number'];

    protected $appends = ['image_url'];
    protected static function boot()
    {
        parent::boot();

        static::observe(ClientDetailObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function getImageUrlAttribute()
    {
        return ($this->image) ? asset_url('avatar/' . $this->image) : asset('img/default-profile-2.png');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScopes(['active']);
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScopes(['active', 'company']);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
