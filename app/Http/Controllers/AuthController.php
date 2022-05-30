<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as RulesPassword;

class AuthController extends Controller
{
    /**
    * @OA\Post(
    * path="/api/v1/login",
    * operationId="authLogin",
    * tags={"Users"},
    * summary="User Login",
    * description="Login User Here",
    *     @OA\RequestBody(
    *         @OA\JsonContent(),
    *         @OA\MediaType(
    *            mediaType="multipart/form-data",
    *            @OA\Schema(
    *               type="object",
    *               required={"email", "password"},
    *               @OA\Property(property="email", type="email"),
    *               @OA\Property(property="password", type="password")
    *            ),
    *        ),
    *    ),
    *      @OA\Response(
    *          response=201,
    *          description="Login Successfully",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=200,
    *          description="Login Successfully",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=422,
    *          description="Unprocessable Entity",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(response=400, description="Bad request"),
    *      @OA\Response(response=404, description="Resource Not Found"),
    * )
    */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token'  => $token,
            'token_type'    => 'bearer',
            'expires_in'    => auth()->factory()->getTTL() * 60
        ]);
    }

    /**
    * @OA\Post(
    * path="/api/v1/register",
    * operationId="authRegister",
    * tags={"Users"},
    * summary="User Register",
    * description="Register User Here",
    * @OA\RequestBody(
    *     @OA\MediaType(
    *        mediaType="application/json",
    *        @OA\Schema(
    *           type="object",
    *           required={"name", "username", "phone", "birthday", "email", "password"},
    *           @OA\Property(property="name", type="string"),
    *           @OA\Property(property="username", type="string"),
    *           @OA\Property(property="phone", type="integer"),
    *           @OA\Property(property="birthday", type="date"),
    *           @OA\Property(property="email", type="string"),
    *           @OA\Property(property="password", type="password")
    *        ),
    *    ),
    * ),
    * @OA\Response(
    *     response=201,
    *     description="Register created.",
    *     @OA\JsonContent(
    *         type= "object",
    *         @OA\Property(property="message", type="string", description="Message"),
    *         @OA\Property(
    *             property="user",
    *             type="object",
    *             @OA\Property(property="name", type="string"),
    *             @OA\Property(property="username", type="string"),
    *             @OA\Property(property="phone", type="integer"),
    *             @OA\Property(property="birthday", type="date"),
    *             @OA\Property(property="email", type="string"),
    *             @OA\Property(property="email_verified_at", type="string", format="date-time"),
    *             @OA\Property(property="created_at", type="string", format="date-time"),
    *             @OA\Property(property="updated_at", type="string", format="date-time"),
    *         )
    *     )
    * ),
    * @OA\Response(response=400, description="Bad request"),
    * @OA\Response(response=404, description="Resource Not Found"),
    * )
    */
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'phone'     => 'required|integer',
            'username'  => 'required|unique:users',
            'birthday'  => 'required|date',
            'email'     => 'required|string|email|max:100|unique:users',
            'password'  => 'required'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $user = User::create(array_merge(
            $validator->validate(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'message'   => 'User created successfully.',
            'user'      => $user
        ], 201);
    }

    /**
    * @OA\Post(
    * path="/api/v1/forgot-password",
    * operationId="forgotPassword",
    * tags={"Users"},
    * summary="Forgot password.",
    * description="Return data.",
    * @OA\RequestBody(
    *     @OA\MediaType(
    *        mediaType="application/json",
    *        @OA\Schema(
    *           type="object",
    *           @OA\Property(property="email", type="string")
    *        ),
    *    ),
    * ),
    * @OA\Response(
    *     response=200,
    *     description="Successful operation",
    *     @OA\JsonContent(
    *         type="object",
    *         @OA\Property(property="message", type="string"),
    *      ),
    *  ),
    * @OA\Response(
    *     response=400,
    *     description="Bad Request"
    * ),
    * @OA\Response(
    *     response=401,
    *     description="Unauthenticated",
    * ),
    * @OA\Response(
    *     response=403,
    *     description="Forbidden"
    * )
    * )
    */
    public function forgotPassword(Request $request){
        $request->validate([
            'email' => 'required|email'
        ]);

        Password::sendResetLink(
            $request->only('email')
        );

        return response()->json(['message' => 'Reset password link sent on your email.'], 200);

    }

    /**
    * @OA\Post(
    * path="/api/v1/reset-password",
    * operationId="resetPassword",
    * tags={"Users"},
    * summary="Reset password.",
    * description="Return data.",
    *
    * @OA\Parameter(
    *     name="email",
    *     in="query",
    *     description="Email",
    *     required=true,
    *     @OA\Schema(
    *         type="string"
    *     )
    * ),
    * @OA\Parameter(
    *     name="password",
    *     in="query",
    *     description="Password",
    *     required=true,
    *     @OA\Schema(
    *         type="string"
    *     )
    * ),
    * @OA\Parameter(
    *     name="token",
    *     in="query",
    *     description="Token",
    *     required=true,
    *     @OA\Schema(
    *         type="string"
    *     )
    * ),
    * @OA\Response(
    *     response=200,
    *     description="Successful operation",
    *     @OA\JsonContent(
    *         type="object",
    *         @OA\Property(property="message", type="string"),
    *      ),
    *  ),
    * @OA\Response(
    *     response=400,
    *     description="Bad Request"
    * ),
    * @OA\Response(
    *     response=401,
    *     description="Unauthenticated",
    * ),
    * @OA\Response(
    *     response=403,
    *     description="Forbidden"
    * )
    * )
    */
    public function resetPassword(Request $request){
        $credentials = $request->validate([
            'email'     => 'required',
            'password'  => 'required',
            'token'     => 'required'
        ]);

        $status = Password::reset($credentials, function($user, $password){
            $user->password = bcrypt($password);
            $user->save();
        });

        if($status == Password::INVALID_TOKEN){
            return $this->response()->json([], 401);
        }

        return response()->json(['message' => 'Password updated succesfully'], 200);
    }
}