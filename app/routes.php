<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
\URL::forceSchema('https');
Route::get('/', function()
{
	$faculties=Faculty::all();
	return View::make('public.welcome', compact('faculties'));
});


Route::get('markdown', function()
	{
		return Markdown::render('hi there');
	});

Route::get('fix', function()
	{
		$user=User::findOrFail(604);
	//	$user->password=Hash::make("test");
	//	$user->save();
		dd(Hash::make("sdfdsf"));
	});

Route::controller('grades', 'GradesController');
Route::controller('user', 'UsersController');
Route::controller('tests', 'TestsController');
Route::controller('public', 'PublicController');
Route::resource('syllabus', 'SyllabusController');
Route::resource('extras', 'ExtrasController');

Route::resource('algorithms', 'AlgorithmsController');
Route::resource('assignments', 'AssignmentsController');
Route::resource('comments', 'CommentsController');
Route::controller('courses', 'CoursesController');
Route::resource('dates', 'DatesController');
Route::resource('faculty', 'FacultiesController');
Route::resource('files', 'FilesController');
Route::resource('scores', 'ScoresController');
Route::resource('students', 'StudentsController');
Route::resource('summaries', 'SummariesController');
Route::resource('types', 'TypesController');

// testing stuff



Route::get('testfilt/{course_id}', ['before'=>'checkcorrectfaculty', function($course_id)
	{
		echo "hi there";
	}]);

Route::get('testnumeric', function()
	{
		$scores=Score::whereRaw("score REGEXP '^[0-9\.]+$'")->get();
		return count($scores);
	});

Route::get('testdd', function()
	{
		$links=Link::all();
		dd($links);
	});

Route::get('liststudents', function()
	{
		$students=Student::groupBy('name')
			->get(array(
				DB::Raw('id, name, hamlineid, group_concat(courseid) as courselist')));
		foreach ($students AS $student)
		{
			echo "{$student->id}: {$student->name} {$student->hamlineid} {$student->courselist}<br/>";
			//I've run this so don't run it again
			/*
			ini_set('max_execution_time', 1000); //300 seconds = 5 minutes

			$user = new User;
			$user->username=$student->hamlineid;
			$user->password=Hash::make($student->hamlineid);
			$user->userable_id=$student->id;
			$user->userable_type="Student";
			$user->save();
			//$actualstudent=Student::find($student->id);
			$student->courses()->sync(explode(",",$student->courselist));
			*/
		};
	});

Route::get('showdups', function()
	{
		$students=User::groupBy('username')
			->having('num','>','1')
			->get(array(
				DB::Raw('id, username, userable_type, userable_id, count(id) as num, group_concat(userable_id) AS sid')));
		/* foreach ($students AS $student)
		{
			echo "{$student->userable->name}: {$student->sid} <br/>";
			$duparray=explode(",", $student->sid);
			$first=array_shift($duparray);
			DB::table('course_student')
				->whereIn('student_id', $duparray)
				->update(['student_id'=>$first]);
			DB::table('scores')
				->whereIn('student_id', $duparray)
				->update(['student_id'=>$first]);

		}; */
	});

Route::get('fixscores', function()
	{
		/*
		this is done
		$users=User::where('userable_type','Student')->get();
		ini_set('max_execution_time', 1000); //300 seconds = 5 minutes
		foreach ($users AS $user)
		{
			$ids=Student::where('hamlineid',$user->username)->lists('id');
			if (count($ids)>0)
			{
				echo implode(', ', $ids);
				echo ": ";
				echo count($ids);
				echo "<br/>";
				Score::whereIn('old_student_id', $ids)
					->update(['student_id'=>$user->userable_id]);
			};

		};
		*/
	});

Route::get('allassignments/{course_id}', function($course_id)
	{
		$course=Course::findOrFail($course_id);
		$asses=$course->assignments->lists('id');
		print_r($asses);
	});

Route::get('testschemagrades', function()
	{
		$student=Student::find(1111);
		echo "<pre>";
		print_r($student->getTotalsnewAttribute(70));
		echo "</pre>";
	});

Route::get('getfaculty', function()
	{
		//I've already run this so don't do it again
		/*$faculties=Faculty::all();
		foreach ($faculties AS $faculty)
		{


			$user = new User;
			$user->username=$faculty->hamlineid;
			$user->password=Hash::make($faculty->hamlineid);
			$user->userable_id=$faculty->id;
			$user->userable_type="Faculty";
			$user->save();

		};*/
	});

Route::get('seesql', function()
	{
		echo(User::where('username','hithere')->toSql());
	});

Route::get('fixcomments', function()
	{
		$comments=Comment::with('student', 'assignment', 'student.scores','student.user')->get();
		$i=0;
		ini_set('max_execution_time', 1000); //300 seconds = 5 minutes
		foreach($comments AS $comment)
		{
			/* $recentscore=$comment
					->student
					->scores()
					->where('assignment_id',$comment->assignment_id)
					->orderBy('date', "DESC")
					->first(); */
			$recentscore=Score::where('assignment_id', $comment->assignment_id)
					->where('old_student_id', $comment->student_id)
					->orderBy('date', "DESC")
					->first();
			if (count($recentscore)==0) continue;
			$comment->score_id=$recentscore->id;
			if ($comment->fac_id == -1)
			{
				$comment->user_id=$comment->student->user->id;
			} else {
				$course=$comment->assignment->type->course;
				$fac=$course->faculties->first();
				$comment->user_id=$fac->user->id;
			};
			$comment->save();
			$i++;
		};
		return $i;
	});

Route::get('fixcommentusers', function()
	{
		/* $comments=Comment::all();
		foreach ($comments AS $comment)
		{
			$recentscore=Score::where('assignment_id', $comment->assignment_id)
					->where('student_id', $comment->student_id)
					->orderBy('date', "DESC")
					->first();
			if (count($recentscore)==0) continue;
			if ($comment->faculty_id == -1)
				{
					$comment->user_id=$comment->score->student->user->id;
					$comment->save();
				};
		}; */
	});

Route::get('testsort', function()
	{
		$a=['a1'=>['hi'=>1, 'there'=>7], 'a2'=>['hi'=>2, 'there'=>2]];
		$a=Comment::where('user_id',604)->take(50)->get();
		$c=array();
		$b=array();
		foreach ($a AS $key=>$row)
		{
			$c[$key]=$row;
			$b[$key]=$row->updated_on;
		};
		array_multisort($b, SORT_ASC, $c);
		print_r($a);
	});

Route::get('populatecommentdates', function()
	{
		/* $comments=Comment::all();
		foreach ($comments AS $comment)
		{
			$comment->update(['created_at'=>$comment->date]);
		}; */
	});

Route::get('findorphans', function()
	{
		$typelist=Assignment::lists('id');
		$orphanedcomments=Comment::whereNotIn('assignment_id', $typelist)->delete();
		print_r($orphanedcomments);
	});


Route::get('testuser/{id}', function($id)
	{
		$user=User::findOrFail($id);
		foreach ($user->userable->courses AS $course)
		{
			echo "{$course->classname}<br/>";
		};
	});
