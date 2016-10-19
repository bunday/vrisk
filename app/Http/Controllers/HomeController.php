<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Input;
use App\Iso;
use DB;
use Excel;
use Likelihood;
use Impact;
use formular;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    //import excel to database
    public function importExcel()
    {
        if(Input::hasFile('iso')){
            $path = Input::file('iso')->getRealPath();
            $data = Excel::load($path, function($reader) {
            })->get();
            if(!empty($data) && $data->count()){
                foreach ($data as $key => $value) {
                    $insert[] = ['title' => $value->title, 'description' => $value->description];
                }
                if(!empty($insert)){
                    DB::table('isos')->insert($insert);
                    dd('Insert Record successfully.');
                }
            }
        }
        return redirect()->back();
    }

    public function management_scale()
    {
        $impact = new Impact();
        for ($i=1; $i < Input::get('impact'); $i++) {
            $impact->scale = Input::get('impact');
            $impact->value = $i; 
            $impact->isms_no = Input::get('isms_no');
            $impact->save();
        }
        $like = new Likelihood();
        for ($i=1; $i < Input::get('likelihood'); $i++) {
            $like->scale = Input::get('likelihood');
            $like->value = $i;
            $like->isms_no = Input::get('isms_no');
            $like->save();
        }
        $formular = new formular();
        $formular->value = Input::get('formular');
        $formular->isms_no = Input::get('isms_no');
        $formular->save();

        return redirect()->back();
    }
}
