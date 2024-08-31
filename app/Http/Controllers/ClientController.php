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
// use Prettus\Validator\Exceptions\ValidatorException;
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Role; // Importez le modèle Role



class ClientController extends Controller
{
    use Responsetrait;
    /**
     * Display a listing of the resource.
     */
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequests $request)
    {
        try {
//             $validatedData = $rebc quest->validated();
// dd($validatedData); // Vérifiez si les données sont bien structurées


            //   Assurez-vous que le rôle existe
        // $role = Role::where("role", $request['role'])->first();

       
        
        DB::beginTransaction();
        $clientRequest =  $request->only('surname','adresse','telephone');
        // dd($clientRequest);
        $client= Client::create($clientRequest);
        // dd($client);
        
        // dd($client);
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
                    'password' => Hash::make($request->input('user.password')), // Correction ici
                    'role_id' => $roledefault["id"],
                ]);

                $user->client()->save($client);
                
            }
            // dd($client);
            DB::commit();
            return $this->sendResponse(new ClientResource($client));

        }catch (Exception $e){
            DB::rollBack();
           return $this->sendResponse(['error'=>$e->getMessage()],StateEnums::ECHEC);
    }




    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }



}
