<?php

class GradesController extends \BaseController {

	
	public function __construct()
	    {
		
		//$this->beforeFilter('checkcorrectfaculty', ['only'=>'getWholeclass']);
		$this->beforeFilter('auth');
		
	    }
	
	/**
	 * Display a listing of the resource.
	 * GET /grades
	 *
	 * @return Response
	 */
	
	
	public function index()
	{
		echo "does this work?";
	}
	
	public function getTest()
	{
		echo "you made it to test";
	}
	
	public function getStudent($student_id)
	{
		$student=Student::findOrFail($student_id);
		$course=$student->courses()->first();
		$algs=$course->types()->lists('algorithm');
		print_r($algs);

	}
	
	public function getStudentgrades($student_id, $course_id)
	{
					
		$student = Student::findOrFail($student_id);
		$course = Course::findOrFail($course_id);
		if (Auth::user()->userable_type == 'Faculty')
		{
			$facids=$course->faculties->lists('id');
			if (!in_array(Auth::user()->userable_id, $facids))
				return "sorry, you're not a faculty member for this course";
		} elseif (Auth::user()->userable_type='Student')
		{
			if (Auth::user()->userable_id != $student->id)
				return "sorry, you're not enrolled in this course";
		};
		$totals=$student->getTotalsnewAttribute($course_id);
		//var_dump($totals);
		$role=Auth::user()->userable_type;
		
		return View::make('grades.show', 
			['student'=>$student,
			'totals'=>$totals,
			'course'=>$course,
			'role'=>$role]);
		
	}
	
	public function getWholeclass($course_id)
	{
		$facids=Course::findorFail($course_id)
			->faculties->lists('id');
		if (!in_array(Auth::user()->userable_id, $facids))
			return "sorry, you're not a faculty member for this course";
		
		$course=Course::with('types', 'types.assignments','students')->findOrFail($course_id);
		$alltotals=array();
		foreach ($course->students AS $student)
		{
			$alltotals[$student->id]=$student->getTotalsnewAttribute($course_id);
		};
		return View::make('grades.wholeclass',
			['course'=>$course,
			'alltotals'=>$alltotals]);
	}
	
	public function getSingle($student_id, $assignment_id)
	{
		$student=Student::findOrFail($student_id);
		$assignment=Assignment::findOrFail($assignment_id);
		$course=$assignment->type->course;
		// note that $student->course doesn't work because
		// it should be $student->courses
		
		if (Auth::user()->userable_type == 'Faculty')
		{
			$facids=$course->faculties->lists('id');
			if (!in_array(Auth::user()->userable_id, $facids))
				return "sorry, you're not a faculty member for this course";
		} elseif (Auth::user()->userable_type='Student')
		{
			if (Auth::user()->userable_id != $student->id)
				return "sorry, you're not enrolled in this course";
		};
		
		$role=Auth::user()->userable_type;
		
		// right here I'll have to deal with 
		// assignments that are teams.
		// if not, everything below is fine
		// if it is a team, you need to grab
		// all the students in the team (maybe make that a getter)
		// and then all the scores with this assignment id
		// and from any of those students
		$teammates=array();
		if ($assignment->team)
		{
			$teammateids=$student->teammateids($assignment_id);
			$teammates=$student->teammates($assignment_id);
			$scores=Score::with('comments', 'links')
				->whereIn('student_id', $teammateids)
				->where('assignment_id', $assignment_id)
				->orderBy('created_at', 'DESC')
				->get();
		} else {
			
		
			$scores=Score::with('comments', 'links')
				->where('student_id', $student_id)
				->where('assignment_id', $assignment_id)
				->orderBy('created_at', 'DESC')
				->get();
		};
		$allactivities=array();
		foreach ($scores AS $score)
		{
			$activities=array();
			$dates=array();
			foreach ($score->comments AS $comment)
			{
				$activities[]=['date'=>$comment->created_at,
						'type'=>'Comment',
						'data'=>$comment];
				$dates[]=$comment->created_at;
			};
			foreach ($score->links AS $comment)
			{
				$activities[]=['date'=>$comment->created_at,
						'type'=>'Link',
						'data'=>$comment];
				$dates[]=$comment->created_at;
			};
			array_multisort($dates, SORT_DESC, $activities);
			$allactivities[$score->id]=$activities;
		};
		$totals=$student->getTotalsnewAttribute($course->id);
		
		return View::make('grades.single',
			['student'=>$student,
			'assignment'=>$assignment,
			'scores'=>$scores,
			'course'=>$course,
			'allactivities'=>$allactivities,
			'totals'=>$totals,
			'role'=>$role,
			'teammates'=>$teammates]);  
	}
		
