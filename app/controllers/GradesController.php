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
		
		$scores=Score::with('comments', 'links')
			->where('student_id', $student_id)
			->where('assignment_id', $assignment_id)
			->orderBy('created_at', 'DESC')
			->get();
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
			'dates'=>$dates,
			'totals'=>$totals,
			'role'=>$role]);  
	}
		
	public function postUpdatesingle($student_id, $assignment_id)
	{
		$recentscore=Score::where('student_id', $student_id)
				->where('assignment_id', $assignment_id)
				->orderBy('created_at', 'DESC')
				->first();
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
		
		return View::make('grades.assignment',
			['assignment'=>$assignment,
			'course'=>$course,
			'students'=>$students]);
	}
	
	public function postAssignment($assignment_id)
	{
		$assignment=Assignment::findOrFail($assignment_id);
		$scorelist=array();
		foreach (Input::get('score') AS $student_id => $score)
		{
			if ($score=='')
			{
				if (Input::get('scoreids')[$student_id] != '')
				{
					Score::find(Input::get('scoreids')[$student_id])
						->delete();
				};
			} else
			{
				if (Input::get('scoreids')[$student_id] != '')
				{
					// update the score
					$scoremodel=Score::find(Input::get('scoreids')[$student_id]);
					
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
				$scorelist[$student_id]=['score'=>$score,
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

}