<?php
namespace App\Http\Controllers;

class DashboardController extends Controller
{
    
    public function __construct()
    {
       // parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('welcome');
    }
    
    public function termsConditions()
    {
        return view('term-condition-home');
    }
    
    public function termsConditionsIndia()
    {
        return view('term-condition-india');
    }
    
    public function termsConditionsBahrain()
    {
        return view('term-conditon-bahrain');
    }
    
    public function termsConditionsKuwait()
    {
        return view('term-conditon-kuwait');
    }
    
    public function termsConditionsOman()
    {
        return view('term-conditon-oman');
    }
    
    public function termsConditionsQatar()
    {
        return view('term-conditon-qatar');
    }
    
    public function termsConditionsUAE()
    {
        return view('term-conditon-uae');
    }
}