	public function postUpdatesingle($student_id, $assignment_id)
	{
		$assignment=Assignment::findOrFail($assignment_id);
		if ($assignment->team)
		{
			$student=Student::findOrFail($student_id);
			$teammateids=$student->teammateids($assignment_id);
			//$teammates=$student->teammates($assignment_id);
			$recentscore=Score::whereIn('student_id', $teammateids)
				->where('assignment_id', $assignment_id)
				->orderBy('created_at', 'DESC')
				->first();
		} else {
			
		
			$recentscore=Score::where('student_id', $student_id)
				->where('assignment_id', $assignment_id)
				->orderBy('created_at', 'DESC')
				->first();
		};
		$empty=False;
		if (count($recentscore)==0)
			$empty=True;
		$user=Auth::user();
		// make sure a score exists
		// if not, make one
		// also make one if "new" is chosen
		if ($empty || Input::get('attach')=='new')
		{
			$recentscore=new Score;
			$recentscore->student_id=$student_id;
			$recentscore->assignment_id=$assignment_id;
			// decided to add these next two lines
			// because otherwise comments without scores
			// show blanks for the scores
			$recentscore->score="pending";
			$recentscore->description="comment";
			
			$recentscore->save();
		};
		
		// if faculty, set score
		if (($user->userable_type=='Faculty') && ($recentscore->score != Input::get('score')))
		{
			$recentscore->score=Input::get('score');
			$recentscore->description=Input::get('scoredescription');
		};
		
		// if student and new chosen, set score to 'pending'
		if (($user->userable_type=='Student') && (Input::get('attach')=='new'))
		{
			$recentscore->score="pending";
			$recentscore->description="submission";
		};
		
		$recentscore->save();
		
		// now take care of comments and links
		
		if (Input::get('comment') != '')
		{
			$comment=new Comment;
			$comment->comment=Input::get('comment');
			$comment->user_id=$user->id;
			$comment->score_id=$recentscore->id;
			$comment->save();
		};
		
		if (Input::get('link') != '')
		{
			$link=new Link;
			$link->link=Input::get('link');
			$link->description=Input::get('description');
			$link->user_id=$user->id;
			$link->score_id=$recentscore->id;
			$link->save();
		};
		
		return Redirect::to(action('GradesController@getSingle', [$student_id, $assignment_id]));
	}
	
	public function getAssignment($assignment_id)
	{
		$assignment=Assignment::findOrFail($assignment_id);
		$course=$assignment->type->course;
		$students=$course->students;
		//$teams=array();
		if ($assignment->team)
		{
			if ($assignment->teams()->count()==0)
				return Redirect::to(action('CoursesController@getMaketeams', [$course->id, $assignment->id]));
			$students=$assignment->teams;
		};
		return View::make('grades.assignment',
			['assignment'=>$assignment,
			'course'=>$course,
			'students'=>$students]);
	}
	
	public function postAssignment($assignment_id)
	{
		$assignment=Assignment::findOrFail($assignment_id);
		$scorelist=array();
		$t=$assignment->team;
		// if it comes in from a team, all student_id's are
		// actually team ids. Careful with scorelist, though
		// as that probably still needs to be the teamid
		// The student_ids used below should be for the first student 
		// in the team
		
		// score will definitely be indexed by the team id
		foreach (Input::get('score') AS $student_id => $score)
		{
			if ($t)
			{
				$sid=$student_id;
				$student_id=Team::findOrFail($student_id)->students()->first()->id;
				
			} else
			{
				$sid=$student_id;
			};
			if ($score=='')
			{
				if (Input::get('scoreids')[$sid] != '')
				{
					Score::find(Input::get('scoreids')[$sid])
						->delete();
				};
			} else
			{
				if (Input::get('scoreids')[$sid] != '')
				{
					// update the score
					$scoremodel=Score::find(Input::get('scoreids')[$sid]);
					
				} else
				{
					// create the score
					$scoremodel=new Score;
					
				};
				$scoremodel->score=$score;
				$scoremodel->assignment_id=$assignment_id;
				$scoremodel->student_id=$student_id;
				$scoremodel->description=Input::get('description');
				$scoremodel->save();
				$scorelist[$sid]=['score'=>$score,
							'id'=>$scoremodel->id];
			};
			
		};
		return Redirect::to(action('GradesController@getAssignment', [$assignment_id]))
			->with('scorelist', $scorelist)
			->with('description', Input::get('description'));
	}


