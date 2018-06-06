<?php

namespace App\Http\Controllers;

use DB;
//use Form;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;


class TaskController extends Controller
{
    public function index()
	{
		$users = DB::table('users')->get();
		return view('user.index',['users'=> $users]);
	}
	
	/*public function create()
	{
		
	}*/
	public function store(Request $request)
	{
		
		$name= $request->input('name');
		$email = $request->input('email');
		$password = $request->input('password');
		$user = new user();
		$user->name=$name;
		$user->email = $email;
		$user->password = $password;
		$user->save();
		echo "xyz";
	}
}
