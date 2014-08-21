<?php

class Team extends \Eloquent {
	protected $fillable = [];
	
	public function assignments()
	{
		return $this->belongsToMany('Assignment');
	}
	
	public function course()
	{
		return $this->belongsTo('Course');
	}
	
	public function students()
	{
		return $this->belongsToMany('Student');
	}
}