<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

use App\Representations\UserRepresentation;
use App\Representations\UserRepresentationCollection;

use DB;
use Log;
use Auth;
use Config;
use Storage;
use Validator;
use Carbon\Carbon;
use Laravel\Passport\Passport;

class AuthController extends Controller
{
    /**
        * @OA\Post(
        *    description = "Register User",
        *    summary     = "Register User",
        *    operationId = "register",
        *    tags        = {"Auth"},
        *    path        = "/auth/register",
        *    @OA\RequestBody(
        *        @OA\MediaType(
        *            mediaType = "application/json",
        *            @OA\Schema(
        *                @OA\Property(property = "name", type = "string"),
        *                @OA\Property(property = "email", type = "string"),
        *                @OA\Property(property = "password", type = "string")
        *            )
        *        )
        *    ),
        *    @OA\Response(
        *        response    = 200,
        *        description = "Successful"
        *    )
        * )
    */
    public function register(Request $request)
    {
        $data = $request->json()->all();

        $validator = Validator::make($data, [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'password' => 'required|string'
        ], trans('validation'), trans('validation.attributes'));

        if($validator->fails()) {
            $error_messages = [];
            $errors = $validator->errors();
            foreach($errors->all() as $message) {
                $error_messages[] = $message;
            }
            return response()->json($error_messages, 400);
        }

        $user = User::where('email', $data['email']);
        if($user->exists()) {
            return response()->json([trans('messages.already_exists', ['name' => 'email'])], 400);
        }

        $newUser = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            
            'status' => Config::get('constants.STATUS.ACTIVE'),
            'created_by' => Config::get('constants.OWNER'),
            'last_updated_by' => Config::get('constants.OWNER')
        ];

        $user = User::create($newUser);

        if(!$user) {
            return response()->json([trans('messages.internal_error')], 500);
        }

        $tokenResult = $user->createToken('PLATFORM-USER', ['*']);

        $token = $tokenResult->token;

        $token->save();

        $user->api_token = trim($tokenResult->accessToken);

        if(!$user->save()) {
            return response()->json([trans('messages.internal_error')], 500);
        }

        return new UserRepresentation($user);
    }

    /**
        * @OA\Post(
        *    description = "Login User",
        *    summary     = "Login User",
        *    operationId = "login",
        *    tags        = {"Auth"},
        *    path        = "/auth/login",
        *    @OA\RequestBody(
        *        @OA\MediaType(
        *            mediaType = "application/json",
        *            @OA\Schema(
        *                @OA\Property(property = "email", type = "string"),
        *                @OA\Property(property = "password", type = "string")
        *            )
        *        )
        *    ),
        *    @OA\Response(
        *        response    = 200,
        *        description = "Successful"
        *    )
        * )
    */
    public function login(Request $request)
    {
        $data = $request->json()->all();

        $validator = Validator::make($data, [
            'email' => 'required|email|max:255',
            'password' => 'required|string'
        ], trans('validation'), trans('validation.attributes'));

        if($validator->fails()) {
            $error_messages = [];
            $errors = $validator->errors();
            foreach($errors->all() as $message) {
                $error_messages[] = $message;
            }
            return response()->json($error_messages, 400);
        }

        $user = User::where('email', $data['email']);
        if($user->doesntExist()) {
            return response()->json([trans('messages.not_found', ['name' => 'email'])], 400);
        }
        $user = $user->first();

        if(!Hash::check($data['password'], $user->password)) {
            return response()->json([trans('messages.invalid', ['name' => 'password'])], 400);
        }

        $scopes = ['*'];
        
        $tokenResult = $user->createToken('PLATFORM-USER', $scopes);
        
        $token = $tokenResult->token;
        
        $token->save();

        $user->api_token = trim($tokenResult->accessToken);

        if(!$user->save()) {
            return response()->json([trans('messages.internal_error')], 500);
        }

        $request->scopes = $scopes;

        return new UserRepresentation($user);
    }

    /**
        * @OA\Post(
        *    description = "Logout Admin User",
        *    summary     = "Logout Admin User",
        *    operationId = "logout",
        *    tags        = {"Auth"},
        *    path        = "/auth/logout",
        *    security    = {{ "passport": {"*"} }},
        *    @OA\Response(
        *        response    = 200,
        *        description = "Successful"
        *    )
        * )
    */
    public function logout(Request $request)
    {
        try {
            Auth::logout();
        }
        catch(\Exception $e) {
            return response()->json(["Logout Failed"], 500);
        }

        return response()->json(["Success"], 200);
    }
}