<?php

class TestsController extends \BaseController {

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
		dd($course->students);
	}

}