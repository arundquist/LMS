<?php

class PublicController extends \BaseController {

	public function getSyllabi($faculty_id)
	{
		$faculty=Faculty::findOrFail($faculty_id);
		return View::make('public.faculty', compact('faculty'));
	}

}