<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->with('roles');
        if ($search = $request->input('q')) {
            $query->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
        }

        $users = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $allowed = ['Super Admin','Manager','Controller','Operator','Monitoring'];
        $roles = Role::whereIn('name', $allowed)->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            $data['password'] = \Illuminate\Support\Str::random(12);
        }

        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : true;

        $user = User::create($data);

        // Support both roles[] (array) and single role selection (role)
        if (! empty($data['roles'])) {
            $user->roles()->sync($data['roles']);
        } elseif ($request->input('role')) {
            $user->roles()->sync([$request->input('role')]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created.');
    }

    public function show(User $user)
    {
        $user->load('roles');
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $allowed = ['Super Admin','Manager','Controller','Operator','Monitoring'];
        $roles = Role::whereIn('name', $allowed)->get();
        $user->load('roles');
        return view('admin.users.edit', compact('user','roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : true;

        $user->update($data);

        $user->roles()->sync($data['roles'] ?? ( $request->input('role') ? [$request->input('role')] : [] ));

        return redirect()->route('admin.users.index')->with('success', 'User updated.');
    }

    public function destroy(Request $request, User $user)
    {
        // Prevent deleting self
        if ($request->user()->id === $user->id) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        // Prevent deleting last admin
        if ($user->isAdmin()) {
            $adminsCount = User::where('is_admin', true)
                ->orWhereHas('roles', function($q){ $q->where('name', 'Super Admin'); })
                ->count();
            if ($adminsCount <= 1) {
                return back()->with('error', 'Cannot delete the last administrator.');
            }
        }

        $user->roles()->detach();
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}
