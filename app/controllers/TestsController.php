<?php

class TestsController extends \BaseController {

	public function __construct()
	{
		$this->beforeFilter('authAdmin');
	}
	
	
	/**
	 * Display a listing of the resource.
	 * GET /tests
	 *
	 * @return Response
	 */
	
	public function getEmails()
	{
		return View::make('tests.roster');
	}
	 
	 public function postEmails()
	{
		preg_match_all("/<A[^>]*Student Information[^>]*>([^<]*).*?fieldmediumtext\">([0-9]+).*?do_mail\('hamline.edu','([a-zA-Z0-9]+)/s",Input::get('roster'), $matches);
		foreach ($matches[1] AS $key=>$match)
		{
			echo "\"$match\" &lt;{$matches[3][$key]}@hamline.edu&gt;:{$matches[2][$key]}<br/>";
		};
	}
	
	public function getDates()
	{
		$date="1971-07-07";
		$carbondate=Carbon::createFromFormat('Y-m-d', $date);
		$nextfriday=$carbondate->next(5);
		$followingfriday=$nextfriday->addWeek();
		$nextfriday->addWeek();
		dd($nextfriday);
	}
	
	public function getComparedates()
	{
		$curdates=Course::find(76)->dates()->select(DB::raw('date(date) as date'))->lists('date');
		dd($curdates);
	}
	
	public function getFirstornew()
	{
		$user=User::firstOrNew(['username'=>'96658']);
		if (isset($user->password))
		{
			echo "true here";
		} else
		{
			echo "false here";
		};
	}
	
	public function getGoogledoc($docid)
	{
		echo "<a href='https://docs.google.com/a/hamline.edu/document/d/".$docid."'>google doc</a><br/>";
		echo "<iframe src='https://docs.google.com/document/d/$docid/pub?embedded=true' width='640' height='400'></iframe>";
	}
	
	public function getTesthash()
	{
		if (Hash::check(Auth::user()->username, Auth::user()->password))
			echo "yep";
		
	}
	
	public function getTesthelper()
	{
		echo Helpers\trythis();
	}
	
	public function getTestgoogle()
	{
		$text="lsdjf kdlsjf sdljf google(1pqLRjsSwWhmrF8SQznKfvHnd4VePCsn0_dgHuP1Ycw4) sdfj lkd";
		return Helpers\replacegoogle($text);
	}
	
	public function getTestpregreplace()
	{
		$text = "a a sjdfkd a a rueioruewio a";
		$newtext=preg_replace('/a/', 'y', $text);
		return $newtext;
	}
	
	public function getTestcal()
	{
		$data = array(
		    3  => 'http://example.com/news/article/2006/03/',
		    7  => 'http://example.com/news/article/2006/07/',
		    13 => 'http://example.com/news/article/2006/13/',
		    26 => 'http://example.com/news/article/2006/26/'
		);
		
		return Calendar::generate(2014, 8, $data);
	}
	
	public function getTestgooglecal()
	{
		Helpers\makecalendar([3,2]);
	}
	
	public function getScrapegoogle($docid)
	{
		$all=file_get_contents("https://docs.google.com/document/d/$docid/pub?embedded=true");
		$all2=preg_replace('~<head>.*(<style.*?</style>)</head><body[^>]*?>(.*)</body></html>~','$1',$all);
		preg_match('~(<style.*?</style>).*<body[^>]*?>(.*?)</body>~', $all, $matches);
		return View::make('tests.google',
			['return'=>"$matches[1] $matches[2]"]);
	}
	
	public function getGooglesyllabus($course_id)
	{
		$course=Course::findOrFail($course_id);
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
		return View::make('tests.googlesyllabus',
			['head'=>$head,
			'body'=>$body]);   
	}
	
	public function getTestarraymap()
	{
		$a=['sdfjkl'=>'a',
		'wourei'=>'A',
		'xcvcvx'=>12];
		$replace=19;
		foreach ($a AS $key=>$value)
		{
			if($value=='A')
			{
				$a[$key]=$replace;
			};
		};
		dd($a);
	}
	
