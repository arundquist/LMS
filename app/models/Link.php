<?php

class Link extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];
	
	public function score()
	{
		return $this->belongsTo('Score');
	}
	
	public function user()
	{
		return $this->belongsTo('User');
	}

}