<?php

namespace App\Http\Controllers;

use App\Models\Attr;
use  App\Models\User;
use Illuminate\Http\Request;
use App\Models\UserPermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function list()
    {
        return response()->json([
            'code' => 1,
            'message' => 'success',
            'data' => User::with('permissions')->get()
        ]);
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

    public function details(Request $request)
    {
        $userID = intval($request->route('user_id'));
        $user = User::where('id', $userID)->with(['permissions'])->first();

        if (is_null($user)) {
            return response()->json(['code' => 0, 'message' => 'მომხმარებელი ვერ მოიძებნა']);
        }

        return response()->json($user);
    }

    public function add(Request $request)
    {
        $data = $request->only([
            'name',
            'lastname',
            'phone',
            'email',
            'password'
        ]);

        $validator = Validator::make($data, [
            'name' => 'required',
            'lastname' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        $user = User::where('email', $data['email'])->first();

        if ($user) {
            return response()->json([
                'code' => 0,
                'message' => 'მომხმარებელი ასეთი ელ-ფოსტით უკვე არსებობს'
            ], 400);
        }

        $data['password'] = Hash::make($data    ['password']);
        $data['otp_enabled'] = true;
        $user = User::create($data);
        $createPermissions = $this->createPermissions($user->id);

        return response()->json([
            'code' => 1,
            'message' => 'სისტემის მომხმარებელი ' . $user->email . ' წარმატებით დაემატა',
        ]);
    }

    public function edit(Request $request)
    {
        $userID = intval($request->route('user_id'));
        $data = $request->only([
            'email',
            'name',
            'lastname',
            'address',
            'phone',
        ]);
        $user = User::where('id', $userID)->first();

        if (is_null($user)) {
            return response()->json([
                'code' => 0,
                'message' => 'მომხმარებელი ვერ მოიძებნა',
            ]);
        }

        if (count($data) <= 0) {
            return response()->json([
                'code' => 0,
                'message' => 'არასაკმარისი მონაცემები',
            ]);
        }

        if ($user->update($data)) {
            return response()->json([
                'code' => 1,
                'message' => 'სისტემის მომხმარებლის ' . $user->email . '-ის რედაქტირება წარმატებით დასრულდა'
            ]);
        }

        return response()->json([
            'code' => 0,
            'message' => 'ოპერაციის შესრულებისას მოხდა შეცდომა',
        ]);;
    }

    public function changePassword(Request $request)
    {
        $data = $request->only([
            'currentPassword',
            'newPassword',
            'confirmPassword',
        ]);

        $userID = Auth::id();
        $user = User::where('id', $userID)->first();

        if (is_null($user)) {
            return response()->json([
                'code' => 0,
                'message' => 'მომხმარებელი ვერ მოიძებნა'
            ], 500);
        }

        if (!Hash::check($request->get('currentPassword'), $user->password)) {
            return response()->json([
                'code' => 0,
                'message' => 'არსებული პაროლი არასწორია'
            ], 500);
        }

        $validator = Validator::make($data, [
            'currentPassword' => 'required',
            'newPassword' => 'required',
            'confirmPassword' => 'required',
        ]);

        if ($request->get('newPassword') != $request->get('confirmPassword')) {
            return response()->json([
                'code' => 0,
                'message' => 'პაროლები არ ემთხვევა'
            ], 500);
        }

        if ($validator->fails()) {
            return $validator->errors();
        }

        if ($user->update(['password' => Hash::make($request->get('newPassword'))])) {
            return response()->json([
                'code' => 1,
                'message' => 'ოპერაცია წარმატებით დასრულდა'
            ]);
        }

        return response()->json([
            'code' => 0,
            'message' => 'ოპერაციის შესრულების დროს მოხდა შეცდომა'
        ], 500);
    }


    public function updatePassword(Request $request)
    {
        $data = $request->only([
            'email',
            'password',
            'password_confirmation',
        ]);

        $validator = Validator::make($data, [
            'email' => 'required',
            'password' => 'required',
            'password_confirmation' => 'required',
        ]);

        $user = User::where('email', $data['email'])->first();



        if (is_null($user)) {
            return response()->json(['code' => 0, 'message' => 'მომხმარებელი ვერ მოიძებნა'], 400);
        }

        if ($data['password'] != $data['password_confirmation']) {
            return response()->json(['code' => 0, 'message' => 'პაროლები არ ემთხვევა ერთმანეთს'], 400);
        }

        if ($validator->fails()) {
            return $validator->errors();
        }

        if (!$user->update(['password' => Hash::make($data['password'])])) {
            return response()->json(['code' => 0, 'message' => 'დაფიქსირდა შეცდომა'], 400);
        }

        return response()->json(['code' => 1, 'message' => 'ოპერაცია წარმატებით დასრულდა']);
    }

    public function delete(Request $request)
    {
        $userID = intval($request->route('user_id'));
        $user = User::find($userID);
        $userPermissions = UserPermission::where('user_id', $userID);

        if ($user === null) {
            return response()->json(['მომხმარებელი ვერ მოიძებნა'], 400);
        }

        if ($user->delete() && $userPermissions->delete()) {
            return response()->json(['ოპერაცია წარმატებით დასრულდა'], 200);
        }

        return response()->json(['ოპერაციის შესრულებისას მოხდა შეცდომა'], 500);
    }


    public function updateBooleanProperties(Request $request)
    {
        $userID = intval($request->route('user_id'));
        $statusID = (bool) intval($request->status_id);
        $otpEnabled = (bool) intval($request->otp_enabled);

        $user = User::find($userID);

        if (is_null($user)) return response()->json(['code' => 0, 'message' => 'მომხმარებელი ვერ მოიძებნა'], 400);

        $user->update(['status_id' => $statusID, 'otp_enabled' => $otpEnabled]);

        return response()->json(['code' => 1, 'message' => 'ოპერაცია წარმატებით დასრულდა']);
    }

    // ATTR PERMISSIONS
    public function updatePermission(Request $request)
    {
        $userID = intval($request->route('user_id'));
        $attrID = intval($request->route('attr_id'));
        $permissionType = config('settings.PERMISSION_TYPES')[intval($request->permission_type)];
        $permissionValue = (bool) $request->permission_value;

        $values = ['user_id' => $userID, 'attr_id' => $attrID];
        $userPermission  = UserPermission::where($values)->first();
        $values[$permissionType] =  $permissionValue; // append permission type ID+ new value

        if (is_null($userPermission)) {
            $userPermission = UserPermission::create($values);
        } else {
            $userPermission->update($values);
        };

        if (is_null($userPermission)) {
            return response()->json([
                'code' => 0,
                'message' => 'მომხმარებლის უფლებების ცვლილებისას დაფიქსირდა შეცდომა',
            ]);
        };

        return response()->json([
            'code' => 1,
            'message' => 'ოპერაცია წარმატებით დასრულდა',
            'data' => $userPermission
        ]);
    }

    private function createPermissions(int $userID): bool
    {
        $attrIDS = Attr::select('id')->get()->pluck('id');
        $permissions = [];

        foreach ($attrIDS as $attrID) {
            $permissions[] =  ['user_id' => $userID, 'attr_id' => $attrID, 'can_view' => false, 'can_update' => false, 'can_delete' => false, 'can_edit_structure' => false];
        }

        return UserPermission::insert($permissions);
    }
}
