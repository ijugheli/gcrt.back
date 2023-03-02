<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserAction;
use App\Http\Helpers\Helper;
use Illuminate\Http\Request;
use App\Models\UserValidationCode;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\CodeSenderService;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private $codeSenderService;

    public function __construct(CodeSenderService $codeSenderService)
    {
        $this->middleware('auth:api', ['except' => ['login', 'refresh', 'logout', 'validateCode', 'sendCode', 'recoverPassword']]);
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
            return $this->sendCode(config('constants.actionTypeIDS.otp'), config('constants.validationTypeIDS.email'), $user);
        }

        Helper::saveUserAction(config('constants.userActionTypesIDS.login'), null, null, null, $user->id);

        return $this->respondWithToken(Auth::attempt($credentials));
    }

    public function sendRecoveryLink()
    {
        $data = request()->only(['actionType', 'validationType', 'email']);

        $validator = Validator::make($data, [
            'actionType' => 'required|integer',
            'validationType' => 'required|integer',
            'email' => 'required',
        ]);

        if ($validator->fails() || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return response()->json(['code' => 0, 'message' => 'შეიყვანეთ ელ-ფოსტა სწორი ფორმატით', 'errors' => $validator->errors()]);
        }

        return $this->sendCode($data['actionType'], $data['validationType'], $data['email']);
    }

    // $user is User model or Users email
    public function sendCode(int $actionType, int $validationType, $data)
    {
        $user = $data instanceof \Illuminate\Database\Eloquent\Model ? $data : User::where('email', $data)->first();

        if (is_null($user)) {
            return response()->json(['code' => 0, 'message' => 'მომხმარებელი ვერ მოიძებნა'], 400);
        }

        // Temporary solution
        if (!$this->codeSenderService->send($actionType, $validationType, $user)) {
            return response()->json(['code' => 0, 'message' => 'გთხოვთ სცადოთ მოგვიანებით'], 400);
        }

        return response()->json(['code' => 1, 'message' => 'ლინკი/კოდი წარმატებით გამოიგზავნა თქვენს ელ-ფოსტაზე']);
    }

    public function validateCode(Request $request)
    {
        $validationCode = UserValidationCode::where('code', $request->code)->with('user')->first();

        if (is_null($validationCode) || $validationCode->expires_at->isPast()) {
            return response()->json(['code' => 0, 'message' => 'ლინკი/კოდი არავალიდურია'], 400);
        }

        if ($validationCode->action_type == config('constants.actionTypeIDS.otp')) {
            $validationCode->delete();
            Helper::saveUserAction(config('constants.userActionTypesIDS.login'), null, null, null, $validationCode->user->id);
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
        Helper::saveUserAction(config('constants.userActionTypesIDS.logout'));
        return response()->json(['code' => 1, 'message' => 'Successfully logged out']);
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
