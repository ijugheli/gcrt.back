<?php

namespace App\Http\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\UserValidationCode;
use Illuminate\Support\Facades\Mail;

class CodeSenderService
{
    private $emailService;
    private $phoneService;

    public function __construct(EmailService $emailService, PhoneService $phoneService)
    {
        $this->phoneService = $phoneService;
        $this->emailService = $emailService;
    }

    public function send(int $actionType, int $validationType, User $user)
    {
        $codeExists = UserValidationCode::where('user_id', $user->id)->where('action_type', $actionType)->exists();

        if ($codeExists) {
            return false;
        }

        $time = Carbon::now();
        $model = new UserValidationCode;
        $model->user_id = $user->id;
        $model->code = $actionType == config('settings.ACTION_TYPE_IDS.RECOVER_PASSWORD') ? sha1(time()) : intval(str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT));
        $model->action_type = $actionType;
        $model->validation_type = $validationType;
        $model->created_at = $time;
        $model->expires_at = $time->addSeconds(config('settings.CODE_EXPIRY_TIME'));

        if (!$model->save()) {
            return false;
        }

        if (!$this->emailService->send($user->email, $model->code, $actionType)) {
            return false;
        }

        return true;
    }
}


class EmailService
{
    public function send($email, $hash, int $actionType)
    {
        $checkActionType = $actionType == config('settings.ACTION_TYPE_IDS.RECOVER_PASSWORD');
        $data = $checkActionType ?  ['link' =>  env('APP_URL') . ':4200' . '/update-password?hash=' . $hash . '&email=' . $email] : ['code' => $hash];

        Mail::send($checkActionType ? 'password' : 'otp', $data, function ($message) use ($email, $checkActionType) {
            $message->to($email)->subject($checkActionType ? 'პაროლის აღდგენა GCRT' : 'ავტორიზაციის კოდი GCRT');
        });

        return true;
    }
}

class PhoneService
{
    public function send()
    {
    }
}
