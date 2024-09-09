<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Laravel\Passport\Passport;
use App\Services\AuthServiceInterface;
use App\Traits\Responsetrait;


class AuthController extends Controller

{
    use Responsetrait;
    protected $var;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->var = $authService;
    }


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string|email|max:255',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'errors' => $validator->errors()->toArray()], 422);
        }

        // $user = User::where('login', $request->login)->first();

        // if (!$user || !Hash::check($request->password, $user->password)) {
        //     return response()->json(['error' => 'Unauthorized'], 401);
        // }


        $login = $request->only(['login', 'password']);

        // $token = $user->createToken('Personal Access Token')->accessToken;
        return $this->var->login($login);

        // return response()->json(['token' => $token], 200);
    }
}
