<?php

namespace App\Http\Controllers;

use App\Http\Services\CodeSenderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use  App\Models\User;
use App\Models\UserValidationCode;

class AuthController extends Controller
{
    private $codeSenderService;

    public function __construct(CodeSenderService $codeSenderService)
    {
        $this->middleware('auth:api', ['except' => ['login', 'refresh', 'logout','validateCode', 'sendCode']]);
        $this->codeSenderService = $codeSenderService;
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {

        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);

        if (!$token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }


    public function sendCode()
    {
        $user = User::where('email', request()->email)->first();

        if (is_null($user)) {
            return response()->json(['code' => 0, 'message' => 'მომხმარებელი ვერ მოიძებნა'], 400);
        }

        // Temporary solution
        if (!$this->codeSenderService->send(1, 1, $user)) {
            return response()->json(['code' => 0, 'message' => 'გთხოვთ სცადოთ მოგვიანებიტ'], 400);
        }

        return response()->json(['code' => 1, 'message' => 'ლინკი წარმატებით გამოიგზავნა თქვენს ელ-ფოსტაზე']);
    }

    public function validateCode()
    {
        $code = request()->code;
        $email = request()->email;
        $user = User::where('email', $email)->first();
        $validated = UserValidationCode::where('user_id', $user->id)->where('code', $code)->first();

        if (is_null($user)) {
            return response()->json(['code' => 0, 'message' => 'მომხმარებელი ვერ მოიძებნა'], 400);
        }

        if (is_null($validated)) {
            return response()->json(['code' => 0, 'message' => 'ლინკი არავალიდურია'], 400);
        }

        $validated->delete();

        return response()->json(['code' => 1, 'message' => 'Success']);
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
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => auth()->user(),
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
