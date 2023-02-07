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

    public function __construct()
    {
        // $this->middleware('auth:api');
    }

    public function list()
    {
        return response()->json(User::with('permissions')->get());
    }

    public function details(Request $request)
    {
        $userID = intval($request->route('user_id'));
        $user = User::where('id', $userID)->with(['permissions'])->first();

        if ($user === null) {
            return response()->json(['მომხმარებელი ვერ მოიძებნა']);
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

        if ($user != null) {
            return response()->json(['StatusMessage' => 'მომხმარებელი ასეთი ელ-ფოსტით უკვე არსებობს'], 400);
        }

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        $createPermissions = $this->createPermissions($user->id);

        if (!$createPermissions) {
            return response()->json([
                'code' => 0,
                'message' => 'მომხმარებლის უფლებების შენახვისას დაფიქსირდა შეცდომა'
            ]);
        }

        return response()->json([
            'code' => 1,
            'message' => 'სისტემის მომხმარებელი ' . $user->email . ' წარმატებით დაემატა',
            'data' => $user
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
                'message' => 'სისტემის მომხმარებლის ' . $user->email . '-ის რედაქტირება წარმატებით დასრულდა',
                'data' => $user
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

        if ($user === null) {
            return response()->json(['მომხმარებელი ვერ მოიძებნა']);
        }

        if (!Hash::check($request->get('currentPassword'), $user->password)) {
            return response()->json(['StatusMessage' => 'არსებული პაროლი არასწორია'], 500);
        }

        $validator = Validator::make($data, [
            'currentPassword' => 'required',
            'newPassword' => 'required',
            'confirmPassword' => 'required',
        ]);

        if ($request->get('newPassword') != $request->get('confirmPassword')) {
            return response()->json(['StatusMessage' => 'პაროლები არ ემთხვევა'], 500);
        }

        if ($validator->fails()) {
            return $validator->errors();
        }

        if ($user->update(['password' => Hash::make($request->get('newPassword'))])) {
            return response()->json(['StatusMessage' => 'ოპერაცია წარმატებით დასრულდა']);
        }

        return response()->json(['StatusMessage' => 'ოპერაციის შესრულების დროს მოხდა შეცდომა'], 500);
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
        $user = User::where('id', $userID)->first();
        $userPermissions = UserPermission::where('user_id', $userID);

        if ($user === null) {
            return response()->json(['მომხმარებელი ვერ მოიძებნა'], 400);
        }

        if ($user->delete() && $userPermissions->delete()) {
            return response()->json(['ოპერაცია წარმატებით დასრულდა'], 200);
        }

        return response()->json(['ოპერაციის შესრულებისას მოხდა შეცდომა'], 500);
    }


    public function updateStatusID(Request $request)
    {
        $userID = intval($request->route('user_id'));
        $statusID = (bool) intval($request->route('status_id'));

        $user = User::where('id', $userID)->first();

        if (is_null($user)) return response()->json(['მომხმარებელი ვერ მოიძებნა'], 400);

        $user->update(['status_id' => $statusID]);

        return response()->json(['ოპერაცია წარმატებით დასრულდა']);
    }

    // ATTR PERMISSIONS
    public function savePermission(Request $request)
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
            $permissions[] =  ['user_id' => $userID, 'attr_id' => $attrID, 'update' => false, 'delete' => false, 'structure' => false];
        }

        return UserPermission::insert($permissions);
    }
}
