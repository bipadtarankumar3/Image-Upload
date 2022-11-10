<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Models\User;

use Validator;

class UserController extends Controller
{
    public function register(Request $request){
        
        $vali = Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required',
            'password'=>'required',
        ]);

        if($vali->fails()){
            return response()->json($vali->errors(),202);
        }

        $inp =  $request->all();
        $inp['password'] =  bcrypt($request->password);

        $user = User::create($inp);

        $resArr = array();
        $resArr['token'] = $user->createToken('Laravel Password Grant Client')->accessToken;
        $resArr['name'] = $user->name;

        return response()->json($resArr,200);


    }


    public function login(Request $request){

        if(Auth::attempt(['email'=>$request->email,'password'=>$request->password])){
            $user = Auth::user();

            $resArr = array();
            $resArr['token'] = $user->createToken('Laravel Password Grant Client')->accessToken;
            $resArr['name'] = $user->name;

            return response()->json($resArr,200);

        }else{
            return response()->json('please provide valid user or password',202);
        }

    }


    public function update(Request $request){

        $vali  = Validator::make($request->all(),[
            'image'=>'required|max:500'
        ]);

        if($vali->fails()){
            return response()->json($vali->errors(),202);
        }

        $image = $request->image;

        $user = Auth::user();

        $getUser = User::find($user->id);


        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/upload');
            $image->move($destinationPath, $name);

            $getUser->image = $name;

            $getUser->save();
    
            return response()->json('Image Upload successfully');
        }
        





    }



}
