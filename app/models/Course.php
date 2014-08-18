<?php

class Course extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = ['classname', 'syllabus', 'year', 'time', 'semester'];
	
	public function algorithm()
	{
		return $this->hasOne('Algorithm');
	}
	
	public function faculties()
	{
		return $this->belongsToMany('Faculty');
	}
	
	public function dates()
	{
		return $this->hasMany('Date')->orderBy('date', 'ASC');
	}
	
	public function students()
	{
		return $this->belongsToMany('Student');
	}
	
	public function types()
	{
		return $this->hasMany('Type');
	}
	
	public function assignments()
	{
		return $this->hasManyThrough('Assignment', 'Type');
	}
	
	public function getShortAttribute()
	{
		return "{$this->classname} ({$this->semester} {$this->year})";
	}

}