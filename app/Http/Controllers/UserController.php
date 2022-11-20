<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use  App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function list()
    {
        return User::all();
    }

    public function details(Request $request)
    {
        $userID = intval($request->route('user_id'));
        $user = User::where('id', $userID)->first();

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

        return response()->json(User::create($data));
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

        // dd($user);
        // return response()->json($user);

        if ($user === null) {
            return response()->json(['მომხმარებელი ვერ მოიძებნა']);
        }

        if (count($data) <= 0) {
            return response()->json(['არასაკმარისი მონაცემები']);
        }

        if ($user->update($data)) {
            return response()->json(['ოპერაცია წარმატებით დასრულდა']);
        }

        return response()->json(['ოპერაციის შესრულებისას მოხდა შეცდომა']);
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

    public function delete(Request $request)
    {
        $userID = intval($request->route('user_id'));
        $user = User::where('id', $userID)->first();

        if ($user === null) {
            return response()->json(['მომხმარებელი ვერ მოიძებნა'], 400);
        }

        if ($user->delete()) {
            return response()->json(['ოპერაცია წარმატებით დასრულდა'], 200);
        }

        return response()->json(['ოპერაციის შესრულებისას მოხდა შეცდომა'], 500);
    }
}
