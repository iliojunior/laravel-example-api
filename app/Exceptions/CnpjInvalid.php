<?php

namespace App\Exceptions;

use Exception;

class CnpjInvalid extends Exception
{
    public function render($request)
    {
        return response()->json([
            'message' => "The input cnpj is invalid",
        ], 400, [], JSON_UNESCAPED_UNICODE);
    }
}