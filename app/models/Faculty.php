<?php

class Faculty extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];
	
	public function courses()
	{
		return $this->belongsToMany('Course')->orderBy('year', 'DESC')
					->orderBy('semester', 'ASC');
	}
	
	public function comments()
	{
		return $this->hasMany('Comment');
	}
	
	public function files()
	{
		return $this->hasMany('File');
	}
	
	public function user()
	{
		return $this->morphOne('User', 'userable');
	}

}