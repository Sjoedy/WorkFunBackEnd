<?php

namespace App\Services;

use App\Models\Group;
use App\Models\GroupUser;
use App\Services\Base\BaseService;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\ErrorHandler\Exception\FlattenException;

final class ExceptionService extends BaseService
{
    public function getInfo($e): array
    {
        $e = FlattenException::create($e);
        $code = $e->getStatusCode();
        $message = $e->getMessage();
        return [
            'code' => $code,
            'message' => $message
        ];
    }
}
