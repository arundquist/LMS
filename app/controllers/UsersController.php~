<?php

class UsersController extends \BaseController {

	public function __construct()
	    {
		$this->beforeFilter('auth', array('except' =>['postLogin', 'getLogin']));
		
	
		
	    }
	
	/**
	 * Display a listing of the resource.
	 * GET /users
	 *
	 * @return Response
	 */
	
	 public function getTest()
	 {
	 	 return "hi there";
	 }
	 
	 public function getLogin()
	 {
	 	 return View::make('user.login');
	 }
	 
	 public function postLogin()
	 {
	 	 if (Auth::attempt(['username'=>Input::get('username'), 'password'=>Input::get('password')]))
	 	 {
	 	 	 return Redirect::intended(action('UsersController@getDashboard'));
	 	 };
	 	 
	 }
	 
	 public function getLogout()
	 {
	 	 Auth::logout();
	 	 return Redirect::to(action('UsersController@getLogin'));
	 }
	 
	 public function getDashboard()
	 {
	 	 $user=Auth::user();
	 	 return View::make('user.dashboard', compact('user'));
	 }
}