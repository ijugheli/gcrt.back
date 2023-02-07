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
        $model->code = $actionType == config('settings.ACTION_TYPE_IDS.RECOVER_PASSWORD') ? sha1(time()) : rand(6, 6);
        $model->action_type = $actionType;
        $model->validation_type = $validationType;
        $model->created_at = $time;
        $model->expires_at = $time->addSeconds(config('settings.CODE_EXPIRY_TIME'));
        if (!$model->save()) {
            return false;
        }


        if (!$this->emailService->send($user->email, $model->code)) {
            return false;
        }

        return true;
    }
}


class EmailService
{
    public function send($email, $hash)
    {
        $data = ['link' =>  env('APP_URL') . ':4200' . '/update-password?hash=' . $hash . '&email=' . $email];
        Mail::send('mail', $data, function ($message) use ($email) {
            $message->to($email)->subject('პაროლის აღდგენა GCRT');
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
