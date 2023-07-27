<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserValidationRequest;
use App\Http\Requests\ForgotValidationRequest;
use App\Http\Requests\LoginValidationRequest;
use App\Http\Requests\ResetValidationRequest;
use App\Jobs\sendEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
//use sirajcse\UniqueIdGenerator\UniqueIdGenerator;

class AuthController extends Controller
{
    /**
     * Login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(LoginValidationRequest $request)
    {

        $user = $this->getUser($request);

        if ($user == null) {
            return response()->json(['error' => 'User not found'], 400);
        }

        if ($user->confirmed != 1){
            return response()->json(['error' => 'User not yet confirmed'], 400);
        }
        if (!Hash::check($request->password, $user->password)) {
           
            return response()->json(['error' => 'Password is invalid'], 400);

        } 
              
    
        else {
             // Generate OTP
         $otp = $this->generateOTP();
    
         // Insert OTP in Password Reset Dashbard
         $this->insertOTPPasswordReset($user->email, $otp);

         // SendOTP Detials
         $email_template_id = 'verify';
         $this->sendMail($user, $email_template_id, $otp);
         return response()->json(
            [
            'message' => "OTP Code Generated Successfully",
            ], 200);   
        }
    }

    public function register(CreateUserValidationRequest $request)
    {
        try {

            // Create User
            $input = $request->validated();
            //$id = UniqueIdGenerator::generate(['table' => 'users', 'length' => 6, 'prefix' => date('y')]);
            //$input['id'] = $id;
            $input['password'] = bcrypt($input['password']);
            $input['status'] = 1;
            $input['confirmed'] = 0;
            
            $user = $this->getUser($request);
        //    return $user;
            if ($user){
                return response()->json(['messgae' => "User already Exists"], 400);
            }else{
                $user = User::create($input);
                
                // Generate OTP
                $otp = $this->generateOTP();
    
                // Insert OTP in Password Reset Dashbard
                $this->insertOTPPasswordReset($user->email, $otp);
    
                // SendOTP Detials
                $email_template_id = 'verify';
                $this->sendMail($user, $email_template_id, $otp);
                return response()->json(
                    [
                    'message' => "User created Successfully",
                    ], 200);
            }
            
            
        } catch (\Exception$exception) {

            return response([

                'message' => $exception->getMessage(),
            ], 400);
        }

    }

    public function forgot(ForgotValidationRequest $request)
    {

        $email = $this->getUser($request)->email ?? null;

        if ($email == null) {
            return response([
                'message' => 'User does not exist',
            ], 400);
        }

        try {

            // Insert token generated to database
            $otp = $this->generateOTP();
            $this->insertOTPPasswordReset($email, $otp);

            $email_template_id = 'forgot';
            $user = User::where('email', $email)->first();

            $this->sendMail($user, $email_template_id, $otp);

            return response([

                'message' => "success",
            ], 200);

        } catch (\Exception$exception) {

            return response([

                'message' => $exception->getMessage(),
            ], 400);
        }

    }

    public function checkOTP(Request $request)
    {
        $token = $request->token;
        $passwordReset = DB::table('password_reset_tokens')->where('token', $token)->first();

        if ($passwordReset == null) {
            return response([
                'message' => 'Invalid OTP',
            ], 400);
        }
        $user = $this->getUser($request);
       
        if ($user->confirmed == 0){
            if ($user){
                $user->update(['confirmed' => 1]);
            }
            // Send Register Message
             $email_template_id = 'register';
             $otp = null;
            $this->sendMail($user, $email_template_id, $otp);
            DB::table('password_reset_tokens')->where('token', $token)->delete();

            return response([
                'message' => 'OTP has been confirmed Successfully',
            ], 200);
        }
        
        else{
            DB::table('password_reset_tokens')->where('token', $token)->delete();
            return response()->json(['token' => $this->generateUserAccessToken($user),
            'data' => $user,
        ], 200);

        }
        
        
        
       

       
    }

    public function reset(ResetValidationRequest $request)
    {

        $user = User::where('email', $request->email)->first();

        $user->password = Hash::make($request->password);

        return response([

            'message' => $user->save(),
        ]);

    }

    protected function getUser(Request $request)
    {
        // dd($request->file('photo'));

        $search_email= $request->user_details ?? $request->email;
        $search_username= ($request->user_details ?? $request->username) ?? $search_email;

      // return  $search_email . " -" . $search_username;

          return User::where(function($query) use ($search_email, $search_username) {
            $query->where('email', $search_email)
                ->orWhere('username', $search_username);
        })->first();
    }

    private function generateUserAccessToken($user)
    {

        return $user->createToken('MyApp')->accessToken; // Generate API Token
    }

    private function generateOTP()
    {

        return rand(100000, 999999);
    }

    private function sendMail($user, $email_template_id, $otp)
    {
        Log::info('Send Mail:' . $user . " - " . $email_template_id . " - " . $otp);
        return sendEmail::dispatch($user, $email_template_id, $otp)->onQueue('sync');
    }

    private function insertOTPPasswordReset($email, $token)
    {
        DB::table('password_reset_tokens')->insert([

            'email' => $email,
            'token' => $token,

        ]);
    }

    public function testmail(User $user)
    {
        $email_template_id = 'register';
        $otp = '54554545';
        $this->sendMail($user, $email_template_id, $otp);
    }

    public function resendOTP(Request $request)
    {
        $user = $this->getUser($request);
        // Generate OTP
        $otp = $this->generateOTP();

        // Insert OTP in Password Reset Dashbard
        $this->insertOTPPasswordReset($user->email, $otp);

        $email_template_id = 'verify';
        $this->sendMail($user, $email_template_id, $otp);
    }
}