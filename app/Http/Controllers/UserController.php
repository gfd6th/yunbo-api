<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserMoreInfoResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function me()
    {
        return new UserResource(request()->user());
    }

    public function moreInfo()
    {
        return new UserMoreInfoResource(request()->user());
    }
}
