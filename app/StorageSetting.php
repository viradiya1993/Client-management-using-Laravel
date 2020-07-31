<?php

namespace App;

use App\Observers\StorageSettingObserver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StorageSetting extends BaseModel
{
    protected $table = 'storage_settings';

    protected $fillable = ['filesystem','auth_keys','status'];

    protected static function boot()
    {
        parent::boot();

        static::observe(StorageSettingObserver::class);
    }

}
