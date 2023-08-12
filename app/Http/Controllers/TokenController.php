<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TokenController extends Controller
{
    /**
     * @OA\Post(
     *      path="/tokens/create",
     *      operationId="create",
     *      tags={"token"},
     *      summary="create Token for user after authentication",
     *      description="create token for user after authentication",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"email", "password"},
     *            @OA\Property(property="email", type="string", format="email", example="test@example.com"),
     *            @OA\Property(property="password", type="string", format="string", example="12345"),
     *         ),
     *      ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", example=""),
     *             @OA\Property(property="token_type",type="string", example="Bearer")
     *          )
     *       )
     *  )
     */
    public function createToken(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid Credentials'
            ], 401);
        }

        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }
}
