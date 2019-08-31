<?php
/**
 * Created by PhpStorm.
 * User: hexu
 * Date: 2019/8/19
 * Time: 11:23 AM
 */

namespace App\Socialite;


interface SocialiteInterface
{
    public function redirect($provider);
}