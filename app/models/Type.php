<?php

class Type extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];
	
	public function assignments()
	{
		return $this->hasMany('Assignment');
	}
	
	public function course()
	{
		return $this->belongsTo('Course');
	}
	
	

}