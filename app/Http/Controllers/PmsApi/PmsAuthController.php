<?php

namespace App\Http\Controllers\PmsApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class PmsAuthController extends Controller
{
    //
    public function pms_login(Request $request){

        try {
            if (Auth::attempt($request->only('email', 'password'))) {

                /** @var User $user */
                $user = Auth::user();

                $token = $user->createToken("PMS API TOKEN")->plainTextToken;

                $output = [
                    'success' => true,
                    'message' => 'You are successfully logged in',
                    'token' => $token,
                    'id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'role' => $user->role,
                ];

                return response()->json($output,200);
            }
        } catch (\Exception $e) {
            return response([
                'message' => 'Internal error, please try again later.'
            ], 400);
        }

        return response([
            'message' => 'Invalid Email or password.'
        ], 401);
        
        
        
        ///return $request->all();

    }


    public function logout(Request $request){

         try {
           
                /** @var User $user */
                $user = Auth::user();

                //return $user->currentAccessToken()->id;

                $user->tokens()->where('id',$user->currentAccessToken()->id)->delete();

                return response()->json([
                    'status'=>true,
                    'message' => 'Successfully logout'
                ],200);
        
         } catch (\Exception $e) {
             return response([
                 'message' => 'Internal error.'
             ], 400);
         }

        return response([
            'message' => 'Something went wrong'
        ], 401);
        
        
        ///return $request->all();

    }


    public function changepassword(Request $request){

        try {
           
           $newpass =  $request->newpass;
            
            /** @var User $user */
            $user = Auth::user();
            $id = $user->id;
            //return $id;
            
            $output = User::where('id',$id)
                ->update([
                    'password' => Hash::make($newpass)
                ]);


            //return response()->json($output,200);

            return response()->json([
                'status'=>true,
                'message' => 'Password successfully changed'
            ],200);
            
        } catch (\Exception $e) {
            return response([
                'message' => 'Internal error, please try again later.'
            ], 400);
        }

        return response([
            'message' => 'Something went wrong.'
        ], 401);
        
        
        
        ///return $request->all();

    }

    

}
