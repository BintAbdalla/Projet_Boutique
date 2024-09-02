<?php

namespace App\Http\Controllers;

use App\Enums\StateEnums;
use App\Http\Requests\StoreUserRequests; // Importez le StoreUserRequests
use App\Models\User;
use App\Models\Role; // Importez le modèle Role
use Illuminate\Support\Facades\Hash;
use App\Traits\Responsetrait;
use Illuminate\Http\Request;
use App\Models\Client;

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
            'etat' => $validated['etat'],
        ]);

        return $this->sendResponse($user, StateEnums::SUCCESS, 'User created successfully', 201);
    }

    public function createUserForClient(StoreUserRequests $request ,$Id)
{
    $validated = $request->validated();

    // Assurez-vous que le rôle existe
    $role = Role::where("role", $validated['role'])->first();

    if (!$role) {
        return $this->errorResponse([], 'Le rôle sélectionné est invalide.', 422);
    }

    // Récupérer le client par ID
    $client = Client::find($Id);

  
    if (!$client) {
        return $this->sendResponse([], StateEnums::ECHEC, 404);
    }

    // Vérifier si ce client a déjà un utilisateur
    if ($client->user) {
        return $this->sendResponse([], StateEnums::ECHEC, 422);
    }



    // Créer l'utilisateur
    $user = User::create([
        'nom' => $validated['nom'],
        'prenom' => $validated['prenom'],
        'login' => $validated['login'],
        'password' => Hash::make($validated['password']),
        'role_id' => $role->id,
        'etat' => $validated['etat'],
        'client_id' => $client->id,
    ]);

    // Associer l'utilisateur au client
    $client->update(['user_id' => $user->id]);

    return $this->sendResponse($user, StateEnums::SUCCESS, 'Utilisateur créé et associé au client avec succès', 201);
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
    public function filterUsersByRole(Request $request)
    {
        $role = $request->query('role');
    
        // Vérifiez si le rôle est fourni
        if (!$role) {
            return $this->sendResponse(null, StateEnums::ECHEC, 'Le rôle est requis.', 400);
        }
    
        // Rechercher les utilisateurs par rôle
        $users = User::whereHas('role', function($query) use ($role) {
            $query->where('role', $role);
        })->get();
    
        // Si aucun utilisateur n'est trouvé, retourner une liste vide
        if ($users->isEmpty()) {
            return $this->sendResponse(null, StateEnums::SUCCESS, 'Aucun utilisateur trouvé pour ce rôle.', 200);
        }
    
        // Retourner la liste des utilisateurs filtrés
        return $this->sendResponse($users, StateEnums::SUCCESS, 'Liste des utilisateurs récupérés avec succès.', 200);
    }
    
    public function filterUsersByRoleAndState(Request $request)
    {
        // Récupérer les paramètres de la requête
        $role = $request->query('role');
        $state = $request->query('etat'); // Utilisez 'etat' comme clé pour filtrer
    
        // Construire la requête en fonction des paramètres fournis
        $query = User::query();
    
        if ($role) {
            $query->whereHas('role', function($query) use ($role) {
                $query->where('role', $role);
            });
        }
    
        if ($state) {
            $query->where('etat', $state); // Filtrer par 'etat'
        }
    
        // Exécuter la requête et récupérer les utilisateurs
        $users = $query->get();
    
        // Vérifier si la liste est vide
        if ($users->isEmpty()) {
            return $this->sendResponse(null, StateEnums::SUCCESS, 'Aucun utilisateur trouvé pour ces critères.', 200);
        }
    
        // Retourner la liste des utilisateurs filtrés
        return $this->sendResponse($users, StateEnums::SUCCESS, 'Liste des utilisateurs récupérés avec succès.', 200);
    }
    
    
  public function filterUsersByState(Request $request){
   
        // Récupérer les paramètres de la requête
        
        $state = $request->query('etat'); // Utilisez 'etat' comme clé pour filtrer
    
        // Construire la requête en fonction des paramètres fournis
        $query = User::query();
    
     
    
        if ($state) {
            $query->where('etat', $state); // Filtrer par 'etat'
        }
    
        // Exécuter la requête et récupérer les utilisateurs
        $users = $query->get();
    
        // Vérifier si la liste est vide
        if ($users->isEmpty()) {
            return $this->sendResponse(null, StateEnums::SUCCESS, 'Aucun utilisateur trouvé pour ces critères.', 200);
        }
    
        // Retourner la liste des utilisateurs filtrés
        return $this->sendResponse($users, StateEnums::SUCCESS, 'Liste des utilisateurs récupérés avec succès.', 200);
    }



    
    public function destroy(User $user)
    {
        $user->delete();

        return $this->successResponse(null, 'User deleted successfully');
    }
}
