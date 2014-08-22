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
	
	public function teams()
	{
		return $this->hasMany('Team');
	}
	
	public function dates()
	{
		return $this->hasMany('Date')->orderBy('date', 'ASC');
	}
	
	public function students()
	{
		return $this->belongsToMany('Student')
			->orderBy('name', 'ASC');
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
		return link_to_route('syllabus.show',"{$this->classname} ({$this->semester} {$this->year})", [$this->id]);
	}
	
	public function getGoogleAttribute()
	{
		$use=preg_match('/^google\(([^\)]+)\)$/', $this->syllabus, $matches);
		if ($use==1)
		{
			$all=file_get_contents("https://docs.google.com/document/d/$matches[1]/pub?embedded=true");
			preg_match('~(<style.*?</style>).*<body[^>]*?>(.*?)</body>~', $all, $matches2);
			return $matches2;
		}
		else
		{
			
			return [$this->syllabus];
		};
	}
	
	public function getClassemailAttribute()
	{
		$studentemails=$this->students()->lists('email');
		$arg="arundquist@hamline.edu?bcc=";
		$arg.=implode(',',$studentemails);
		return HTML::mailto($arg,'email class');
	}

}