<?php

class AlgorithmsController extends \BaseController {

	/**
	 * Display a listing of algorithms
	 *
	 * @return Response
	 */
	public function index()
	{
		$algorithms = Algorithm::all();

		return View::make('algorithms.index', compact('algorithms'));
	}

	/**
	 * Show the form for creating a new algorithm
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('algorithms.create');
	}

	/**
	 * Store a newly created algorithm in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Algorithm::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Algorithm::create($data);

		return Redirect::route('algorithms.index');
	}

	/**
	 * Display the specified algorithm.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$algorithm = Algorithm::findOrFail($id);

		return View::make('algorithms.show', compact('algorithm'));
	}

	/**
	 * Show the form for editing the specified algorithm.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$algorithm = Algorithm::find($id);

		return View::make('algorithms.edit', compact('algorithm'));
	}

	/**
	 * Update the specified algorithm in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$algorithm = Algorithm::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Algorithm::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$algorithm->update($data);

		return Redirect::route('algorithms.index');
	}

	/**
	 * Remove the specified algorithm from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Algorithm::destroy($id);

		return Redirect::route('algorithms.index');
	}

}
