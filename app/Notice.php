<?php

namespace App;

use App\Observers\NoticeObserver;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Notice extends BaseModel
{
    use Notifiable;
    protected $appends = ['notice_date'];

    protected static function boot()
    {
        parent::boot();

        static::observe(NoticeObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function getNoticeDateAttribute(){
        if(!is_null($this->created_at)){
            return Carbon::parse($this->created_at)->format('d F, Y');
        }
        return "";
    }
}
