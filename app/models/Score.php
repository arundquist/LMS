<?php

class Score extends \Eloquent {

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
	
	public function user()
	{
		return $this->belongsTo('User');
	}
	
	public function comments()
	{
		return $this->hasMany('Comment');
	}
	
	public function links()
	{
		return $this->hasMany('Link');
	}
	
	protected static function boot() {
		parent::boot();
	
		static::deleting(function($score) { // before delete() method call this
		     $score->comments()->delete();
		     $score->links()->delete();
		     // do the rest of the cleanup...
		});
	    }

}