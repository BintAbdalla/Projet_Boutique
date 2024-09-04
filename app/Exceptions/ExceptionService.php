<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Http\JsonResponse;

class ExceptionService extends Exception
{
    public function handleException(Exception $e): JsonResponse
    {
        // Formater la réponse d'erreur selon vos besoins
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }

    
}
