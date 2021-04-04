<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Image;
use Str;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum'], ['only' => ['logout', 'me']]);
    }

    public function login()
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response([
                'status' => 'validation_error',
                'errors' => $validator->errors()
            ], 422);
        }

        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $accessToken = Auth::user()->createToken('authToken');
            $user = Auth::user();
            return response([
                'status' => 'done',
                'message' => 'Successfully logged in...',
                'token' => 'Bearer ' . $accessToken->plainTextToken,
                'user' => $user,
            ], 200);
        } else {
            return response([
                'status' => 'error',
                'message' => 'Credentials doesn\'t matched...'
            ], 401);
        }
    }

    public function register()
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'phone' => 'required',
            'alternate_phone' => 'required',
            'blood_group' => 'required',
            'weight' => 'required',
            'gender' => 'required',
            'street_address' => 'required',
            'city' => 'required',
            'post_code' => 'required|numeric',
            'dob' => 'required',
            'age' => 'required|numeric',
            'avatar' => 'required|image',
        ]);

        if ($validator->fails()) {
            return response([
                'status' => 'validation_error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = new User();

        $user->name = request('name');
        $user->email = request('email');
        $user->password = Hash::make(request('password'));
        $user->phone = request('phone');
        $user->alternate_phone = request('alternate_phone');
        $user->social_link = request('social_link');
        $user->blood_group = request('blood_group');
        $user->weight = request('weight');
        $user->gender = request('gender');
        $user->street_address = request('street_address');
        $user->city = request('city');
        $user->post_code = request('post_code');
        $user->dob = request('dob');
        $user->age = request('age');
        $user->role = 'donor';

        $img = request()->file('avatar');

        if (!file_exists('assets/images/users')) {
            $dir = mkdir('assets/images/users');
        }

        if ($img->getMimeType() === 'image/png') {
            $avatar_name = 'assets/images/users/' . Str::slug(request('name')) . '-' . Str::random(6) . '.png';
            Image::make($img)->save($avatar_name, 80);
            $user->avatar = request()->getHost() . '/' . $avatar_name;
        } else {
            $avatar_name = 'assets/images/users/' . Str::slug(request('name')) . '-' . Str::random(6) . '.jpg';
            Image::make($img)->save($avatar_name, 80);
            $user->avatar = request()->getHost() . '/' . $avatar_name;
        }

        $user->save();

        return response([
            'status' => 'done',
            'message' => 'Successfully registered...'
        ], 201);
    }

    public function logout()
    {
        $auth = Auth::user();

        $auth->tokens()->delete();

        return response([
            'status' => 'done',
            'message' => 'Successfully logout...',
        ], 200);

    }

    public function me()
    {
        $auth = Auth::user();

        return response([
            'status' => 'done',
            'user' => $auth
        ], 200);
    }
}
