<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiDocsController extends Controller
{
    public function index()
    {
        $baseUrl = config('app.url') . '/api/v1';
        
        return view('api-docs.index', [
            'baseUrl' => $baseUrl,
        ]);
    }

    public function generateToken(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $user = Auth::user();
        $token = $user->createToken($request->name)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token generated successfully',
            'data' => [
                'token' => $token,
                'token_name' => $request->name,
            ],
        ]);
    }
}

