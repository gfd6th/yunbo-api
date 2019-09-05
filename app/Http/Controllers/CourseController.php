<?php

namespace App\Http\Controllers;

use App\Course;
use App\Http\Resources\CourseResource;
use App\Http\Resources\LessonResource;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = $request->has('page') ? $request->query('page') : 1;
        return \Cache::remember('courses_' . $page, 60*60, function(){
            return CourseResource::collection(Course::withCount('lessons')->paginate(10));
        });


    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Course $course
     * @return \Illuminate\Http\Response
     */
    public function show(Course $course)
    {
        return
            \Cache::remember('courseDetail', 60*60, function() use ($course){
                return new CourseResource($course->load('lessons'));
            });

    }





}
