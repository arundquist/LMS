<?php

class Assignment extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = ['comments', 'details', 'type_id', 'duedate', 'total'];
	
	public function type()
	{
		return $this->belongsTo('Type');
	}
	
	public function teams()
	{
		return $this->belongsToMany('Team');
	}
	
	public function date()
	{
		return $this->belongsTo('Date');
	}
	
	public function comments()
	{
		return $this->hasMany('Comment');
	}
	
	public function files()
	{
		return $this->hasMany('File');
	}
	
	public function scores()
	{
		return $this->hasMany('Score');
	}
	
	public function extras()
	{
		return $this->belongsToMany('Extra');
	}

}