<?php

namespace App\Http\Controllers;

use App\Http\Services\CodeSenderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserValidationCode;

class AuthController extends Controller
{
    private $codeSenderService;

    public function __construct(CodeSenderService $codeSenderService)
    {
        $this->middleware('auth:api', ['except' => ['login', 'refresh', 'logout', 'validateCode', 'sendCode']]);
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


        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return response()->json(['code' => 0, 'message' => 'მომხმარებელი ვერ მოიძებნა'], 401);
        }

        if (!Auth::validate($credentials)) {
            return response()->json(['code' => 0, 'message' => 'გთხოვთ სცადოთ განსხვავებული პარამეტრები'], 401);
        }

        if ($user->isOTPEnabled()) {
            return $this->sendCode(config('settings.ACTION_TYPE_IDS.OTP'), config('settings.VALIDATION_TYPE_IDS.EMAIL'), $user);
        }

        if (!$token = Auth::attempt($credentials)) {
            return response()->json(['code' => 0, 'message' => 'Unauthorized'], 401);
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


    public function sendCode(int $actionType, int $validationType, ?User $user)
    {
        $user = $user ?? User::where('email', request()->email)->first();

        if (is_null($user)) {
            return response()->json(['code' => 0, 'message' => 'მომხმარებელი ვერ მოიძებნა'], 400);
        }

        // Temporary solution
        if (!$this->codeSenderService->send($actionType, $validationType, $user)) {
            return response()->json(['code' => 0, 'message' => 'გთხოვთ სცადოთ მოგვიანებით'], 400);
        }

        return response()->json(['code' => 1, 'message' => 'ლინკი/კოდი წარმატებით გამოიგზავნა თქვენს ელ-ფოსტაზე']);
    }

    public function validateCode()
    {
        $code = request()->code;
        $validationCode = UserValidationCode::where('code', $code)->with('user')->first();

        if (is_null($validationCode)) {
            return response()->json(['code' => 0, 'message' => 'ლინკი/კოდი არავალიდურია'], 400);
        }

        if ($validationCode->action_type == config('settings.ACTION_TYPE_IDS.OTP')) {
            $validationCode->delete();
            return $this->respondWithToken(Auth::login($validationCode->user));
        }

        $validationCode->delete();

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
            'code' => 1,
            'message' => 'ავტორიზაცია წარმატებით დასრულდა',
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'user' => auth()->user(),
                'expires_in' => auth()->factory()->getTTL() * 60
            ],
        ]);
    }
}
