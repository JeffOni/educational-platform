<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->paginate(10);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users
        ]);
    }

    public function create()
    {
        $roles = Role::all();

        return Inertia::render('Admin/Users/Create', [
            'roles' => $roles
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ];

        // Admin siempre crea estudiantes internos
        if ($request->role === 'student') {
            $data['student_type'] = 'internal';
        }

        $user = User::create($data);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado correctamente');
    }

    public function edit(User $user)
    {
        $roles = Role::all();

        return Inertia::render('Admin/Users/Edit', [
            'user' => $user->load('roles'),
            'roles' => $roles
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|exists:roles,name',
            'student_type' => 'nullable|in:external,internal',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Solo agregar student_type si el rol es student
        if ($request->role === 'student' && $request->filled('student_type')) {
            $data['student_type'] = $request->student_type;
        }

        $user->update($data);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user->update([
                'password' => bcrypt($request->password),
            ]);
        }

        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado correctamente');
    }

    public function destroy(User $user)
    {
        // Prevenir que un admin se elimine a sí mismo
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado correctamente');
    }
}
