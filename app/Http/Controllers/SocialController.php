<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;
use App\Models\User;
use App\Models\Provider;
use Validator;
use Hash;
use GuzzleHttp\Exception\ClientException;



class SocialController extends Controller
{
    public function google_login(Request $request)
    {
        $valid = $request->validate([
                'token' => 'required',
                'device_token' => 'required'
            ]);
        
        
        // $token = "ya29.a0Ael9sCPsmArAjWgdwv35qaC91siUre8LbNqEJeT1QeVVVCIrpnEXXU8QK0YOiV11k5JPt_ga-wv9aoMP9Nsp1Tp3w1CPxukyGiLg0CzxHC8v2jihUZzSt2QtHCw8TS_p_4Mv44RqKW6LoZgAKdC03qrTowJiaCgYKAawSARASFQF4udJhyBnTGlFmCk2wnfW7cq-xgw0163";
        $token = $request->token;
        // $token = "ya29.a0Ael9sCNlhzMpgtbHKP5qsOmqgM-WgMee1KqGk1OlJufEhJU1G6w8XMVhdAouZSki05kEeT0ebSbsmi7nZ1GyUWclUk_pMzahaDbWi59qnSI9DdSDXubmH1aEYE8D3P8CrLRfX79MbIkPhg7mCAGNDV2HqVo9aCgYKATkSARMSFQF4udJhjddn7n-ot0z__pczS2yLGw0163";
        $user = Socialite::driver('google')->userFromToken($token);
        if(!$user) {
            return response()->json([
                        "message"   => "Invalid Token!",
                        "status"    => 401,
                    ]);
        }
        
        $data = $user->attributes;
        $providerId = $data['id'];
        $name = $data['name'];
        $email = $data['email'];
        $image = $data['avatar_original'];

        // check if email exists
        $checkIfUserExists = User::where('email',$email)->first();
        if(!$checkIfUserExists) {
            // means we dont have this user
            $explode_name = explode(" ",$name);
            $newuser =  User::create([
                'first_name' => $explode_name[0],
                'last_name' => $explode_name[1],
                'email' => $email,
                'password' => Hash::make(\Str::random(8)),
                'device_token' => $request->device_token
            ]);

            Provider::create([
                'user_id' => $newuser->id,
                'provider_id' => $providerId 
            ]);
            
            $token = $newuser->createToken($newuser->email . '_Token')->plainTextToken;
                
                return response()->json([
                        "message"   => "New User Created!",
                        "status"    => 201,
                        "token"     => $token,
                        "data"      => $newuser
                    ]);
            
        } else {
            //means we already have this user
            
            //update the device token
            $checkIfUserExists->update([
                    "device_token" => $request->device_token
                ]);
            
            $socialAccount = $checkIfUserExists->socialAccounts()->where('provider_id', $providerId)->first();
            //if social account is not linked to this email, then link it
            if(!$socialAccount) {
                $socialAccount = Provider::create([
                    'user_id'        => $checkIfUserExists->id,
                    'provider_id'    => $providerId
                ]);
            }
            
            $token = $checkIfUserExists->createToken($checkIfUserExists->email . '_Token')->plainTextToken;
             return response()->json([
                        "message"   => "Ok!",
                        "status"    => 200,
                        "token"     => $token,
                        "data"      => $checkIfUserExists
                    ]);
            
            //return view('dashboard');
        }

    }
    
    
    public function facebookRedirect()
    {
        return Socialite::driver('facebook')->stateless()->redirect();

    }

    
    public function facebookCallback(Request $req)
    {
        try{
            $user = Socialite::driver('facebook')->stateless()->user();
            dd($user);

        }
        catch (GuzzleHttp\Exception\ClientException  $e) {
            dd($e->getMessage);
        }
    }
    
    
    
    public function facebookToken(Request $request)
    {
        
        $valid = $request->validate([
                'token' => 'required',
                'device_token' => 'required'
            ]);
        
        //$token = "EAALGUYtiCZAQBAHZAPHtARrLfwZBKYFu7B6n4qNQbVLGfNjjt17fcGz7qvUoTgiBf3hiG8jXK6IyTFSkMB3ZC3x196mQgVhmaLWBdbleyUqU5DVdEfXU6SUcZCqxyTDAn6LIHZBMmi2LnZCXzQekcxDuSZCIqWX3E8orU7jQZCuwu4NXqBF4nEcNo0lPYZBZCt910O1MHWwZCJdYXtdfUGsKyZCsfAMQnw0TnNmL56HDbOPEQmHtHA6ikUhLa";
        $token = $request->token;
        $user = Socialite::driver('facebook')->userFromToken($token);
        if(!$user) {
            return response()->json([
                        "message"   => "Invalid Token!",
                        "status"    => 401,
                    ]);
        }
        
        $data = $user;
        //dd($data);
        
        if(!isset($data->email) && $data->email == null) {
                return response()->json([
                        "message"   => "Email not Associated with this account!",
                        "status"    => 401,
                ]);    
        }
        
        $providerId = $data->id;
        $name = $data->name;
        $email = $data->email;
        $image = $data->avatar;
        
        
        // check if email exists
        $checkIfUserExists = User::where('email',$email)->first();
        if(!$checkIfUserExists) {
            // means we dont have this user
            $explode_name = explode(" ",$name);
            $newuser =  User::create([
                'first_name' => $explode_name[0],
                'last_name' => $explode_name[1],
                'email' => $email,
                'password' => Hash::make(\Str::random(8)),
                'device_token' => $request->device_token
            ]);

            Provider::create([
                'user_id' => $newuser->id,
                'provider_id' => $providerId 
            ]);
            
            $token = $newuser->createToken($newuser->email . '_Token')->plainTextToken;
                
                return response()->json([
                        "message"   => "New User Created!",
                        "status"    => 201,
                        "token"     => $token,
                        "data"      => $newuser
                    ]);
            
        } else {
            //means we already have this user
            
            //update the device token
            $checkIfUserExists->update([
                    "device_token" => $request->device_token
                ]);
            
            $socialAccount = $checkIfUserExists->socialAccounts()->where('provider_id', $providerId)->first();
            //if social account is not linked to this email, then link it
            if(!$socialAccount) {
                $socialAccount = Provider::create([
                    'user_id'        => $checkIfUserExists->id,
                    'provider_id'    => $providerId
                ]);
            }
            
            $token = $checkIfUserExists->createToken($checkIfUserExists->email . '_Token')->plainTextToken;
             return response()->json([
                        "message"   => "Ok!",
                        "status"    => 200,
                        "token"     => $token,
                        "data"      => $checkIfUserExists
                    ]);
            
            //return view('dashboard');
        }
        
    }
    
}
