<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Exception;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:farmer,agri_worker,admin',
            'location' => 'nullable|string|max:255',
            'device' => 'nullable|in:web,mobile',
        ]);

        try {
            $user = User::create([
                'name' => trim($request->name),
                'email' => strtolower(trim($request->email)),
                'password' => $request->password,
                'role' => $request->role,
                'location' => $request->location ?: 'Not specified',
            ]);

            if ($request->hasSession()) {
                Auth::guard('web')->login($user);
                $request->session()->regenerate();
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            $response = [
                'success' => true,
                'user' => $this->formatUser($user),
                'token' => $token,
                'message' => 'Account created and saved successfully!',
            ];

            return response()->json($response, 201);
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.',
            ], 500);
        }
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'device' => 'nullable|in:web,mobile',
        ]);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            if ($request->hasSession()) {
                Auth::guard('web')->login($user);
                $request->session()->regenerate();
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            $response = [
                'success' => true,
                'user' => $this->formatUser($user),
                'token' => $token,
                'message' => 'Login successful!',
            ];

            return response()->json($response);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid email or password.',
        ], 401);
    }

    public function logout(Request $request): JsonResponse
    {
        if ($request->user() && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        if ($request->hasSession()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    public function user(Request $request): JsonResponse
    {
        $user = $request->user() ?: Auth::guard('web')->user();
        if (!$user) {
            return response()->json(['success' => false, 'user' => null]);
        }

        return response()->json([
            'success' => true,
            'user' => $this->formatUser($user),
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'location' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'remove_avatar' => 'nullable',
        ]);

        if ($request->filled('name')) {
            $user->name = $request->name;
        }
        if ($request->has('location')) {
            $user->location = $request->location;
        }
        if ($request->filled('password')) {
            $user->password = $request->password;
        }

        // Handle Profile Picture upload / removal
        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        } elseif ($request->boolean('remove_avatar') || $request->input('remove_avatar') === '1' || $request->input('remove_avatar') === 'true') {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = null;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'user' => $this->formatUser($user),
            'message' => 'Profile updated successfully.',
        ]);
    }

    private function formatUser(User $user): array
    {
        $avatarUrl = null;
        if ($user->avatar) {
            $avatarUrl = str_starts_with($user->avatar, 'http') || str_starts_with($user->avatar, 'data:')
                ? $user->avatar
                : asset('storage/' . $user->avatar);
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'role_label' => match($user->role) {
                'farmer' => 'Rice Farmer',
                'agri_worker' => 'Agricultural Extension Worker',
                'admin' => 'Administrator / Researcher',
                default => ucfirst($user->role),
            },
            'location' => $user->location ?: 'Not specified',
            'avatar' => $user->avatar,
            'avatar_url' => $avatarUrl,
            'created_at' => $user->created_at ? $user->created_at->format('M j, Y') : 'N/A',
        ];
    }
}
