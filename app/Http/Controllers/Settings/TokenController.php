<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TokenController extends Controller
{
    /**
     * Show the user's token settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/Token', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
            'tokens' => $request->user()->tokens()->select('id', 'name', 'expires_at')->get(),
        ]);
    }

    /**
     * Generate new user's token.
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'user_id' => 'required|exists:users,id',
            'token_name' => 'required|in:application,prometheus',
        ]);

        $abilities = [
            'application' => '*',
            'prometheus' => 'prometheus:view',
        ];

        $token = $request->user()->createToken($validated['token_name'], [$abilities[$validated['token_name']]], now()->addYear());

        return Inertia::render('settings/Token', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
            'tokens' => $request->user()->tokens()->select('id', 'name', 'expires_at')->get(),
            'newToken' => $token->plainTextToken,
        ]);
    }

    /**
     * Revoke user's tokens.
     */
    public function revoke(Request $request, $tokenId)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
        ]);

        $token = $request->user()->tokens()->find($tokenId);

        if (! $token) {
            return response()->json(['message' => 'You don\'t own this token.'], 403);
        }

        $token->delete();

        return response()->json(['message' => 'Token has been deleted successfully.'], 200);
    }
}
