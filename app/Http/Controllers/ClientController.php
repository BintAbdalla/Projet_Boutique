<?php

 namespace App\Http\Controllers;

 use App\Enums\StateEnums;
 use App\Http\Requests\StoreClientRequests;
use App\Http\Resources\ClientCollection;
 use App\Http\Resources\ClientResource;
 use App\Models\Client;
 use App\Models\User;
 use App\Traits\Responsetrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
 use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;
use Prettus\Validator\Exceptions\ValidatorException;
 use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Role; // Importez le modèle Role
use App\Models\Dettes;



 class ClientController extends Controller
 {
    use Responsetrait;
//     /**
//      * Display a listing of the resource.
//      */
    public function index(Request $request)
    {
      //  return Client::whereNotNull('user_id')->get();
         $include = $request->has('include')?  [$request->input('include')] : [];

         $data = Client::with($include)->whereNotNull('user_id')->get();
     
        $clients = QueryBuilder::for(Client::class)
            ->allowedFilters(['surname'])
            ->allowedIncludes(['user'])
            ->get();
       return new ClientCollection($clients);
    }

//     /**
//      * Store a newly created resource in storage.
//      */
     public function store(StoreClientRequests $request)
    
    {
       $this->authorize('create', Client::class);
        try {
// //             $validatedData = $rebc quest->validated();
// // dd($validatedData); // Vérifiez si les données sont bien structurées


//             //   Assurez-vous que le rôle existe
             $role = Role::where("role", $request['role'])->first();

       
        
     DB::beginTransaction();
         $clientRequest =  $request->only('surname','adresse','telephone');
          dd($clientRequest);
         $client= Client::create($clientRequest);
//         // dd($client);
        
//         // dd($client);
         if ( $request->has('user')){
             $role=$request->input('user.role','client');
             if (!$role) {
                 throw new Exception('Role not found' . $role);
             }
             $roledefault=Role::where("role", $role)->first();
             $user = User::create([
                     'nom' => $request->input('user.nom'),
                     'prenom' => $request->input('user.prenom'),
                     'login' => $request->input('user.login'),
                     'password' => Hash::make($request->input('user.password')), 
                     'role_id' => $roledefault["id"],
                 ]);

                 $user->client()->save($client);
                
          }
//             // dd($client);
        DB::commit();
          return $this->sendResponse(new ClientResource($client));

       }catch (Exception $e){
           DB::rollBack();
          return $this->sendResponse(['error'=>$e->getMessage()],StateEnums::ECHEC);}
    

       }
    

//     /**
//      * Display the specified resource.
//      */
    public function filterByTelephone(Request $request)
     {
//         // Récupère le numéro de téléphone depuis la requête
      $telephone = $request->input('telephone');

//         // Vérifie si le téléphone est fourni
      if (empty($telephone)) {
           return response()->json([
               'status' => 'error',
                'message' => 'Le numéro de téléphone est requis.',
             ], 422);
         }

//         // Rechercher les clients par numéro de téléphone en utilisant le Query Builder
      $clients = DB::table('clients')
           ->where('telephone', $telephone)
          ->get();

//         // Retourner les clients trouvés
        return response()->json([
           'status' => 'success',
           'data' => $clients,
           'message' => $clients->isEmpty() ? 'Aucun client trouvé pour ce numéro de téléphone.' : 'Clients récupérés avec succès.',
       ], 200);
   }

   

//         /**
//          * Récupère les dettes d'un client spécifique.
//          */
        
        public function getDettes(Request $request, $id)
        {
            // Récupérer les dettes du client avec l'ID donné
            $dettes = Dettes::where('client_id', $id)->get();
    
           // Vérifiez si des dettes existent pour le client
            if ($dettes->isEmpty()) {
               return response()->json([
                   'message' => 'Aucune dette trouvée pour ce client.'
                ], 404);  

                }
    
//             // Retourner les dettes dans une réponse JSON
   return response()->json([
                 'client_id' => $id,
                 'dettes' => $dettes
             ], 200);
       
     }

         public function show($id,$client)
         {

             $this->authorize('view', $client);
             $client = Client::find($id);
    
             if (!$client) {
                 return response()->json([
                     'message' => 'Client non trouvé.'
                 ], 404);
             }
    
             return response()->json($client, 200);
         }



       public function getUserForClient($id)
       {
            // Récupérer le client avec l'utilisateur associé
            $client = Client::with('user')->find($id);
        
             // Vérifiez si le client existe
             if (!$client) {
                 return response()->json([
                     'message' => 'Client non trouvé.'
                 ], 404);
             }
        
             // Retourner l'utilisateur du client
             return response()->json([
                 'client_id' => $client->id,
                 'user' => $client->user
             ], 200);
         }
        



  }
    


