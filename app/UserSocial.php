<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSocial extends Model
{
    protected $table = 'socialites';

    protected $guarded = [];

    public static function createUser($name, $phone,$groupId, $avatar, $openid)
    {
        $user = User::create([
            'name' => $name,
            'phone' => $phone,
            'group_id' => $groupId,
            'avatar' => $avatar
        ]);

        UserSocial::create([
            'provider_id' => $openid,
            'user_id' => $user->id,
            'type' => 'wechat'
        ]);

        return $user;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
