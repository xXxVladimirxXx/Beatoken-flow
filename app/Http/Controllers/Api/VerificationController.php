<?php

namespace App\Http\Controllers\Api;

use App\Models\{User};
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify($id, Request $request) {
        if (!$request->hasValidSignature()) {
            return response()->json(["message" => "Invalid/Expired url provided"], 401);
        }

        $user = User::findOrFail($id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect()->to('/');
    }

    public function resend() {
        if (auth()->user()->hasVerifiedEmail()) {
            return response()->json(["message" => "Email already verified"], 400);
        }

        auth()->user()->sendEmailVerificationNotification();

        return response()->json(["message" => "Email verification link sent on your email id"], 200);
    }
}
