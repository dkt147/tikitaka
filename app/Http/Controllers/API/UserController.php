<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\NewMemberMail;
use App\Mail\OtpMail;
use App\Mail\ResetPassword;
use App\Models\User;
use App\Models\Invites;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\sendEmail;
use App\Models\Box;
use App\Models\Category;
use App\Models\Items;
use App\Models\Plans;
// use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users', // Check for uniqueness in the 'users' table
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'false',
                'message' => $validator->errors()->first(), // Return the first validation error message
            ], 401);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        $token = $user->createToken('authToken')->plainTextToken;
        $user->api_token = $token;
        $user->save();
        return response()->json([
            'status' => 'true',
            'message' => 'You have signed up successfully.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'otp' => $user->otp,
                'api_token' => $token,
                'email_verified_at' => $user->email_verified_at
            ],
        ]);
    }

    public function userMobile(Request $request)
    {
        $request->validate([
            'mobile_no' => 'required',
        ]);
        $user = User::where('id', Auth::user()->id)->first();
        $user->mobile_no = $request->mobile_no;
        $user->save();
        return response()->json([
            'status' => 'true',
            'message' => 'Phone number has been updated successfully',
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(array("status" => false, 'errors' => $validator->errors()->first()));
        }
        $user = user::where('email', '=', $request->email)->first();
        $remember_me = $request->has('remember_me') ? true : false;
        if ($remember_me = true) {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember_me)) {
                $token = $user->createToken('authToken')->plainTextToken;
            } else {
                return response()->json(['status' => 'false', 'message' => 'Email or password is incorrect']);
            }
        } else {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $token = $user->createToken('authToken')->plainTextToken;
            } else {
                return response()->json(['status' => 'false', 'message' => 'Email or password is incorrect']);
            }
        }
        if (empty($user)) {
            return response()->json([
                'status' => 'false',
                'message' => 'Email or password is incorrect',
            ]);
        }

        $user->save();


        return response()->json([
            'status' => 'true',
            'message' => 'You have logged in successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'api_token' => $token,
                'email_verified_at' => $user->email_verified_at,
            ],
        ]);

    }

    public function emailOTP(Request $request)
    {
        $user = User::where('id', Auth::user()->id)->first();
        $otp = random_int(1000, 9999);
        $user->otp = $otp;
        $user->save();
        if ($user) {
        }
        if (!empty($user)) {
            Mail::to($user->email)->send(new OtpMail($user));
            return response(["status" => 'true', "message" => "OTP was sent successfully", 'data' => ['otp' => $otp]]);
        } else {
            return response()->json([
                'status' => 'false',
                'message' => 'Please enter a valid email address',

            ]);
        }
    }

    public function sendOTP(Request $request)
    {
        $request->validate([
            'email' => 'required',
        ]);
        $user = user::where('email',  $request->email)->first();
        $otp = random_int(1000, 9999);
        $user->otp = $otp;
        $user->save();
        if (!empty($user)) {
            Mail::to($request->email)->send(new ResetPassword($user));
            return response(["status" => 'true', "message" => "OTP was sent successfully", 'data' => ['otp' => $otp]]);
        } else {
            return response()->json([
                'status' => 'false',
                'message' => 'Please enter a valid email address',
            ]);
        }
    }

    public function verifyForgot(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'otp' => 'required',
        ]);
        $user = user::where('email', $request->email)->first();
        $lastUpdate = Carbon::parse($user->updated_at);
        $now = Carbon::now();
        if ($now->diffInSeconds($lastUpdate) > 60000) {
            $otp = random_int(1000, 9999);
            $user->otp = $otp;
            $user->save();
            Mail::to($user->email)->send(new OtpMail($user));
            return response()->json(['error' => 'You have entered an expired OTP']);
        }
        if ($user->otp == $request->otp) {
            $user->otp = null;
            return response()->json([
                'status' => 'true',
                'message' => 'Otp verified successfully',
            ]);
        } else {
            return response()->json([
                'status' => 'false',
                'message' => 'Please enter valid otp'
            ]);
        }
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'otp' => 'required',
        ]);
        $user = user::where('id', Auth::user()->id)->first();
        if (empty($user)) {
            return response()->json([
                'status' => 'false',
                'message' => 'Please enter a valid email address.'
            ]);
        } else {
            $lastUpdate = Carbon::parse($user->updated_at);
            $now = Carbon::now();
            if ($now->diffInSeconds($lastUpdate) > 60000) {
                $otp = random_int(1000, 9999);
                $user->otp = $otp;
                $user->save();
                Mail::to($user->email)->send(new OtpMail($user));
                return response()->json(['error' => 'You have entered an expired OTP']);
            } else {
                if ($user->otp == $request->otp) {
                    $user->email_verified_at = $now;
                    $user->otp = null;
                    $user->save();
                    return response()->json([
                        'status' => 'true',
                        'message' => 'Otp verified successfully',
                        'data' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'otp' => $user->otp,
                            'mobile_no' => $user->mobile_no,
                            'api_token' => $user->api_token,
                            'device_token' => $user->device_token,
                            'email_verified_at' => $user->email_verified_at
                        ],
                    ]);
                } else {
                    return response()->json([
                        'status' => 'false',
                        'message' => 'Please enter valid otp'
                    ]);
                }
            }
        }
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'new_password' => 'required|min:6',
        ]);
        $user = user::where('email', $request->email)->first();
        if (!empty($user)) {
            $user->password = Hash::make($request->new_password);
            $user->save();
            return response()->json([
                'status' => 'true',
                'message' => 'Password Updated',
            ]);
        } else {
            return response()->json(['status' => 'false', 'message' => 'user not found.']);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 'true', 'message' => 'You have been logged out successfully']);
    }
    public function delete(Request $request)
    {
        $user = user::find($request->id);
        if (empty($user)) {
            return response()->json(['status' => 'false', 'message' => 'User not found']);
        }
        $user->delete();
        return response()->json(['status' => 'true', 'message' => 'User Deleted']);
    }

    public function addMember(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required',
        ]);
        $invitee = Invites::where('invitee_email', $request->email)->first();
        if (!empty($invitee)) {
            return response()->json(['status' => 'false', 'message' => 'Invitation already send']);
        }
        $user = User::where('email', $request->email)->first();
        if (!empty($user)) {
            return response()->json(['status' => 'false', 'message' => 'User already exist']);
        }
        $invitee = new Invites();
        $invitee->invitee_email = $request->email;
        $invitee->invitee_name = $request->name;
        $user = User::find(Auth::user()->id);
        $user->userMember()->save($invitee);
        Mail::to($request->email)->send(new NewMemberMail($invitee));
        return response()->json(['status' => 'true', 'message' => 'Invitation Send']);
    }

    public function test()
    {
        if (Auth::check()) {
            dd('logged in');
        } else {
            dd('logged out');
        }
    }

    public function lov(Request $request)
    {
        $itemName = [];
        $itemCategory = [];
        $itemName = $request->item_name;
        $itemCategory  = $request->cat_name;
        $start_date = $request->start_date;
        $end_date = $request->end_date;


        if (!in_array(null, $itemName) && !in_array(null, $itemCategory)) {
            $boxes = Box::where('user_id', Auth::user()->id)
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    if ($end_date) {
                        return $query->whereBetween('created_at', [$start_date, $end_date]);
                    } else {
                        return $query->where('created_at', '>=', $start_date);
                    }
                })
                ->whereHas('boxItem', function ($query) use ($itemName, $itemCategory) {
                    $query->whereIn('item_name', $itemName)
                        ->whereHas('itemCategory', function ($query) use ($itemCategory) {
                            $query->where('category_name', $itemCategory);
                        });
                })
                ->get();
            return response()->json(['status' => 'true', 'message' => 'Data found', 'Data' => $boxes]);
        }

        if (in_array(null, $itemCategory) && !in_array(null, $itemName)) {
            $boxes = Box::where('user_id', Auth::user()->id)
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    if ($end_date) {
                        return $query->whereBetween('created_at', [$start_date, $end_date]);
                    } else {
                        return $query->where('created_at', '>=', $start_date);
                    }
                })
                ->whereHas('boxItem', function ($query) use ($itemName) {
                    $query->whereIn('item_name', $itemName);
                })
                ->get();
            return response()->json(['status' => 'true', 'message' => 'Data found', 'Data' => $boxes]);
        }

        if (in_array(null, $itemName) && !in_array(null, $itemCategory)) {
            $boxes = Box::where('user_id', Auth::user()->id)
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    if ($end_date) {
                        return $query->whereBetween('created_at', [$start_date, $end_date]);
                    } else {
                        return $query->where('created_at', '>=', $start_date);
                    }
                })
                ->whereHas('boxItem', function ($query) use ($itemName, $itemCategory) {
                    $query->whereHas('itemCategory', function ($query) use ($itemCategory) {
                        $query->where('category_name', $itemCategory);
                    });
                })
                ->get();
            return response()->json(['status' => 'true', 'message' => 'Data found', 'Data' => $boxes]);
        }

        $boxes = Box::where('user_id', Auth::user()->id)
            ->when($start_date, function ($query) use ($start_date, $end_date) {
                if ($end_date) {
                    return $query->whereBetween('created_at', [$start_date, $end_date]);
                } else {
                    return $query->where('created_at', '>=', $start_date);
                }
            })
            ->whereHas('boxItem', function ($query) use ($itemCategory) {
                $query->whereHas('itemCategory', function ($query) use ($itemCategory) {
                    $query->where('category_name', $itemCategory);
                });
            })
            ->get();
        return response()->json(['status' => 'true', 'message' => 'Data found', 'Data' => $boxes]);
    }
}
