<?php

class SyllabusController extends \BaseController {

	public function __construct()
	    {
		
		//$this->beforeFilter('checkcorrectfaculty', ['only'=>'getWholeclass']);
		$this->beforeFilter('auth', ['except'=>'show']);
		
	    }
	
	/**
	 * Display a listing of the resource.
	 * GET /syllabus
	 *
	 * @return Response
	 */
	public function index()
	{
		$courses=Course::orderBy('year', 'DESC')->get();
		return View::make('Syllabus.index', compact('courses'));
	
	}
	
	public function show($id)
	{
		$course=Course::findOrFail($id);
		return View::make('syllabus.show', compact('course'));
	}
	
	public function edit($id)
	{
		$course=Course::findOrFail($id);
		return View::make('Syllabus.edit', compact('course'));
	}
	
	public function update($id)
	{
		$course = Course::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Course::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$course->update($data);
		return Redirect::route('syllabus.show',[$id]);
	
	}

	

}