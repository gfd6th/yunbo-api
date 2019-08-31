<?php

namespace App;

use EasyWeChat\OfficialAccount\User\UserClient;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{


    protected $guarded = [];

//    protected $appends = ['paid_stat'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->hasMany(User::class);
    }

    public function getPaidStatAttribute()
    {
        return collect($this->members->toArray())
            ->groupBy('paid')->map->count();
    }


}
