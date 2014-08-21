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
	
	public function getStudentlistAttribute()
	{
		return implode(', ', $this->students()->lists('name'));
	}
	
	public function getNameAttribute()
	{
		return implode(', ', $this->students()->lists('name'));
	}
}