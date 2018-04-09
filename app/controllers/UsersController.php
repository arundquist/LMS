<?php

class UsersController extends \BaseController {

	public function __construct()
	    {
		$this->beforeFilter('auth', array('except' =>['postLogin', 'getLogin', 'getChangepassword',
			'postChangepassword']));



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
		 dd("made it here just inside postLogin");
		 if (Auth::attempt(['username'=>Input::get('username'), 'password'=>Input::get('password')]))
	 	 {
			 dd("made it here");
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

	 public function getChangepassword()
	 {
	 	 $user=Auth::user();
	 	 return View::make('user.changepassword', compact('user'));
	 }

	 public function postChangepassword()
	 {
	 	 if (Auth::guest())
	 	 	 return Redirect::to(action('UsersController@getLogin'));
	 	 $user=Auth::user();
	 	 if ((Input::get('newpassword')==Input::get('newpassword2'))
	 	 	 && (Input::get('newpassword') != $user->username))
	 	 {
	 	 	 $user->password=Hash::make(Input::get('newpassword'));
	 	 	 $user->save();
	 	 	 return Redirect::to(action('UsersController@getDashboard'));
	 	 } else
	 	 {
	 	 	 return Redirect::back();
	 	 };
	 }

	 public function getResetpassword()
	 {
	 	 $user=Auth::user();
	 	 $user->password=Hash::make($user->username);
	 	 $user->save();
	 	 return Redirect::to(action('UsersController@getDashboard'));
	 }

}
