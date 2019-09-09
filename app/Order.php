<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use \Znck\Eloquent\Traits\BelongsToThrough;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsToThrough(Group::class, User::class);
    }


}