	/**
	 * Show the form for creating a new resource.
	 * GET /grades/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /grades
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /grades/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /grades/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /grades/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /grades/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}
	
	public function getRecentcomments()
	{
		if (!(Auth::user()->userable_type=='Faculty'))
			return "sorry, you're not faculty";
		$fac=Faculty::findOrFail(Auth::user()->userable_id);
		$courses=$fac->courses;
		$courseids=$courses->lists('id');
		$scoreids=array();
		foreach ($courses AS $course)
		{
			$assignmentids=$course->assignments->lists('id');
			if(count($assignmentids)>0)
			{
				$thesescoreids=Score::whereIn('assignment_id', $assignmentids)
					->lists('id');
				$scoreids=array_merge($scoreids, $thesescoreids);
			};
		};
		$comments=Comment::with('score','score.student')
			->whereIn('score_id', $scoreids)
			->where('user_id', '!=', Auth::user()->id)
			->orderBy('created_at', 'DESC')
			->simplePaginate(15);
			
		
		return View::make('grades.recentcomments',
			['comments'=>$comments,
			'model'=>'comments']);
	}
	
	public function getRecentlinks()
	{
		if (!(Auth::user()->userable_type=='Faculty'))
			return "sorry, you're not faculty";
		$fac=Faculty::findOrFail(Auth::user()->userable_id);
		$courses=$fac->courses;
		$courseids=$courses->lists('id');
		$scoreids=array();
		foreach ($courses AS $course)
		{
			$assignmentids=$course->assignments->lists('id');
			if(count($assignmentids)>0)
			{
				$thesescoreids=Score::whereIn('assignment_id', $assignmentids)
					->lists('id');
				$scoreids=array_merge($scoreids, $thesescoreids);
			};
		};
		$comments=Link::with('score','score.student')
			->whereIn('score_id', $scoreids)
			->where('user_id', '!=', Auth::user()->id)
			->orderBy('created_at', 'DESC')
			->simplePaginate(15);
			
		
		return View::make('grades.recentcomments',
			['comments'=>$comments,
			'model'=>'links']);
	}
	
	public function getGradelater($course_id)
	{
		if (!(Auth::user()->userable_type=='Faculty'))
			return "sorry, you're not faculty";
		$fac=Faculty::findOrFail(Auth::user()->userable_id);
		$courses=Course::where('id',$course_id)->get();
		$course=$courses->first();
		$assignmentids=$course->assignments->lists('id');
		$scores=Score::with('student','assignment')->whereIn('assignment_id', $assignmentids)
			->where('score', 'grade later')
			->orderBy('created_at', 'ASC')
			->get();
		//dd($scores);
		return View::make('grades.gradelater',
			['course'=>$course,
			'scores'=>$scores]);
	}
	
	public function getGradedtoday()
	{
		if (!(Auth::user()->userable_type=='Faculty'))
			return "sorry, you're not faculty";
		$fac=Faculty::with('courses','courses.assignments')
			->findOrFail(Auth::user()->userable_id);
		$courses=$fac->courses;
		//dd($courses);
		$allassids=[];
		foreach ($courses AS $course)
		{
			$assignmentids=$course->assignments->lists('id');
			$allassids=array_merge($allassids, $assignmentids);
		};
		//dd($allassids);
		$count=Score::whereIn('assignment_id', $allassids)
			->whereRaw('updated_at >= curdate()')
			->count();
		echo "you've graded $count assignments today";
	}
	
	public function getRecentlinksactive($course_id)
	{
		if (!(Auth::user()->userable_type=='Faculty'))
			return "sorry, you're not faculty";
		$fac=Faculty::findOrFail(Auth::user()->userable_id);
		//$courses=$fac->courses;
		$courses=Course::where('id',$course_id)->get();
		$course=$courses->first();
		//$courseids=[$course_id];
		$courseids=$courses->lists('id');
		$scoreids=array();
		foreach ($courses AS $course)
		{
			$assignmentids=$course->assignments->lists('id');
			if(count($assignmentids)>0)
			{
				$thesescoreids=Score::whereIn('assignment_id', $assignmentids)
					->lists('id');
				$scoreids=array_merge($scoreids, $thesescoreids);
			};
		};
		$comments=Link::with('score','score.student','score.assignment')
			->whereIn('score_id', $scoreids)
			->where('user_id', '!=', Auth::user()->id)
			->orderBy('created_at', 'ASC')
			->get();
			
		// here I'm going to go through each and see if the score or any 
		// other comments or files are more recent
		$activecomments=array();
		
		foreach ($comments AS $comment)
		{
			$cdate=$comment->updated_at;
			$student=$comment->user;
			$thisscore=$comment->score;
			
			// crap. I need to grab the most recent score
			
			$score=Score::where('student_id', $student->userable_id)
				->where('assignment_id',$thisscore->assignment_id)
				->orderBy('updated_at', 'DESC')
				->first();
				
			// if the score doesn't exist, it's because it's pending right now
			if (count($score)==0)
			{
				$activecomments[]=$comment;
				continue;
			};
			
			
			$scoredate=$score->updated_at;
			if ($scoredate->diffInSeconds($cdate,false)<0)
				continue; // score is more recent than link
			// here check most recent comments from me
			$scomments=$score->comments()
				->where('user_id', Auth::user()->id)
				->orderBy('updated_at', 'DESC')
				->first();
			//dd($scomments);
			if (count($scomments))
			{
				if ($scomments->updated_at->diffInSeconds($cdate,false)<0)
					continue;
			};
			// now do any links
			$scomments=$score->links()
				->where('user_id', Auth::user()->id)
				->orderBy('updated_at', 'DESC')
				->first();
			if (count($scomments))
			{
				if ($scomments->updated_at->diffInSeconds($cdate,false)<0)
					continue;
			};
			$activecomments[]=$comment;
		}; // end for loop looking for more recent scores or whatever
			
		//dd($activecomments);
		return View::make('grades.recentcommentsactive',
			['comments'=>$activecomments,
			'model'=>'links',
			'course'=>$course]);
	}
	
	public function getStudentchart($student_id,$course_id)
	{
		if (!(Auth::user()->userable_type=='Faculty'))
			return "sorry, you're not faculty";
		$fac=Faculty::findOrFail(Auth::user()->userable_id);
		$student=Student::findOrFail($student_id);
		$course=Course::findOrFail($course_id);
		$assignmentids=$course->assignments->lists('id');
		$num=array_flip($assignmentids);
		//dd($num);
		$assignmentnames=$course->assignments->lists('comments','id');
		$scores=Score::whereIn('assignment_id',$assignmentids)
			->where('student_id',$student_id)
			->get();
		$orgscores=array();
		$strings=[];
		foreach ($scores as $score) {
			if (is_numeric($score->score))
			{
				$justdate=substr($score->date, 0,-1);
				$orgscores[$score->assignment_id][]=[$score->score,$score->updated_at];
				$string="[new Date('$score->date'), ";
				
				for($i=0; $i<$num[$score->assignment_id]; $i++)
				{
					$string.=", undefined, undefined,";
				};
				$string .= " $score->score, '{$assignmentnames[$score->assignment_id]}', 'hi there'";
				for($i=$num[$score->assignment_id]; $i<max($num); $i++)
				{
					$string.=",, undefined, undefined";
				};
				$string .= "],";
				$strings[]=$string;
			}
		}
		return View::make('grades.studentchart',
			['assignments'=>$assignmentnames,
			'orgscores'=>$orgscores,
			'strings'=>$strings]);
	}
	

}