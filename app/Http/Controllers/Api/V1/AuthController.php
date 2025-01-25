<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function register(Request $request)
    {
        // return response()->json($request->all());

        $validator = Validator::make($request->all(), [
            "email" => "required|email:rfc,dns",
            "password" => ['required', Password::defaults()]
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "Gagal validasi pada request", "errors" => $validator->errors()], 403);
        }

        User::create([
            "name" => explode("@", $request->email)[0],
            "email" => $request->email,
            "email_verified_at" => now(),
            "password" => Hash::make($request->password),
        ]);

        return response()->json(["message" => "Data berhasil ditambahkan"]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => "required|email:rfc,dns",
            "password" => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "Gagal validasi pada request", "errors" => $validator->errors()], 403);
        }

        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            $validator->errors()->add(
                'email',
                'Email salah/tidak tersedia'
            );
            $validator->errors()->add(
                'password',
                'Password salah/tidak tersedia'
            );
        }

        return response()->json(["message" => "Anda berhasil login"]);
    }

    public function LoginTest()
    {
        return Auth::viaRemember();
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
