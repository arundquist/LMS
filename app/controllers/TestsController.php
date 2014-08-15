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

}