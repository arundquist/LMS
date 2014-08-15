<?php

class FacultiesController extends \BaseController {

	/**
	 * Display a listing of faculties
	 *
	 * @return Response
	 */
	public function index()
	{
		$faculties = Faculty::all();

		return View::make('faculties.index', compact('faculties'));
	}

	/**
	 * Show the form for creating a new faculty
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('faculties.create');
	}

	/**
	 * Store a newly created faculty in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Faculty::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Faculty::create($data);

		return Redirect::route('faculties.index');
	}

	/**
	 * Display the specified faculty.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$faculty = Faculty::findOrFail($id);

		return View::make('faculties.show', compact('faculty'));
	}

	/**
	 * Show the form for editing the specified faculty.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$faculty = Faculty::find($id);

		return View::make('faculties.edit', compact('faculty'));
	}

	/**
	 * Update the specified faculty in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$faculty = Faculty::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Faculty::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$faculty->update($data);

		return Redirect::route('faculties.index');
	}

	/**
	 * Remove the specified faculty from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Faculty::destroy($id);

		return Redirect::route('faculties.index');
	}

}