	public function getTestarrayintersect()
	{
		$first=Student::find(1270)->teams;
		$second=Assignment::find(1177)->teams;
		$found=$first->intersect($second);
		dd($found);
	}
	
	public function getTestsingle()
	{
		$student=Student::find(1270);
		dd($student);
	}
	
	public function getTestteammateids()
	{
		$student=Student::find(1272);
		dd($student->teammateids(1177));
	}
	
	public function getFixalgorithms()
	{
		$courses=Course::with('algorithm')->has('algorithm','==',0)->lists('id');
		//dd($courses);
		foreach ($courses AS $course_id)
		{
			$alg=new Algorithm;
			$alg->algorithm='';
			$alg->course_id=$course_id;
			$alg->save();
		};
	}
	
	public function getTesteval()
	{
		$totals=array();
		$totals[-2]='3';
		$bigfull="\$totals[-1]=30;";
		eval($bigfull);
		dd($totals);
	}
	
	public function getTestkeychange()
	{
		$a=['a'=>1, 'b'=>2];
		foreach ($a AS $key=>$value)
		{
			$key=1;
			echo $value;
			echo "<br/>";
		};
	}
	
	public function getTestroster($course_id)
	{
		$course=Course::findOrFail($course_id);
		foreach ($course->students AS $student)
		{
			echo "{$student->id}: {$student->name}";
			if (count($student->user))
				echo ": {$student->user->username}";
			echo "<br/>";
		};
	}
	
	public function getTestsetinif()
	{
		$a=['a'=>1,'b'=>2];
		if ($found=array_search(0, $a))
			return $found;
		return "didn't work";
	}
	
	public function getTestnew()
	{
		$user = new User;
		dd(count($user->student));
	}
	
	// I need to consolidate the student duplicates
	// they all have the same hamlineid and are connected
	// to different classes. 
	// I need to get each, find the student_id of the first one
	// then, for the rest, find out what class they're associated with
	// dissassociate them, then associate the saved one to that class
	
	public function getFixstudentduplicates($course_id)
	{
		$course=Course::findOrFail($course_id);
		$students=$course->students;
		foreach ($students AS $student)
		{
			// if user exists, then you're fine.
			// if not, need to find other student(s) with same
			// hamline id and attach them to this course
			if (!$student->user)
			{
				$otherstudents=Student::where('hamlineid', $student->hamlineid)
					->where('id', '!=', $student->id)->get();
				if (count($otherstudents)==0)
				{
					// it's a true orphan student
					// so it just needs a user
					$user=new User;
					$user->username=$student->hamlineid;
					$user->userable_type='Student';
					$user->userable_id=$student->id;
					$user->save();
				} elseif (count($otherstudents)==1)
				{
					// there's just one other
					// so switch it out
					// and delete this one
					$otherstudent=$otherstudents->first();
					$correctuser=$otherstudent->user;
					foreach ($student->courses AS $othercourse)
					{
						$othercourse->students()->detach($student->id);
						$othercourse->students()->attach($otherstudent->id);
					};
					$student->delete();
				} else
				{
					// there's a few other students
					// but I think there's only one
					// that has a user. 
					// so find it and do what you did above
					$bestotherstudent='';
					foreach ($otherstudents AS $otherstudent)
					{
						if ($otherstudent->user)
						{
							$bestotherstudent=$otherstudent;
							break;
						};
					};
					foreach ($student->courses AS $othercourse)
					{
						$othercourse->students()->detach($student->id);
						$othercourse->students()->attach($bestotherstudent->id);
					};
					$student->delete();
				};
			};
		};
		
	}
	
	public function getMailto($course_id)
	{
		$studentemails=Course::findOrFail($course_id)->students()->lists('email');
		$arg="arundquist@hamline.edu?bcc=";
		$arg.=implode(',',$studentemails);
		echo HTML::mailto($arg,'hi there');
	}
	
	public function getStandardslist($type_id)
	{
		$type=Type::findOrFail($type_id);
		echo "<ol>";
		foreach ($type->assignments AS $assignment)
		{
			echo "<li>{$assignment->comments}: {$assignment->details}</li>";
		};
		echo "</ol>";
	}
				
	
	

}