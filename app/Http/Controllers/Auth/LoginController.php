<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers {
        logout as performLogout;
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    public function login(request $req)
    {
        $input= $req->all();

        $this->validate($req,[
            'email'=>'required|email',
            'password'=>'required',
        ]);

        if(auth()->attempt(array('email'=>$input['email'],'password'=>$input['password'])))
        {
            if(auth()->user()->role == "admin")
        {
            return redirect()->route('admin.Dashboard');
        }
        else{
            abort(403);
        }

        }
        else{
            return back()->with('error','email id or password is wrong');
        }
    }

    public function logout(Request $request)
{
    $this->performLogout($request);
    return redirect()->route('admin.Dashboard');
}
}
