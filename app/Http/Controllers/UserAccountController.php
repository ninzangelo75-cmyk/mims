<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $users = User::orderBy('fullname')
            ->paginate(10)
            ->through(function (User $user) {
                return [
                    'useid' => $user->useid,
                    'fullname' => $user->fullname,
                    'username' => $user->username,
                    'role' => $user->role,
                    'department' => $user->department,
                    'designation' => $user->designation,
                    'emailadd' => $user->emailadd,
                    'contact' => $user->contact,
                    'created_at' => $user->created_at?->format('Y-m-d'),
                ];
            });

        return Inertia::render('Account/Index', [
            'users' => $users,
            'roles' => ['ADMIN', 'STAFF', 'USER'],
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $data = $request->validate([
            'fullname' => ['required', 'string', 'max:200'],
            'username' => ['required', 'string', 'max:100', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:ADMIN,STAFF,USER'],
            'department' => ['nullable', 'string', 'max:150'],
            'designation' => ['nullable', 'string', 'max:150'],
            'emailadd' => ['nullable', 'email', 'max:150', 'unique:users,emailadd'],
            'contact' => ['nullable', 'string', 'max:50'],
        ]);

        User::create([
            'fullname' => $data['fullname'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'department' => $data['department'] ?? null,
            'designation' => $data['designation'] ?? null,
            'emailadd' => $data['emailadd'] ?? null,
            'contact' => $data['contact'] ?? null,
        ]);

        return redirect()->route('account.index')->with('message', 'User created successfully.');
    }
}

