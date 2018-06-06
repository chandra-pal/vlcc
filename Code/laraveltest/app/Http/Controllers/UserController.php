<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class UserController extends Controller
{
    //
	Public function showMessage()
		{
			return view('greetings',['name' => 'James']);
		}
}
