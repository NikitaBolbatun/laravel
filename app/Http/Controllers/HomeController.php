<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
     * @return Renderable
     */
    public function index()
    {
        return view('home');
    }


    public function update($id, Request $req)
    {

        $valid = Validator::make($req->all(),[
            'name' => ['required', 'string', 'max:255','regex:/^\S*$/','alpha'],
            'surname' => ['required', 'string','regex:/^\S*$/','alpha','max:255',],
            'organization' => ['required','string', 'max:255',],
            'phone' => ['required', 'integer','unique:users', 'digits_between:6,15'],
            'password' => ['required', 'string',  'confirmed'],
        ]);
        if ($valid->fails()) {
            return redirect()->route('home')
                ->withErrors($valid)
                ->withInput();
        }
        $org = DB::table('organization')->where('name',$req['organization'])->first();
        if (!$org) {
            DB::table('organization')->insert(
                array('name' => $req['organization'])
            );
        }
        $user = User::find($id);
        $user->name = $req->input('name');
        $user->surname = $req->input('surname');
        $user->organization = $req->input('organization');
        $user->phone = $req->input('phone');
        $user->password = $req->input('password');


        $user -> save();
        return redirect()->route('home')
            ->with('success','Профиль успешно обновлен');
    }


}
