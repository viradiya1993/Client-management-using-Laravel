<?php

namespace App\Http\Controllers\Front;

use App\Company;
use App\Currency;
use App\EmployeeDetails;
use App\Helper\Reply;
use App\Http\Requests\Front\Register\StoreRequest;
use App\Notifications\EmailVerification;
use App\Notifications\EmailVerificationSuccess;
use App\Notifications\NewCompanyRegister;
use App\Role;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\GlobalSetting;

class RegisterController extends FrontBaseController
{
    public function index() {
        $this->pageTitle = 'Sign Up';

        if($this->setting->front_design == 1){
            return view('saas.register', $this->data);
        }
        return view('front.register', $this->data);

    }

    public function store(StoreRequest $request) {

        if(!is_null($this->global->google_recaptcha_key))
        {
            $gRecaptchaResponseInput = 'g-recaptcha-response';
            $gRecaptchaResponse = $request->{$gRecaptchaResponseInput};
            $validateRecaptcha = $this->validateGoogleRecaptcha($gRecaptchaResponse);
            if(!$validateRecaptcha)
            {
                return Reply::error('Recaptcha not validated.');
            }
        }

        DB::beginTransaction();
        // Save company name
        $globalSetting = GlobalSetting::first();

        $company = new Company();
        $company->company_name = $request->company_name;
        $company->company_email = $request->email;
        $company->timezone = $globalSetting->timezone;
        $company->save();

        $currency = Currency::where('company_id', $company->id)->first();
        $company->currency_id = $currency->id;
        $company->save();

        // Save Admin
        $user = new User();
        $user->company_id = $company->id;
        $user->name       = 'admin';
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->status = 'active';
        $user->email_verification_code = str_random(40);
        $user->save();

        $employee = new EmployeeDetails();
        $employee->user_id = $user->id;
        $employee->employee_id = 'emp-'.$user->id;
        $employee->company_id = $user->company_id;
        $employee->address = 'address';
        $employee->hourly_rate = '50';
        $employee->save();

        if($globalSetting->email_verification == 1) {
            $user->notify(new EmailVerification($user));
            $user->status = 'deactive';
            $user->save();
            $message =  __('messages.signUpThankYouVerify');
        } else {
            $adminRole = Role::where('name', 'admin')->where('company_id', $user->company_id)->first();
            $user->roles()->attach($adminRole->id);

            $employeeRole = Role::where('name', 'employee')->where('company_id', $user->company_id)->first();
            $user->roles()->attach($employeeRole->id);
            $message = __('messages.signUpThankYou').' <a href="'.route('login').'">Login Now</a>.';
        }

        DB::commit();

        return Reply::success($message);
    }

    public function validateGoogleRecaptcha($googleRecaptchaResponse)
    {
        $client = new Client();
        $response = $client->post(
            'https://www.google.com/recaptcha/api/siteverify',
            ['form_params'=>
                [
                    'secret'=> $this->global->google_recaptcha_secret,
                    'response'=> $googleRecaptchaResponse,
                    'remoteip' => $_SERVER['REMOTE_ADDR']
                ]
            ]
        );

        $body = json_decode((string)$response->getBody());

        return $body->success;
    }

    public function getEmailVerification($code)
    {
        $this->pageTitle = __('modules.accountSettings.emailVerification');

        $user = User::where('email_verification_code', $code)->whereNotNull('email_verification_code')->withoutGlobalScope('active')->first();

        if($user) {
            $user->status = 'active';
            $user->email_verification_code = '';
            $user->save();

            $user->notify(new EmailVerificationSuccess($user));

            $adminRole = Role::where('name', 'admin')->where('company_id', $user->company_id)->first();
            $user->roles()->attach($adminRole->id);

            $employeeRole = Role::where('name', 'employee')->where('company_id', $user->company_id)->first();
            $user->roles()->attach($employeeRole->id);

            $this->messsage = 'Your have successfully verified your email address. You must click  <a href="'.route('login').'">Here</a> to login.';
            $this->class = 'success';
            return view('saas.email-verification', $this->data);


        } else {
            $this->messsage = 'Verification url doesn\'t exist. Click <a href="'.route('login').'">Here</a> to login.';
            $this->class = 'success';
            if($this->setting->front_design == 1){
                return view('saas.email-verification', $this->data);
            }
            return view('front.email-verification', $this->data);
        }

    }
}
