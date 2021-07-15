<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\SocialProfile;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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

    use AuthenticatesUsers;

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

    public function redirectToProvider($provider){

        $providers = ['facebook', 'google'];

        if(in_array($provider, $providers)){
            return Socialite::driver($provider)->redirect();
        }
        else{
            return redirect()->route('login')->with('info', $provider . ' no es una aplicación válida para loguearse.');
        }

        
    }

    public function handlerProviderCallback(Request $request, $provider){

        if($request->get('error')){
            return redirect()->route('login');
        }

        $userSocialite = Socialite::driver($provider)->user();

        $social_profile = SocialProfile::where('social_id',$userSocialite->getId())
                                        ->where('social_name',$provider)
                                        ->first();


        if(!$social_profile){
            
            $user = User::where('email',$userSocialite->getEmail())->first();
            
            if($user && $user->password !== NULL){
                return redirect()->route('login')->with('info', 'El usuario con correo '. $userSocialite->getEmail() . ' ya esta registrado, por favor ingrese con su email y password.');
            }

            if(!$user){
                
                $user = User::create([
                    'name' => $userSocialite->getName(),
                    'email' => $userSocialite->getEmail(),
                ]);
                       
            }

            $social_profile = SocialProfile::create([
                'user_id' => $user->id,
                'social_id' => $userSocialite->getId(),
                'social_name' => $provider,
                'social_avatar' => $userSocialite->getAvatar(),
            ]);

        }
        
        auth()->login($social_profile->user);

        return redirect()->route('home');
    }
}
