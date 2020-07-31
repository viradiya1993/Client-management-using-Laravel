<?php

namespace App;

use App\Observers\TicketObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends BaseModel
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $appends = ['created_on'];

    protected static function boot()
    {
        parent::boot();

        static::observe(TicketObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function requester(){
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScopes(['active']);
    }

    public function agent(){
        return $this->belongsTo(User::class, 'agent_id')->withoutGlobalScopes(['active']);
    }

    public function reply()
    {
        return $this->hasMany(TicketReply::class, 'ticket_id');
    }

    public function tags(){
        return $this->hasMany(TicketTag::class, 'ticket_id');
    }

    public function getCreatedOnAttribute(){
        if(!is_null($this->created_at)){
            return $this->created_at->format('d M Y H:i');
        }
        return "";
    }
}
