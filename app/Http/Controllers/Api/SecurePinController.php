<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SecurePinController extends Controller
{
    public function checkPin(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'has_pin' => !is_null($user->secure_pin),
                'pin_set_at' => $user->secure_pin_set_at ? $user->secure_pin_set_at->toIso8601String() : null,
            ],
        ]);
    }

    public function setPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|string|min:4|max:6',
            'current_password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
            ], 400);
        }

        // Update PIN
        $user->secure_pin = Hash::make($request->pin);
        $user->secure_pin_set_at = now();
        $user->save();

        // ✅ PERBAIKAN: Refresh user untuk dapat updated timestamp
        $user->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Secure PIN set successfully',
            'data' => [
                'has_pin' => true,
                'pin_set_at' => $user->secure_pin_set_at->toIso8601String(),
            ],
        ]);
    }

    public function verifyPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (!$user->secure_pin) {
            return response()->json([
                'success' => false,
                'message' => 'No PIN set',
            ], 400);
        }

        if (!Hash::check($request->pin, $user->secure_pin)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect PIN',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'PIN verified successfully',
            'data' => [
                'verified' => true,
            ],
        ]);
    }

    public function resetPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
            ], 400);
        }

        // Remove PIN
        $user->secure_pin = null;
        $user->secure_pin_set_at = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Secure PIN reset successfully',
            'data' => [
                'has_pin' => false,
            ],
        ]);
    }

    public function changePin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_pin' => 'required|string',
            'new_pin' => 'required|string|min:4|max:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (!$user->secure_pin) {
            return response()->json([
                'success' => false,
                'message' => 'No PIN set',
            ], 400);
        }

        if (!Hash::check($request->current_pin, $user->secure_pin)) {
            return response()->json([
                'success' => false,
                'message' => 'Current PIN is incorrect',
            ], 400);
        }

        // Update PIN
        $user->secure_pin = Hash::make($request->new_pin);
        $user->secure_pin_set_at = now();
        $user->save();

        // ✅ PERBAIKAN: Refresh user
        $user->refresh();

        return response()->json([
            'success' => true,
            'message' => 'PIN changed successfully',
            'data' => [
                'has_pin' => true,
                'pin_set_at' => $user->secure_pin_set_at->toIso8601String(),
            ],
        ]);
    }
}