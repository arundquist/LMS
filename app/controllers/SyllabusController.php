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
		return View::make('syllabus.index', compact('courses'));
	
	}
	
	public function show($id)
	{
		$course=Course::findOrFail($id);
		Carbon::setToStringFormat('D M j');
		$matches=$course->google;
		// if $matches has just one element, it's ready for markdown
		// if it has more, then the second one is the style and the third is the html 
		// from google
		//dd(count($matches));
		if (count($matches)==1) // just markdown
		{
			$head='';
			$body=$matches[0];
		}
		else
		{
			$head=$matches[1];
			$body=$matches[2];
		};
		
		return View::make('syllabus.show', 
			['course'=>$course,
			'head'=>$head,
			'body'=>$body]);
	}
	
	public function edit($id)
	{
		$course=Course::findOrFail($id);
		return View::make('syllabus.edit', compact('course'));
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
		foreach(Input::get('topics') AS $date_id => $maintopic)
		{
			$date=Date::find($date_id);
			$date->maintopic=$maintopic;
			$date->save();
		};
		return Redirect::route('syllabus.show',[$id]);
	
	}

	

}