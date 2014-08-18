<?php

class CoursesController extends \BaseController {

	public function __construct()
	{
		$this->beforeFilter('authFaculty', ['except'=>'getCalendar']);
	}
	
	
	
	public function getDoesthiswork()
	{
		return "yep it does";
	}

	/**
	 * Show the form for creating a new course
	 *
	 * @return Response
	 */
	public function getCreate()
	{
		return View::make('courses.create');
	}

	/**
	 * Store a newly created course in storage.
	 *
	 * @return Response
	 */
	public function postCreate()
	{
		$validator = Validator::make($data = Input::all(), Course::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$course=Course::create($data);
		$course->faculties()->sync([Auth::user()->userable_id]);

		return Redirect::route('syllabus.show',[$course->id]);
		
		//return Input::get('roster');
		
	}

	public function getAdddates($course_id)
	{
		$course=Course::findOrFail($course_id);
		$facids=$course->faculties()->select('faculties.*')->lists('id');
		if (!in_array(Auth::user()->userable_id, $facids))
		{
			return Redirect::to(action('UsersController@getLogin'));
		};
		
		$currentdates=$course->dates;
		return View::make('courses.adddates',
			['course'=>$course,
			'currentdates'=>$currentdates]);
	}
	
	public function postAdddates($course_id)
	{
		$course=Course::findOrFail($course_id);
		$coursetime=$course->time;
		$timearray=explode(":", $coursetime);
		$deletedates=Input::get('deletedates');
		$days=Input::get('days');
		if (isset($deletedates))
		{
			foreach ($deletedates AS $dateid)
			{
				Date::find($dateid)->delete();
			};
		};
		if (Input::get('adddate')!='')
		{
			$date=new Date;
			$date->date=Input::get('adddate');
			$date->course_id=$course_id;
			$date->save();
		};
		
		// get all dates for course (rounds to date)
		
		$curdates=$course->dates()->select(DB::raw('date(date) as date'))->lists('date');
		
		if ((Input::get('startdate')!='') && (Input::get('enddate')!='') 
			&& isset($days))
		{
			$start=Carbon::createFromFormat('Y-m-d H:i:s', Input::get('startdate')." $coursetime");
			$end=Carbon::createFromFormat('Y-m-d', Input::get('enddate'));
			// if date is in the days list, add
			if (array_key_exists($start->dayOfWeek, Input::get('days')))
			{
				// make sure date doesn't already exist
				// could make a list of dates up above
				// but they're all carbon instances and this
				// carbon instance will have the current time
				// although I could do ->startOfDay to set it to zero
				if (!in_array($start->toDateString(), $curdates))
				{
					$date=new Date;
					$date->date=$start;
					$date->course_id=$course_id;
					$date->save();
					$curdates[]=$start->toDateString();
				};
				
				
			};
			foreach ($days AS $day)
			{
				$beg=$start->copy();
				$beg->next($day);
				//dd($beg->diffInDays($end));
				// start a loop where you keep adding
				// seven days.
				// for each check if it's after the
				// end date
				// and also check if the date already exists
				// I think doing $beg->addWeek()
				// will keep changing $beg throughout the loop
				while ($beg->diffInDays($end->startOfDay(),false) >= 0)
				{
					
					if (!in_array($beg->toDateString(), $curdates))
					{
						$date=new Date;
						$date->date=$beg->setTime($timearray[0], $timearray[1], $timearray[2]);
						$date->course_id=$course_id;
						$date->save();
						$curdates[]=$beg->toDateString();
						
					};
					$beg->addWeek();
				};
			};
		};
		return Redirect::action('CoursesController@getAdddates', [$course_id]);
	}
	

	public function getAddroster($course_id)
	{
		$course=Course::findOrFail($course_id);
		$facids=$course->faculties()->select('faculties.*')->lists('id');
		if (!in_array(Auth::user()->userable_id, $facids))
		{
			return Redirect::to(action('UsersController@getLogin'));
		};
		$students=$course->students;
		return View::make('courses.addroster',
			['course'=>$course,
			'students'=>$students]);
	}

	public function postAddroster($course_id)
	{
		$course=Course::findOrFail($course_id);
		//$studenthamlineidlist=$course->students()->lists('hamlineid','students.id');
		
		$deleteids=Input::get('delete');
		if (isset($deleteids))
		{
			// to delete, you need to go through all the
			// relationships
			
			// no, I've used the boot() function in both
			// students and scores to delete the daughters
			// ie the comments and the links
			foreach ($deleteids AS $id)
			{
				$stu=Student::find($id)->delete();
			};
		};
		$studentsincourse=$course->students();
		$studenthamlineidlist=array();
		foreach ($studentsincourse AS $studentincourse)
		{
			$studenthamlineidlist[$studentincourse->id]=$studentincourse->hamlineid;
		};
		
		// next add the individual student only if it doesn't exist
		// if it does exist, go ahead and change the info I guess
		
		// actually maybe make an array of name, username, email
		// and do a big foreach. The individual one just gets 
		// added to the whole roster
		$newstudents=array();
		If ((Input::get('name')!='')&&(Input::get('username')!='')&&(Input::get('email')!=''))
		{
			$newstudents[]=['name'=>Input::get('name'),
					'username'=>Input::get('username'),
					'email'=>Input::get('email')];
		};
		
		// now get the roster
		if (Input::get('roster')!='')
		{
			preg_match_all("/<A[^>]*Student Information[^>]*>([^<]*).*?fieldmediumtext\">([0-9]+).*?do_mail\('([^']*)','([a-zA-Z0-9]+)/s",Input::get('roster'), $matches);
			foreach ($matches[1] AS $key=>$match)
			{
				$newstudents[]=['name'=>$match,
						'username'=>$matches[2][$key],
						'email'=>$matches[4][$key]."@".$matches[3][$key]];
				//echo "\"$match\" &lt;{$matches[3][$key]}@hamline.edu&gt;:{$matches[2][$key]}<br/>";
			};
		};
		//dd($newstudents);
		If (count($newstudents)>0)
		{
			$syncids=array();
			foreach ($newstudents AS $newstudent)
			{
				$user=User::firstOrNew(['username'=>$newstudent['username']]);
				// I guess I'll assume that fac and students will
				// always have different ids. Probably a mistake
				if (!isset($user->password))
					$user->password=Hash::make($newstudent['username']);
				$user->userable_type="Student";
				
				// now see if student exists
				// this next line doesn't work because firstOrNew
				// doesn't work on chains like this
				//$stu=$course->students()->firstOrNew(['hamlineid'=>$newstudent['username']]);
				if ($foundkey=array_search($newstudent['username'], $studenthamlineidlist))
				{
					// it found the student
					$stu=Student::find($foundkey);
				} else
				{
					$stu = New Student;
				};
				
				$stu->name=$newstudent['name'];
				$stu->hamlineid=$newstudent['username'];
				$stu->email=$newstudent['email'];
				$stu->save();
				// maybe do this in one shot at the end?
				//$course->students()->sync([$stu->id]);
				$syncids[]=$stu->id;
				$user->userable_id=$stu->id;
				$user->save();
			};
			$course->students()->sync($syncids);
		};
		return Redirect::action("CoursesController@getAddroster", [$course_id]);
	}
	
	public function getCalendar($course_id)
	{
		$course=Course::findOrFail($course_id);
		$dates=$course->dates;
		/*
		$v=View::make('times.googlecalendar')
			->with('courses',$cs)
			->with('term', $term)
			->with('title', $title);
		return Response::make($v,"200")
			->header('Content-Type', 'text/calendar')
			->header('Content-Disposition', 'attachment; filename="test.ics"');
		*/
		$title="{$course->classname} {$course->semester} {$course->year}";
		$v=View::make('courses.calendar')
			->with('title', $title)
			->with('dates', $dates);
		return Response::make($v,"200")
			->header('Content-Type', 'text/calendar')
			->header('Content-Disposition', 'attachment; filename="test.ics"');
	}
	
	public function getAlgorithms($course_id)
	{
		$course=Course::with('types', 'types.assignments')->findOrFail($course_id);
		return View::make('courses.algorithms', compact('course'));
	}
	
	public function postAlgorithms($course_id)
	{
		$course=Course::with('types')->findOrFail($course_id);
		$types=$course->types;
		$coursealgo=$course->algorithm;
		$coursealgo->algorithm = Input::get('coursealgorithm');
		$coursealgo->save();
		foreach ($types AS $type)
		{
			$type->algorithm=Input::get("typealgorithms[{$type->id}]");
			$type->save();
		};
		foreach (Input::get('deletetypes') AS $key=>$value)
			Type::findOrFail($key)->delete();
		return Redirect::to(action('CoursesController@getAlgorithms', [$course_id]));
	}

}
