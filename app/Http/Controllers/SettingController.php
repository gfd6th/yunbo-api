<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return [
            'plans' =>
                [
                    'yearly'   => setting('plans.yearly.price'),
                    'lifetime' => setting('plans.lifetime.price'),
                ],
        ];
    }
}
