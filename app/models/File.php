<?php

class File extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];
	
	public function assignment()
	{
		return $this->belongsTo('Assignment');
	}
	
	public function student()
	{
		return $this->belongsTo('Student');
	}
	
	public function faculty()
	{
		return $this->belongsTo('Faculty');
	}

}