<?php

namespace App\Traits;

trait ApiResponses
{
    protected function ok($message)
    {
        return $this->success($message, 200);
    }

    protected function success($message, $status_code = 200)
    {
        return response()->json([
            'message' => $message,
            'status' => $status_code
        ], $status_code);
    }
}
