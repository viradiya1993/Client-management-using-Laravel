<?php

namespace App\Http\Controllers\Auth;

use App\GlobalSetting;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\FrontBaseController;
use App\User;
use Carbon\Carbon;
use Froiden\Envato\Traits\AppBoot;
use GuzzleHttp\Client;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends FrontBaseController
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

    use AuthenticatesUsers, AppBoot;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest', ['except' => 'logout']);
    }

    public function showLoginForm()
    {
        if (!$this->isLegal()) {
            return redirect('verify-purchase');
        }

        $this->pageTitle = 'Login Page';
        // if ($this->setting->front_design == 1) {
        //     return view('saas.login', $this->data);
        // }


        return view('auth.login', $this->data);
    }

    protected function validateLogin(\Illuminate\Http\Request $request)
    {
        $setting = GlobalSetting::first();

        $rules = [
            $this->username() => 'required|string',
            'password' => 'required|string'
        ];

        // User type from email/username
        $user = User::where($this->username(), $request->{$this->username()})->first();

        if (!is_null($setting->google_recaptcha_key) && (is_null($user) || ($user && !$user->super_admin))) {
            $rules['g-recaptcha-response'] = 'required';
        }

        $this->validate($request, $rules);
    }

    public function googleRecaptchaMessage()
    {
        throw ValidationException::withMessages([
            'g-recaptcha-response' => [trans('auth.recaptchaFailed')],
        ]);
    }

    public function validateGoogleRecaptcha($googleRecaptchaResponse)
    {
        $setting = GlobalSetting::first();

        $client = new Client();
        $response = $client->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'form_params' =>
                [
                    'secret' => $setting->google_recaptcha_secret,
                    'response' => $googleRecaptchaResponse,
                    'remoteip' => $_SERVER['REMOTE_ADDR']
                ]
            ]
        );

        $body = json_decode((string) $response->getBody());

        return $body->success;
    }

    public function login(\Illuminate\Http\Request $request)
    {
        $setting = GlobalSetting::first();
        $this->validateLogin($request);

        // User type from email/username
        $user = User::where($this->username(), $request->{$this->username()})->first();

        // Check google recaptcha if setting is enabled
        if (!is_null($setting->google_recaptcha_key) && (is_null($user) || ($user && !$user->super_admin))) {
            // Checking is google recaptcha is valid
            $gRecaptchaResponseInput = 'g-recaptcha-response';
            $gRecaptchaResponse = $request->{$gRecaptchaResponseInput};
            $validateRecaptcha = $this->validateGoogleRecaptcha($gRecaptchaResponse);
            if (!$validateRecaptcha) {
                return $this->googleRecaptchaMessage();
            }
        }

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    protected function credentials(\Illuminate\Http\Request $request)
    {
        //return $request->only($this->username(), 'password');
        return [
            'email' => $request->{$this->username()},
            'password' => $request->password,
            'status' => 'active',
            'login' => 'enable'
        ];
    }

    protected function redirectTo()
    {
        $user = auth()->user();
        if ($user->super_admin == '1') {
            return 'super-admin/dashboard';
        } elseif ($user->hasRole('admin')) {
            $user->company()->update([
                'last_login' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
            return 'admin/dashboard';
        }

        if ($user->hasRole('employee')) {
            return 'member/dashboard';
        }

        if ($user->hasRole('client')) {
            return 'client/dashboard';
        }
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/login');
    }
}
