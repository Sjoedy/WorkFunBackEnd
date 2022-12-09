<?php

namespace App\Traits;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use function auth;

trait ResponseTrait
{
    /**
     * Core of response
     *
     * @param object|array|null $data
     * @param string $message
     * @param integer $statusCode
     * @param boolean $isSuccess
     * @return JsonResponse
     */
    public function coreResponse(object|array|null $data, string $message, int $statusCode, bool $isSuccess = true): JsonResponse
    {
        if ($isSuccess) {
            $resData = [
                'error' => false,
                'message' => $message,
                'code' => $statusCode
            ];

        } else {
            $resData = [
                'error' => true,
                'message' => $message,
                'code' => $statusCode
            ];

        }
        if ($data) $resData['data'] = $data;
        return response()->json($resData, $statusCode)
            ->withHeaders([
                'Access-Control-Allow-Origin' => '*',
            ]);
    }

    /**
     * Send any success response
     *
     * @param object|bool|array $data
     * @param string $message
     * @param integer $statusCode
     * @return JsonResponse
     */
    public function success(object|bool|array|null $data, string $message, int $statusCode = 200): JsonResponse
    {
        return $this->coreResponse($data, $message, $statusCode);
    }

    /**
     * Send any error response
     *
     * @param string $message
     * @param integer $statusCode
     * @param null $data
     * @return JsonResponse
     */
    public function error(string $message, int $statusCode = 500, $data = null): JsonResponse
    {
        return $this->coreResponse($data, $message, $statusCode, false);
    }

    /**
     * @param $data
     * @return JsonResponse
     */
    public function returnController($data): JsonResponse
    {
        return $data['success'] ? ($this->success($data['data'], $data['message'])) : ($this->error($data['data'], $data['message']));
    }
}
