<?php

class Student extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];
	
	
	// I've changed the schema so that now users own the comments
	/* public function comments()
	{
		return $this->hasMany('Comment');
	} */
	
	public function files()
	{
		return $this->hasMany('File');
	}
	
	public function scores()
	{
		return $this->hasMany('Score');
	}
	
	public function courses()
	{
		return $this->belongsToMany('Course');
	}
	
	public function summaries()
	{
		return $this->hasMany('Summary');
	}
	
	public function user()
	{
		return $this->morphOne('User', 'userable');
	}
	
	public function getTestAttribute($x)
	{
		return $x;
	}
	
	public function getTest2Attribute()
	{
		return "yep this works";
	}
	
	public function getTotalsnewAttribute($course_id)
	{
		$course=Course::findOrFail($course_id);
		$assids=$course->assignments->lists('id');
		$s=array();
		
		// I need to make changes here so that it grabs the most
		// recent numeric score if it exists
		// One (slow) idea is to grab the most recent
		// score for each assignment separately
		// and set the $s variable that way
		
		foreach ($assids AS $assid)
		{
			$recentscore=$this->scores()
					->whereRaw("score REGEXP '^[0-9\.]+$'")
					->where("assignment_id", $assid)
					->orderBy("updated_at", 'DESC')
					->first();
			if (count($recentscore) > 0)
				$s[$assid]=$recentscore->score;
		};
		
		// this next commented out line gets one score
		// for each assignment but it's not clear if it's the 
		// most recent and not clear if it's numeric
		//$s=$this->scores()->whereIn('assignment_id', $assids)->lists("score", "assignment_id");
		$sactual=$s;
		
		$testlist=$course->assignments;
		$t=array();
		$totals=array();
		foreach ($testlist AS $assignment)
		{
			$t[$assignment->id]=$assignment->total;
			// this is a laravel helper that adds
			// the key if it doesn't exist
			$s=array_add($s, $assignment->id, 0);
			$sactual=array_add($sactual, $assignment->id, null);
			
		};
		// so now s and t are populated
		
		// here's a different way with the t array being a set of arrays
		$types=$course->types;
		$t2=array();
		foreach ($types AS $type)
		{
			foreach ($type->assignments AS $assignment)
			{
				$t2[$type->id][$assignment->id]=$assignment->total;
			};
		};
		
		foreach ($types AS $type)
		{
			switch($type->algorithm)
			{
			 case 'percent':
				$ttotals=$type->assignments()->lists('total', 'id');
				$ssum=array_sum(array_intersect_key($s,$ttotals));
				$tsum=array_sum($ttotals);
				$totals[$type->id]=$ssum/$tsum*100;
				break;
			 case '':
			 	 $totals[$type->id]=0;
			 	 break;
			 default:
				$string=preg_replace("/(s|t|totals)\[([0-9]+)\]/", "\$\\1[\\2]", $type->algorithm);
				$alg="\$totals[$type->id]=".$string.";";
				$justtesting=8;
				eval($alg);
			};
		};
		$bigfull=preg_replace("/(s|t|totals)\[([0-9]+)\]/", "\$\\1[\\2]", $course->algorithm->algorithm);
		$bigfull="\$totals[-1]=$bigfull;";
		eval($bigfull);
		//I'm passing $sactual so that missing scores are missing instead of zero
		$wholething=['s'=>$sactual, 't'=>$t, 'totals'=>$totals, 't2'=>$t2];
		return $wholething;
	}
	
	public function getTotalsAttribute()
	{
		$s=array();
		// this next line has a problem since it gets
		// more than just this course with the schema change
		$s=$this->scores()->lists("score", "assignment_id");
		$sactual=$s;
		
		$testlist=$this->course->assignments;
		$t=array();
		$totals=array();
		foreach ($testlist AS $assignment)
		{
			$t[$assignment->id]=$assignment->total;
			// this is a laravel helper that adds
			// the key if it doesn't exist
			$s=array_add($s, $assignment->id, 0);
			
		};
		// so now s and t are populated
		
		// here's a different way with the t array being a set of arrays
		$types=$this->course->types;
		$t2=array();
		foreach ($types AS $type)
		{
			foreach ($type->assignments AS $assignment)
			{
				$t2[$type->id][$assignment->id]=$assignment->total;
			};
		};
		
		foreach ($this->course->types AS $type)
		{
			switch($type->algorithm)
			{
			 case 'percent':
				$ttotals=$type->assignments()->lists('total', 'id');
				$ssum=array_sum(array_intersect_key($s,$ttotals));
				$tsum=array_sum($ttotals);
				$totals[$type->id]=$ssum/$tsum*100;
				break;
			 case '':
			 	 $totals[$type->id]=0;
			 	 break;
			 default:
				$string=preg_replace("/(s|t|totals)\[([0-9]+)\]/", "\$\\1[\\2]", $type->algorithm);
				$alg="\$totals[$type->id]=".$string.";";
				$justtesting=8;
				eval($alg);
			};
		};
		$bigfull=preg_replace("/(s|t|totals)\[([0-9]+)\]/", "\$\\1[\\2]", $this->course->algorithm->algorithm);
		$bigfull="\$totals[-1]=$bigfull;";
		eval($bigfull);
		//I'm passing $sactual so that missing scores are missing instead of zero
		$wholething=['s'=>$sactual, 't'=>$t, 'totals'=>$totals, 't2'=>$t2];
		return $wholething;
	}
	
	protected static function boot() {
		parent::boot();
	
		static::deleting(function($student) { // before delete() method call this
		     $student->scores()->delete();
		     // do the rest of the cleanup...
		});
	    }

}