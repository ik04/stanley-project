<?php


namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use Ramsey\Uuid\Uuid;

class UserController extends Controller
{
    public function register(Request $request){
        $validation = Validator::make($request->all(),[
            'name' =>'required|string',
            "email"=>'required|string|unique:users',
            'password'=>'required|string'
        ]);
        if($validation->fails()){
            return response()->json($validation->errors()->all(),400);
        }
        $validated = $validation->validated();
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'user_id' => Uuid::uuid4(),
        ]);
        return response()->json(['user'=>$user],201);
    }
    public function login(Request $request){
        $validation = Validator::make($request->all(),[
            "email"=>'required|string',
            'password'=>'required|string'
        ]);
        if($validation->fails()){
            return response()->json($validation->errors()->all(),400);
        }
        $validated = $validation->validated();
        $user = User::where('email',$validated['email'])->first();
        if(!$user){
            return response()->json(['error'=>"Email Not registered"],400);
        }
        if(!Hash::check($validated['password'],$user->password)){
            return response()->json(['error'=>'Invalid Credentials'],400);
        }
        $token = $user->createToken('myapptoken')->plainTextToken;
        return response()->json(['user'=>$user,'token'=>$token],200)->withCookie(cookie()->forever('at',$token));

    }
    public function logout(Request $request){

        $request->user()->currentAccessToken()->delete();
        $response =  [
            'message' => 'logged out'
        ];
        return response($response,200);

    }
}
