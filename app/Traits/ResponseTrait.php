<?php

namespace App\Traits;

trait ResponseTrait
{
    use PaginationTrait;

    public function returnSuccess($msg = '', $code = 200)
    {
        return response()->json([
            'status' => true,
            'msg' => $msg,
            'code' => $code
        ], $code);
    }

    public function returnError($msg = '', $code = 400)
    {
        return response()->json([
            'status' => false,
            'msg' => $msg,
            'code' => $code
        ], $code);
    }

    public function returnData($status = true, $msg = '', $dataKey, $dataValue, $code = 200)
    {
        return response()->json([
            'status' => $status,
            'msg' => $msg,
            'code' => $code,
            $dataKey => $dataValue
        ], $code);
    }

    public function returnPaginationData($status = true, $msg = '', $dataKey, $dataValue, $code = 200)
    {
        return response()->json([
            'status' => $status,
            'msg' => $msg,
            'code' => $code,
            $dataKey =>  $dataValue->items(),
            'pagination' => $this->returnPaginationInformation($dataValue),
        ], $code);
    }

    public function returnLoginRefreshSuccess($msg = '', $dataKey, $dataValue, $token, $code = 200)
    {
        return response()->json([
            'status' => true,
            'msg' => $msg,
            'code' => $code,
            $dataKey => $dataValue,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer'
            ]
        ], $code);
    }
}
