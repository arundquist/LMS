<?php

class ExtrasController extends \BaseController {

	public function __construct()
	{
		$this->beforeFilter('authFaculty', ['only'=>['create', 'store', 'edit', 'update', 'destroy']]);
	}
	
	/**
	 * Display a listing of extras
	 *
	 * @return Response
	 */
	public function index()
	{
		$extras = Extra::all();

		return View::make('extras.index', compact('extras'));
	}

	/**
	 * Show the form for creating a new extra
	 *
	 * @return Response
	 */
	public function create()
	{
		$fac=Faculty::find(Auth::user()->userable_id);
		$courses=$fac->courses;
		
		return View::make('extras.create',
			['courses'=>$courses]);
	}

	/**
	 * Store a newly created extra in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Extra::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		//Extra::create($data);
		$extra=new Extra;
		$extra->content=Input::get('content');
		$extra->save();
		$extra->assignments()->sync(Input::get('asses'));
		//return "made it here";
		

		return Redirect::route('extras.edit',[$extra->id]);
	}

	/**
	 * Display the specified extra.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$extra = Extra::findOrFail($id);

		return View::make('extras.show', compact('extra'));
	}

	/**
	 * Show the form for editing the specified extra.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$extra = Extra::find($id);
		$fac=Faculty::find(Auth::user()->userable_id);
		$courses=$fac->courses;

		return View::make('extras.edit', 
			['extra'=>$extra,
			'extralist'=>$extra->assignments->lists('id'),
			'courses'=>$courses]);
	}

	/**
	 * Update the specified extra in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$extra = Extra::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Extra::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		//$extra->update($data);
		$extra->content=Input::get('content');
		$extra->save();
		$extra->assignments()->sync(Input::get('asses'));

		return Redirect::route('extras.edit', [$extra->id]);
	}

	/**
	 * Remove the specified extra from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Extra::destroy($id);

		return Redirect::route('extras.index');
	}

}
