<?php

class DatesController extends \BaseController {

	public function __construct()
	    {
		
		//$this->beforeFilter('checkcorrectfaculty', ['only'=>'getWholeclass']);
		$this->beforeFilter('auth', ['except'=>'show']);
		
	    }
	
	
	/**
	 * Display a listing of dates
	 *
	 * @return Response
	 */
	public function index()
	{
		$dates = Date::all();

		return View::make('dates.index', compact('dates'));
	}

	/**
	 * Show the form for creating a new date
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('dates.create');
	}

	/**
	 * Store a newly created date in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Date::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Date::create($data);

		return Redirect::route('dates.index');
	}

	/**
	 * Display the specified date.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$date = Date::findOrFail($id);
		//$dueassignments=Assignment::where('duedate', $date->date->startOfDay())->get();
		//dd($date->course->assignments);
		$dueassignments=$date->course->assignments()
			->where('duedate', $date->date->startOfDay())->get();
		return View::make('dates.show', 
			['date'=>$date,
			 'dueassignments'=>$dueassignments]);
	}

	/**
	 * Show the form for editing the specified date.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		
		$date = Date::with('course', 'assignments')->find($id);
		$course=$date->course;
		
		if (Auth::user()->userable_type == 'Faculty')
		{
			$facids=$course->faculties->lists('id');
			if (!in_array(Auth::user()->userable_id, $facids))
				return "sorry, you're not a faculty member for this course";
		} elseif (Auth::user()->userable_type='Student')
		{
			return "sorry, faculty only";
		};
		
		$types=$date->course->types->lists('type','id');

		return View::make('dates.edit', 
			['date'=>$date,
			'types'=>$types]);
	}

	/**
	 * Update the specified date in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$date = Date::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Date::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$date->update($data);
		
		foreach (Input::get('asslist') AS $key=>$assignmentdata)
		{
			If($key>0)
			{
				$assignment=Assignment::findOrFail($key);
			} elseif ($assignmentdata['comments']!='') {
				$assignment=new Assignment;
			} else {
				continue;
			};
			//$assignment->update($assignmentdata);
			
			$assignment->comments=$assignmentdata['comments'];
			$assignment->details=$assignmentdata['details'];
			$assignment->total=$assignmentdata['total'];
			$assignment->team=$assignmentdata['team'];
			$assignment->duedate=$assignmentdata['duedate'];
			$assignment->date_id=$date->id;
			if ($assignmentdata['newtype'] != '')
			{
				$type=new Type;
				$type->type=$assignmentdata['newtype'];
				$type->course_id=$date->course_id;
				$type->save();
				$assignment->type_id=$type->id;
			} else {
				$assignment->type_id=$assignmentdata['type_id'];
			};
			$assignment->save();
			//$assignment->comments=$assignmentdata['comments'];
			//$assignment->save();
			
			// the problem seems to be the quotes
			
			//var_dump($assignmentdata['comments']);
			
			
		};
		return Redirect::route('dates.show', [$id]);
	}

	/**
	 * Remove the specified date from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Date::destroy($id);

		return Redirect::route('dates.index');
	}

}
