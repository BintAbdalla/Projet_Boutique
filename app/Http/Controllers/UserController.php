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
use App\Services\UploadService;
use Illuminate\Support\Facades\Storage;
use App\Services\CloudUploadService;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\Paginator;

use App\Jobs\PhotoJob; // Assuming PhotoJob is defined in your app's Jobs folder


use function Laravel\Prompts\password;

class UserController extends Controller
{
    use Responsetrait;

    // protected $uploadService;
    // protected $CouduploadService;
    protected $CloudUploadService;

    public function __construct(CloudUploadService $CloudUploadService)
    {
        $this->CloudUploadService = $CloudUploadService;
    }

    public function create(StoreUserRequests $request)
    {
        // Valider les données
        $validated = $request->validated();
        
        // Assurez-vous que le rôle existe
        $role = null;
        if (!empty($validated['role'])) {
            $role = Role::where("role", $validated['role'])->first();
            if (!$role) {
                return $this->errorResponse([], 'Le rôle sélectionné est invalide.', 422);
            }
        }
        
        // Créer l'utilisateur sans la photo pour l'instant
        $user = User::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'login' => $validated['login'],
            'password' => Hash::make($validated['password']),
            'role_id' => $role ? $role->id : null,
            'etat' => $validated['etat'],
            // La photo sera ajoutée plus tard via le Job
            'filename' => null,
        ]);
    
        // Vérifiez si un fichier photo est présent
        if ($request->hasFile('filename')) {
            $file = $request->file('filename');
    
            // Déboguer les informations du fichier
            // dd($file);
    
            // Sauvegarder le fichier localement dans un répertoire temporaire
            $filePath = $file->storeAs('temp_photos', $file->getClientOriginalName(), 'public');
            
            // Vérifiez si le fichier a été bien sauvegardé
            // dd($filePath);
    // 
            // Déclenchement du Job avec le chemin du fichier et l'ID de l'utilisateur
            PhotoJob::dispatch($filePath, $user->id);
    
        }
    
        // Retourner l'utilisateur créé
        
        return response()->json($user, 201);
    }
    
    public function createUserForClient(StoreUserRequests $request, $Id)
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
        // Valider les données du formulaire
        $validated = $request->validated();
    
        // Vérifier si le rôle est défini et valide
        if (isset($validated['role'])) {
            $role = Role::find($validated['role']);
            if (!$role) {
                return $this->sendResponse(null, StateEnums::ECHEC, 'Le rôle sélectionné est invalide.', 422);
            }
            $role_id = $role->id;
        } else {
            $role_id = $user->role_id;
        }
    
        // Mettre à jour les informations de l'utilisateur
        $user->update([
            'nom' => $validated['nom'] ?? $user->nom,
            'prenom' => $validated['prenom'] ?? $user->prenom,
            'login' => $validated['login'] ?? $user->login,
            'role_id' => $role_id,
        ]);
    
        // Mettre à jour le mot de passe si fourni
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
            $user->save();
        }
    
        // Retourner une réponse de succès
        return $this->sendResponse($user, StateEnums::SUCCESS, 'Utilisateur mis à jour avec succès.', 200);
    }
    
    
    public function filterUsersByRole(Request $request)
    {
        $role = $request->query('role');

        // Vérifiez si le rôle est fourni
        if (!$role) {
            return $this->sendResponse(null, StateEnums::ECHEC, 'Le rôle est requis.', 400);
        }

        // Rechercher les utilisateurs par rôle
        $users = User::whereHas('role', function ($query) use ($role) {
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
            $query->whereHas('role', function ($query) use ($role) {
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


    public function filterUsersByState(Request $request)
    {

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
        // Supprimer l'utilisateur
        $user->delete();
    
        // Retourner une réponse de succès en utilisant sendResponse
        return $this->sendResponse(null, StateEnums::SUCCESS, 'User deleted successfully', 200);
    }
    


    public function show($id)
{
    // Rechercher l'utilisateur par ID
    $user = User::find($id);

    // Vérifiez si l'utilisateur existe
    if (!$user) {
        return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
    }

    // Retourner les détails de l'utilisateur
    return response()->json($user);
}

public function index(Request $request)
{
    // Récupérer les utilisateurs avec pagination
    $users = User::paginate(5);

    // Déboguer les utilisateurs paginés
    Log::info('Users:', $users->toArray());

    // Retourner les utilisateurs avec pagination
    return response()->json($users);
}



}
