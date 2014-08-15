<?php

class LinksController extends \BaseController {

	/**
	 * Display a listing of links
	 *
	 * @return Response
	 */
	public function index()
	{
		$links = Link::all();

		return View::make('links.index', compact('links'));
	}

	/**
	 * Show the form for creating a new link
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('links.create');
	}

	/**
	 * Store a newly created link in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Link::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Link::create($data);

		return Redirect::route('links.index');
	}

	/**
	 * Display the specified link.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$link = Link::findOrFail($id);

		return View::make('links.show', compact('link'));
	}

	/**
	 * Show the form for editing the specified link.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$link = Link::find($id);

		return View::make('links.edit', compact('link'));
	}

	/**
	 * Update the specified link in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$link = Link::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Link::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$link->update($data);

		return Redirect::route('links.index');
	}

	/**
	 * Remove the specified link from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Link::destroy($id);

		return Redirect::route('links.index');
	}

}
