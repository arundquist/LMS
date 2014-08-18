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

}