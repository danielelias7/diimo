<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as RulesPassword;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
    * @OA\Get(
    * path="/api/v1/users",
    * operationId="getUsers",
    * tags={"Users"},
    * summary="Get user information.",
    * description="Return data.",
    * @OA\Response(
    *     response=200,
    *     description="Successful operation",
    *     @OA\JsonContent(
    *         type="object",
    *         @OA\Property(property="current_page", type="integer"),
    *         @OA\Property(
    *             property="data",
    *             type="array",
    *             @OA\Items(
    *                 @OA\Property(property="id", type="integer"),
    *                 @OA\Property(property="name", type="string"),
    *                 @OA\Property(property="username", type="string"),
    *                 @OA\Property(property="phone", type="integer"),
    *                 @OA\Property(property="email", type="string"),
    *                 @OA\Property(property="birthday", type="date"),
    *                 @OA\Property(property="email_verified_at", type="string", format="date-time"),
    *                 @OA\Property(property="created_at", type="string", format="date-time"),
    *                 @OA\Property(property="updated_at", type="string", format="date-time"),
    *             )
    *         ),
    *         @OA\Property(property="first_page_url", type="string"),
    *         @OA\Property(property="from", type="integer"),
    *         @OA\Property(property="last_page", type="integer"),
    *         @OA\Property(property="last_page_url", type="string"),
    *         @OA\Property(
    *             property="links",
    *             type="array",
    *              @OA\Items(
    *                  @OA\Property(property="url", type="string"),
    *                  @OA\Property(property="label", type="string"),
    *                  @OA\Property(property="active", type="boolean")
    *              )
    *         ),
    *         @OA\Property(property="next_page_url", type="integer"),
    *         @OA\Property(property="path", type="string"),
    *         @OA\Property(property="per_page", type="integer"),
    *         @OA\Property(property="prev_page_url", type="integer"),
    *         @OA\Property(property="to", type="integer"),
    *         @OA\Property(property="total", type="integer"),
    *     )
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
    public function index(){
        $users = User::latest()->paginate(10);
        return response()->json($users, 200);
    }

    /**
    * @OA\Put(
    * path="/api/v1/users/{id}",
    * operationId="updateUser",
    * tags={"Users"},
    * summary="Update user information.",
    * description="Update data.",
    * @OA\RequestBody(
    *     @OA\MediaType(
    *        mediaType="application/json",
    *        @OA\Schema(
    *           type="object",
    *           @OA\Property(property="name", type="string"),
    *           @OA\Property(property="phone", type="integer"),
    *           @OA\Property(property="birthay", type="string", format="date-time")
    *        ),
    *    ),
    * ),
    * @OA\Response(
    *     response=200,
    *     description="Successful operation",
    *     @OA\JsonContent(
    *         type="object",
    *         @OA\Property(property="id", type="integer"),
    *         @OA\Property(property="name", type="string"),
    *         @OA\Property(property="username", type="string"),
    *         @OA\Property(property="phone", type="integer"),
    *         @OA\Property(property="email", type="string"),
    *         @OA\Property(property="birthday", type="date"),
    *         @OA\Property(property="email_verified_at", type="string", format="date-time"),
    *         @OA\Property(property="created_at", type="string", format="date-time"),
    *         @OA\Property(property="updated_at", type="string", format="date-time"),
    *     )
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
    public function update(Request $request, $id){
        $user = User::find($id);
        if ($user) {
            $validator = Validator::make($request->all(), [
                'name'      => 'string',
                'phone'     => 'integer',
                'birthday'  => 'date',
            ]);
            if($validator->fails()){
                return response()->json($validator->errors(), 400);
            }
            $data = [
                'name'      => $request->get('name') ?? $user->name,
                'phone'     => $request->get('phone') ?? $user->phone,
                'birthay'   => $request->get('birthday') ?? $user->birthday,
            ];
            $user->update($data);

            return response()->json($user, 200);
        }
        return response()->json(['message'=>'Not Found'], 404);
    }

    /**
    * @OA\Delete(
    * path="/api/v1/users/{id}",
    * operationId="deleteUser",
    * tags={"Users"},
    * summary="Delete user information.",
    * description="Return data.",
    * @OA\Parameter(
    *     name="id",
    *     in="path",
    *     description="ID user",
    *     required=true,
    *     @OA\Schema(
    *         type="integer"
    *     )
    * ),
    * @OA\Response(
    *     response=204,
    *     description="No content",
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
    public function destroy($id){
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return response()->json([], 204);
        }
        return response()->json(['message'=>'Not Found'], 404);
    }

    /**
    * @OA\Get(
    * path="/api/v1/me",
    * operationId="getMe",
    * tags={"Users"},
    * summary="Get user information.",
    * description="Return data.",
    * @OA\Response(
    *     response=200,
    *     description="Successful operation",
    *     @OA\JsonContent(
    *         type="object",
    *         @OA\Property(property="id", type="integer"),
    *         @OA\Property(property="name", type="string"),
    *         @OA\Property(property="username", type="string"),
    *         @OA\Property(property="phone", type="integer"),
    *         @OA\Property(property="email", type="string"),
    *         @OA\Property(property="birthday", type="date"),
    *         @OA\Property(property="email_verified_at", type="string", format="date-time"),
    *         @OA\Property(property="created_at", type="string", format="date-time"),
    *         @OA\Property(property="updated_at", type="string", format="date-time"),
    *     )
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
    public function me()
    {
        return response()->json(auth()->user(), 200);
    }
}
