<?php

namespace App;

use App\Events\OrderPaid;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Setting;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $appends = [ 'paid'];

    protected $dates = ['expire_at'];


    public function socials()
    {
        return $this->hasMany(UserSocial::class);
    }

    public function socialFor($type)
    {
        return $this->socials()->where('type', $type)->firstOrFail();
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function ownGroup()
    {
        return $this->hasMany(Group::class, 'owner_id');
    }

    public function paidForPlan($plan_price)
    {
        if ($plan_price == Setting::get('plans.yearly.price')) {
            $this->expire_at = now()->addYear();
        }
        if ($plan_price == Setting::get('plans.lifetime.price')) {
            $this->lifetime = true;
        }
        $this->save();
        event(new OrderPaid($plan_price, $this));
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getAccessToken()
    {
        return $this->createToken(config('app.token_name'))->accessToken;
    }

    public function isPremium()
    {
        return $this->yearly() || $this->lifetime || $this->isAdmin();
    }

    public function isAdmin()
    {
        return $this->id === config('app.admin_id');
    }

    public function getAvatarAttribute($avatar)
    {
        return $avatar ?: config('app.default_avatar');
    }

    public function getGroupNameAttribute()
    {
        return $this->group ? $this->group->name : '';
    }

    public function getPaidAttribute()
    {
        if($this->lifetime){
            return '终身用户';
        }
        if($this->yearly()){
            return '年费用户';
        }
        return '普通用户';
    }

    /**
     * @return bool
     */
    protected function yearly(): bool
    {
        return $this->expire_at > now();
    }
}
