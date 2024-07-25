<?php

namespace App\Traits;

trait ApiResponses
{
    protected function ok($message, $data = [])
    {
        return $this->success($message, $data, 200);
    }

    protected function success($message, $data, $status_code = 200)
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'status' => $status_code
        ], $status_code);
    }

    protected function error($message, $status_code)
    {
        return response()->json([
            'message' => $message,
            'status' => $status_code
        ], $status_code);
    }
}
