<?php

class Comment extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = ['created_at'];
	
	protected $dates=['date'];
	
	// now comments belong to users and scores
	/* public function student()
	{
		return $this->belongsTo('Student');
	} */
	
	public function score()
	{
		return $this->belongsTo('Score');
	}
	
	public function user()
	{
		return $this->belongsTo('User');
	}
	
	/* public function assignment()
	{
		return $this->belongsTo('Assignment');
	} */
	
	/* public function faculty()
	{
		return $this->belongsTo('Faculty');
	} */

}