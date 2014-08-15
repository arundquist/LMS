<?php

class Date extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = ['maintopic', 'details'];
	
	protected $dates = array('date');
	
	public function assignments()
	{
		return $this->hasMany('Assignment');
	}
	
	public function course()
	{
		return $this->belongsTo('Course');
	}
	
	public function summaries()
	{
		return $this->hasMany('Summary');
	}

}