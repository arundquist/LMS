<?php

class Summary extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];
	
	public function student()
	{
		return $this->belongsTo('Student');
	}
	
	public function date()
	{
		return $this->belongsTo('Date');
	}

}