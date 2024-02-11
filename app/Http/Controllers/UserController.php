<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function signup(Request $request)
    {
        // user id は 6文字以上20文字以下の半角英数字
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string|regex:/^[a-zA-Z0-9]{6,20}$/',
            'password' => 'required|string|regex:/^[a-zA-Z0-9]{8,20}$/',
        ]);
        // バリデーションに引っかかった場合
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Account creation failed',
                'cause' => "required user_id and password"
            ], 400);
        }
        $user_id = $request->input('user_id');
        $password = $request->input('password');

        $user = new User([
            'user_id' => $user_id,
            'password' => $password
        ]);
        $user->save();
        

        return response()->json([
            'message' => 'Account successfully created!',
            "user" => [
                "user_id" => $user_id,
                "nickname" => $user->nickname,
            ]
        ], 201);
    }

    public function getUser(Request $request, $user_id)
    {
        // header 取得
        $token = $request->bearerToken();
        $tokenDecode = base64_decode($token);
        // : で分割
        $token_user_id = explode(":", $tokenDecode)[0];
        $token_password = explode(":", $tokenDecode)[1];
        $auth_user = User::where('user_id', $token_user_id)->first();
        if ($auth_user === null || $auth_user->password !== $token_password) {
            return response()->json([
                'message' => 'Authentication failed',
            ], 401);
        }
        Log::info($tokenDecode);
        $user = User::where('user_id', $user_id)->first();
        if ($user === null) {
            return response()->json([
                'message' => 'No User Found',
            ], 404);
        }

        $res = [
            'user_id' => $user->user_id,
            "nickname" => $user_id,
        ];
        if ($user->nickname != null) {
            $res['nickname'] = $user->nickname;
            $res['comment'] = $user->comment;
        }
        return response()->json([
            'user' => $res
        ], 200);
    }

    public function updateUser(Request $request, $user_id)
    {
        // header 取得
        $token = $request->bearerToken();
        $tokenDecode = base64_decode($token);
        // : で分割
        $token_user_id = explode(":", $tokenDecode)[0];
        $token_password = explode(":", $tokenDecode)[1];
        $auth_user = User::where('user_id', $token_user_id)->first();
        if ($auth_user === null || $auth_user->password !== $token_password) {
            return response()->json([
                'message' => 'Authentication failed',
            ], 401);
        }
        Log::info($tokenDecode);
        $user = User::where('user_id', $user_id)->first();
        if ($user === null) {
            return response()->json([
                'message' => 'No User Found',
            ], 404);
        }

        if ($user->user_id !== $auth_user->user_id) {
            return response()->json([
                'message' => 'No Permission for Update',
            ], 403);
        }

        $nickname = $request->input('nickname');
        $comment = $request->input('comment');
        if ($nickname === null && $comment === null) {
            return response()->json([
                'message' => 'User updation failed',
                'cause' => "required nickname or comment"
            ], 400);
        }

        if ($request->input('user_id') || $request->input('password')) {
            return response()->json([
                'message' => 'User updation failed',
                'cause' => "not updatable user_id and password"
            ], 400);
        }
        $user->nickname = $nickname;
        $user->comment = $comment;
        $user->save();
        return response()->json([
            'message' => 'User successfully updated',
            "recipe" => [
                "user_id" => $user_id,
                "nickname" => $nickname,
                "comment" => $comment
            ]
        ], 200);
    }

    public function deleteUser(Request $request)
    {
        // header 取得
        $token = $request->bearerToken();
        $tokenDecode = base64_decode($token);
        // : で分割
        $token_user_id = explode(":", $tokenDecode)[0];
        $token_password = explode(":", $tokenDecode)[1];
        $auth_user = User::where('user_id', $token_user_id)->first();
        if ($auth_user === null || $auth_user->password !== $token_password) {
            return response()->json([
                'message' => 'Authentication failed',
            ], 401);
        }
        $auth_user->delete();
        return response()->json([
            'message' => 'Account and user successfully removed',
        ], 200);
    }
}