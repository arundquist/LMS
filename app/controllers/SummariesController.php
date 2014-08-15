<?php

class SummariesController extends \BaseController {

	/**
	 * Display a listing of summaries
	 *
	 * @return Response
	 */
	public function index()
	{
		$summaries = Summary::all();

		return View::make('summaries.index', compact('summaries'));
	}

	/**
	 * Show the form for creating a new summary
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('summaries.create');
	}

	/**
	 * Store a newly created summary in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Summary::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Summary::create($data);

		return Redirect::route('summaries.index');
	}

	/**
	 * Display the specified summary.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$summary = Summary::findOrFail($id);

		return View::make('summaries.show', compact('summary'));
	}

	/**
	 * Show the form for editing the specified summary.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$summary = Summary::find($id);

		return View::make('summaries.edit', compact('summary'));
	}

	/**
	 * Update the specified summary in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$summary = Summary::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Summary::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$summary->update($data);

		return Redirect::route('summaries.index');
	}

	/**
	 * Remove the specified summary from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Summary::destroy($id);

		return Redirect::route('summaries.index');
	}

}
