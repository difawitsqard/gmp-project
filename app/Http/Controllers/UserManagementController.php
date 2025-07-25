<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{

    protected $user;

    public function __construct()
    {
        $this->user = auth()->user();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $user = auth()->user();
        // add role
        // $user->assignRole('Admin');
        // lepas role
        // $user->removeRole('Manajer Stok');

        $perPage = is_numeric($request->perPage) ? $request->perPage :  10;

        if ($this->user->hasRole('Admin')) {
            // Admin hanya melihat user dengan role 'Pelanggan'
            $users = User::whereHas('roles', function ($query) {
                $query->where('name', 'Pelanggan');
            });
        } elseif ($this->user->hasRole('Manajer Stok')) {
            // Manajer Stok hanya melihat user dengan role 'Manajer Stok' atau 'Staff Gudang'
            $users = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['Manajer Stok', 'Staff Gudang']);
            });
        } else {
            abort(403);
        }

        $users = $users->with(['roles:id,name', 'permissions'])
            ->Filter()
            ->Sorting()
            ->paginate($perPage);

        // $currentUserId = auth()->id();
        // $users->setCollection(
        //     $users->getCollection()->filter(function ($user) use ($currentUserId) {
        //         return $user->id !== $currentUserId;
        //     })
        // );

        // Transform roles to only include names
        $users->getCollection()->transform(function ($user) {
            $user->setRelation('roles', $user->roles->map(function ($role) {
                return $role->name;
            }));
            return $user;
        });

        // $roles = Role::all();

        // Check if JSON response is requested
        if ($request->has('json') && $request->json) {
            return response()->json($users);
        }

        return inertia('users/users-list', [
            'users' => $users,
            // 'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasRole(['Admin', 'Manajer Stok'])) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'code' => 403,
                    'message' => 'Tidak memiliki izin untuk melakukan tindakan ini.',
                ], 403);
            }
            return redirect()->back()->with('error', 'Tidak memiliki izin untuk melakukan tindakan ini.');
        }

        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'role' => [
                'required',
                'exists:roles,name',
            ],
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'name' => $validate['name'],
            'email' => $validate['email'],
            'address' => $validate['address'],
            'phone' => $validate['phone'],
            'password' => bcrypt(str()->random(12)),
            'is_active' => 1,
        ]);

        if ($request->has('role')) {
            $user->assignRole($validate['role']);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => true,
                'code' => 201,
                'message' => "Pengguna '{$user->name}' berhasil dibuat.",
                'data' => $user,
            ], 201);
        }
        return redirect()->back()->with('success', "Pengguna '{$user->name}' berhasil dibuat.");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        $user = User::with(['roles:id,name'])->findOrFail($id);

        // Transform roles to only include names
        $user->setRelation('roles', $user->roles->map(function ($role) {
            return $role->name;
        }));

        if ($request->wantsJson() || $request->has('json')) {
            return response()->json($user);
        }

        return inertia('users/user-detail', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Periksa apakah user adalah Pelanggan dan mencoba mengedit user lain
        if (auth()->user()->hasRole('Pelanggan') && auth()->id() != $id) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'code' => 403,
                    'message' => 'Tidak memiliki izin untuk melakukan tindakan ini.',
                ], 403);
            }
            return redirect()->back()->with('error', 'Tidak memiliki izin untuk melakukan tindakan ini.');
        }

        $user = User::find($id);

        // Jika user sudah verifikasi email dan yang update bukan dirinya sendiri, larang update email
        if ($user->hasVerifiedEmail() && auth()->id() != $user->id) {
            $request->merge(['email' => $user->email]); // Pakai email lama
        }

        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'role' => [
                'required',
                'exists:roles,name',
            ],
            'is_active' => 'boolean',
        ]);

        $emailChanged = $validate['email'] !== $user->email;

        $user->update([
            'name' => $validate['name'],
            'email' => $validate['email'],
            'address' => $validate['address'],
            'phone' => $validate['phone'],
            'is_active' => $validate['is_active'] ? 1 : 0,
        ]);

        if ($request->has('role')) {
            $user->syncRoles($validate['role']);
        }

        // Jika email diubah oleh dirinya sendiri, kirim email verifikasi ulang
        if ($emailChanged && auth()->id() == $user->id) {
            $user->email_verified_at = null;
            $user->save();
            $user->sendEmailVerificationNotification();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => "Pengguna '{$user->name}' berhasil diperbarui.",
                'data' => $user,
            ], 200);
        }
        return redirect()->back()->with('success', "Pengguna '{$user->name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
