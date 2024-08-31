<?php

namespace App\Traits;

use App\Enums\StateEnums;

trait Responsetrait
{
    public function sendResponse($data ,StateEnums $status= StateEnums::SUCCESS, $message = 'Ressource non trouvée',$codeStatut = 200)
    {
        return response()->json([
            'data' =>$data,
            'status' =>  $status->value,
            'message' => $message,
        ],$codeStatut);
    }
}
