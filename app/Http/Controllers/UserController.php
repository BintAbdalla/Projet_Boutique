<?php

namespace App\Http\Controllers;

use App\Enums\StateEnums;
use App\Http\Requests\StoreUserRequests; // Importez le StoreUserRequests
use App\Models\User;
use App\Models\Role; // Importez le modèle Role
use Illuminate\Support\Facades\Hash;
use App\Traits\Responsetrait;
use Illuminate\Http\Request;

use function Laravel\Prompts\password;

class UserController extends Controller
{
    use Responsetrait;

    public function create(StoreUserRequests $request)
    {
        $validated = $request->validated();

        // Assurez-vous que le rôle existe
        $role = Role::where("role", $validated['role'])->first();

        // dd($role);
        if (!$role) {
            return $this->errorResponse([], 'Le rôle sélectionné est invalide.', 422);
        }


        $user = User::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'login' => $validated['login'],
            'password' => Hash::make($validated['password']),
            'role_id' => $role["id"],
        ]);

        return $this->sendResponse($user, StateEnums::SUCCESS, 'User created successfully', 201);
    }


    public function update(StoreUserRequests $request, User $user)
    {
        $validated = $request->validated();
    
        if (isset($validated['role'])) {
            $role = Role::find($validated['role']);
            if (!$role) {
                return $this->errorResponse([], 'Le rôle sélectionné est invalide.', 422);
            }
        }

        $user->update($request->only(['nom', 'prenom', 'login', 'role_id'])); 

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
            $user->save();
        }

        return $this->successResponse($user, 'User updated successfully', 201);
    }

    public function show(User $user)
    {
        return $this->successResponse($user, 'User retrieved successfully', 200);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return $this->successResponse(null, 'User deleted successfully');
    }
}
