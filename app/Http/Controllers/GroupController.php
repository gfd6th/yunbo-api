<?php

namespace App\Http\Controllers;

use App\Group;
use App\Http\Resources\GroupResource;
use Illuminate\Http\Request;

class GroupController extends Controller
{


    /**
     * Display the specified resource.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $groups = request()->user()->ownGroup;
//        $this->authorize('own', $groups);
        if(count($groups) === 0){
            abort(403);
        }

        return GroupResource::collection($groups);
    }

}
