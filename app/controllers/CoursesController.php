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
		$alg=new Algorithm;
		$alg->course_id=$course->id;
		$alg->algorithm='';
		$alg->save();
		$course->save();

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
			$date->date=Input::get('adddate')." $coursetime";
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
		// right here you need to make an array
		// or student ids and add them to the
		// new ones below before syncing.
		// right now all old students are getting deleted
		$currentstudentids=$course->students->lists('id');
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
			preg_match_all("/<a[^>]*Student Information[^>]*>([^<]*).*?fieldmediumtext\">([0-9]+).*?do_mail\('([^']*)','([a-zA-Z0-9]+)/s",Input::get('roster'), $matches);
			//dd(Input::get('roster'));
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
			//$syncids=array();
			$syncids=$currentstudentids;
			foreach ($newstudents AS $newstudent)
			{
				$user=User::firstOrNew(['username'=>$newstudent['username']]);
				// I guess I'll assume that fac and students will
				// always have different ids. Probably a mistake

				// if the user exists, there must be an existing
				// student as well. I should grab that one
				// instead of looking below at whether the
				// student already exists in the course

				if (!isset($user->password))
					$user->password=Hash::make($newstudent['username']);
				$user->userable_type="Student";

				// now see if student exists
				// this next line doesn't work because firstOrNew
				// doesn't work on chains like this
				//$stu=$course->students()->firstOrNew(['hamlineid'=>$newstudent['username']]);
				/* if ($foundkey=array_search($newstudent['username'], $studenthamlineidlist))
				{
					// it found the student
					$stu=Student::find($foundkey);
				} else
				{
					$stu = New Student;
				}; */

				if (count($user->userable))
				{
					//it found the student
					$stu=$user->userable;
				} else
				{
					$stu = New student;
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
			$type->algorithm=Input::get("typealgorithms")[$type->id];
			$type->save();
		};
		$dtypes=Input::get('deletetypes');
		if (isset($dtypes))
		{
			foreach (Input::get('deletetypes') AS $key=>$value)
				Type::findOrFail($key)->delete();
		};
		return Redirect::to(action('CoursesController@getAlgorithms', [$course_id]));
	}

	public function getGroups($course_id)
	{
		$course=Course::findOrFail($course_id);
		$students=$course->students->lists('name');
		return View::make('courses.groups', compact('students'));
	}

	public function postGroups()
	{
		$in=Input::get('in');
		$max=Input::get('max');
		$num=count($in);
		$numgroups=ceil($num/$max);
		$diff=$numgroups*$max-$num;
		$rolelist=array('Scribe', 'Calculator', 'Skeptic','Moderator',
			'','','','','','','','');
		foreach (range(1,$numgroups) AS $gnum) {
			foreach (range(0,$max-2) AS $tmp) {
				$role[]="$gnum: $rolelist[$tmp]";
			}
		//	$role[]="$gnum: Scribe";
		//	$role[]="$gnum: Calculator";
		//	$role[]="$gnum: Skeptic";
			};
		if ($numgroups-$diff>0) {
		foreach (range(1,$numgroups-$diff) AS $gnum) {
			$i=$max-1;
			$role[]="$gnum: $rolelist[$i]";
			};
			};

		shuffle($role);
		return View::make('courses.groups',
			['in'=>$in,
			'roles'=>$role]);

	}

	public function getMaketeams($course_id, $assignment_id)
	{
		$course=Course::with('teams', 'teams.students', 'students')->findOrFail($course_id);
		$assignment=Assignment::with('teams')->findOrFail($assignment_id);
		// make an array that's like 'student_id'=>'team number'
		$teamarray=array();
		$currentteams=$assignment->teams;
		foreach ($currentteams AS $team)
		{
			foreach ($team->students AS $student)
			{
				$teamarray[$student->id]=$team->id;
			};
		};
		// at this point some students *might* not be in a team
		$roster=$course->students;
		foreach ($roster AS $student)
		{
			$teamarray=array_add($teamarray, $student->id, '');
		};


		// I think I need to return team sets to choose from
		// do that by grabbing assignments and associated teams

		$assignmentswithteams=$course->assignments()->has('teams')
			->with('teams')->get();



		return View::make('courses.maketeams',
			['course'=>$course,
			'assignment'=>$assignment,
			'roster'=>$roster,
			'currentteams'=>$currentteams,
			'assignmentswithteams'=>$assignmentswithteams,
			'teamarray'=>$teamarray]);
	}

	public function postMaketeams($course_id, $assignment_id)
	{
		$course=Course::with('teams')->findOrFail($course_id);
		$assignment=Assignment::findOrFail($assignment_id);
		$teamset=Input::get('newteamset');
		$newteams=array();
		if (isset($teamset))
		{
			//this means a teamset has been chosen
			// $teamset will be the assignment id
			// so grab all those teams and associate
			// them with the current assignment id
			$teams=Assignment::findOrFail($teamset)->teams()->select('team_id AS tid')->lists('tid');
			$assignment->teams()->sync($teams);
		} else
		{
			// here we take whatever was given for the roster
			// need an array of students for each team that exists
			$existingteams=$assignment->teams;
			$teamlists=array();
			foreach ($existingteams AS $team)
			{
				//$teamlists[$team->id]=$team->students()->select('id as sid')->lists('sid');
				$teamlists[$team->id]=array();
			};
			foreach (Input::get('team') AS $student_id => $newteam)
			{
				if ($newteam != '') // if it's blank, don't do anything
				{
					if (array_key_exists($newteam, $newteams))
					{
						//Student::find($student_id)->teams()->sync([$newteams[$newteam]], false);
						$teamlists[$newteams[$newteam]][]=$student_id;
					} else {
						//$possibleteam=Team::find($newteam); //problematic if team isn't associated with assignment
						if (array_key_exists($newteam, $teamlists))
						{
							$teamlists[$newteam][]=$student_id;
						} else {
							$team=new Team;
							$team->description=$newteam;
							//$team->assignment_id=$assignment->id;
							$team->course_id=$course->id;
							$team->save();
							$team->assignments()->attach($assignment->id);
							$teamlists[$team->id][]=$student_id;
							$newteams[$newteam]=$team->id;
						};
					};
				};
			};
			foreach ($teamlists AS $team_id => $studentlist)
			{
				Team::find($team_id)->students()->sync($studentlist);
			};
			// what about deleting teams that aren't present any more?
			// right now it makes a team with no students
			$emptyteams=$assignment->teams()->has('students', '==', 0)->get();
			foreach ($emptyteams AS $team)
			{
				$team->assignments()->sync([]);
				$team->delete();
			};
		};
		return Redirect::to(action('CoursesController@getMaketeams', [$course_id, $assignment_id]));
		// need to figure out how to deal with letters
		// if letters, make a bunch of new teams assuming it's not ''
		// so if 'A', check if that's already been made
		// if so, just add the student to that team
		// if not, make that team and add the student
		// and somehow add that team to the list to be checked
		// in the next iteration
		// if '', don't do anything
		// if it's a number, grab that team and add the student
		// Another thought for the stuff above is once 'A' is made,
		// replace all 'A's in the array with the new team id.
		// Then the "if it's a number" approach will just work

		// on the other hand, if other teams are selected
		// just use those and ignore the teams.
		// there's still a problem with one student being on
		// multiple teams for the same assignment, though.

		// I think I should change the form so that you can select
		// collections of teams (like ones for other assignments)
	}

	// make a link to reset a student's password that takes their user_id
	// it should check to make sure the student is in a class taught by the logged
	// in faculty.  I'll do that be passing the class_id as well

	public function getResetstudentpassword($class_id, $u_id)
	{
		$curfac=Faculty::findOrFail(Auth::user()->userable_id);
		$classes=$curfac->courses->lists('id');
		if (!in_array($class_id, $classes))
			return "oops, this isn't one of your classes";
		$course=Course::findOrFail($class_id);
		$uids=array();
		foreach ($course->students AS $student)
			$uids[]=$student->user->id;
		if (!in_array($u_id, $uids))
			return "oops, this student isn't in this class";
		$user=User::findOrFail($u_id);
	 	$user->password=Hash::make($user->username);
	 	$user->save();
	 	return Redirect::to(action('UsersController@getDashboard'));
	}

	public function getStandards($class_id)
	{
		$course=Course::findOrFail($class_id);
		echo "<h1>$course->classname</h1>";
		$types=Type::where('course_id',$class_id)->where('type','standard')->lists('id');
		if (count($types)>0)
		{
			$assignments=Assignment::where('type_id',$types[0])->get();
			echo "<table border='1'>";
			echo "<tr><th>Comment</th><th>Details</th></tr>";
			foreach ($assignments AS $a)
			{
				echo "<tr><td>$a->comments</td><td>$a->details</td></tr>";
			}
			echo "</table>";
		};
		//dd($assignments);
	}

}
