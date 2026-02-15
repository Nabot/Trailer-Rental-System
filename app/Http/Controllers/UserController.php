<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::with('roles')->latest();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        $users = $query->paginate(15);

        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $this->authorize('create', User::class);

        $roles = Role::all();
        $modules = $this->getModules();

        return view('users.create', compact('roles', 'modules'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,name'],
            'modules' => ['array'],
            'modules.*' => ['string'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Assign role
        $user->assignRole($validated['role']);

        // Assign module permissions
        if ($request->filled('modules')) {
            $this->assignModulePermissions($user, $request->modules);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);

        $user->load('roles', 'permissions');
        $modules = $this->getModules();
        $userModulePermissions = $this->getUserModulePermissions($user);

        return view('users.show', compact('user', 'modules', 'userModulePermissions'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);

        $roles = Role::all();
        $modules = $this->getModules();
        $userModulePermissions = $this->getUserModulePermissions($user);

        return view('users.edit', compact('user', 'roles', 'modules', 'userModulePermissions'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,name'],
            'modules' => ['array'],
            'modules.*' => ['string'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Update password if provided
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        // Sync role
        $user->syncRoles([$validated['role']]);

        // Sync module permissions
        $this->syncModulePermissions($user, $request->modules ?? []);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        // Prevent deleting yourself
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Get available modules with their permissions.
     */
    private function getModules()
    {
        return [
            'trailers' => [
                'name' => 'Trailers',
                'permissions' => ['trailers.view', 'trailers.create', 'trailers.edit', 'trailers.delete'],
            ],
            'bookings' => [
                'name' => 'Bookings',
                'permissions' => ['bookings.view', 'bookings.create', 'bookings.edit', 'bookings.delete', 'bookings.confirm', 'bookings.cancel'],
            ],
            'inspections' => [
                'name' => 'Inspections',
                'permissions' => ['inspections.view', 'inspections.create', 'inspections.edit'],
            ],
            'customers' => [
                'name' => 'Customers',
                'permissions' => ['customers.view', 'customers.create', 'customers.edit', 'customers.delete'],
            ],
            'leads' => [
                'name' => 'Leads',
                'permissions' => ['inquiries.view', 'inquiries.create', 'inquiries.edit', 'inquiries.delete'],
            ],
            'invoices' => [
                'name' => 'Invoices',
                'permissions' => ['invoices.view', 'invoices.create', 'invoices.edit'],
            ],
            'quotes' => [
                'name' => 'Quotes',
                'permissions' => ['quotes.view', 'quotes.create', 'quotes.edit', 'quotes.delete'],
            ],
            'payments' => [
                'name' => 'Payments',
                'permissions' => ['payments.view', 'payments.create', 'payments.edit'],
            ],
            'reports' => [
                'name' => 'Reports',
                'permissions' => ['reports.view'],
            ],
            'settings' => [
                'name' => 'Settings',
                'permissions' => ['settings.manage'],
            ],
        ];
    }

    /**
     * Get user's module permissions (effective: role + direct).
     */
    private function getUserModulePermissions(User $user)
    {
        $modules = $this->getModules();
        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
        $userModulePermissions = [];

        foreach ($modules as $moduleKey => $module) {
            $hasAccess = false;
            foreach ($module['permissions'] as $permission) {
                if (in_array($permission, $userPermissions)) {
                    $hasAccess = true;
                    break;
                }
            }
            $userModulePermissions[$moduleKey] = $hasAccess;
        }

        return $userModulePermissions;
    }

    /**
     * Assign module permissions to user.
     */
    private function assignModulePermissions(User $user, array $modules)
    {
        $allModules = $this->getModules();
        $permissionsToAssign = [];

        foreach ($modules as $moduleKey) {
            if (isset($allModules[$moduleKey])) {
                $permissionsToAssign = array_merge($permissionsToAssign, $allModules[$moduleKey]['permissions']);
            }
        }

        // Get existing permissions from role
        $rolePermissions = $user->getPermissionsViaRoles()->pluck('name')->toArray();
        
        // Only assign permissions not already granted by role
        $permissionsToAssign = array_diff($permissionsToAssign, $rolePermissions);
        
        if (!empty($permissionsToAssign)) {
            $permissions = Permission::whereIn('name', $permissionsToAssign)->get();
            $user->givePermissionTo($permissions);
        }
    }

    /**
     * Sync module permissions for user.
     */
    private function syncModulePermissions(User $user, array $selectedModules)
    {
        $allModules = $this->getModules();
        $allModulePermissions = [];
        
        // Collect all permissions from all modules
        foreach ($allModules as $module) {
            $allModulePermissions = array_merge($allModulePermissions, $module['permissions']);
        }

        // Get permissions that should be assigned
        $permissionsToAssign = [];
        foreach ($selectedModules as $moduleKey) {
            if (isset($allModules[$moduleKey])) {
                $permissionsToAssign = array_merge($permissionsToAssign, $allModules[$moduleKey]['permissions']);
            }
        }

        // Get existing permissions from role
        $rolePermissions = $user->getPermissionsViaRoles()->pluck('name')->toArray();
        
        // Revoke all module permissions first (except role permissions)
        $userPermissions = $user->permissions->pluck('name')->toArray();
        $permissionsToRevoke = array_diff($userPermissions, $rolePermissions);
        $permissionsToRevoke = array_intersect($permissionsToRevoke, $allModulePermissions);
        
        if (!empty($permissionsToRevoke)) {
            $permissions = Permission::whereIn('name', $permissionsToRevoke)->get();
            $user->revokePermissionTo($permissions);
        }

        // Assign new permissions (excluding role permissions)
        $permissionsToAssign = array_diff($permissionsToAssign, $rolePermissions);
        
        if (!empty($permissionsToAssign)) {
            $permissions = Permission::whereIn('name', $permissionsToAssign)->get();
            $user->givePermissionTo($permissions);
        }
    }
}
