<?php

namespace App\Services\Base;

use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BaseService
{
    public function serviceReturn(bool $success, $message, $code, $data): array
    {
        return [
            'success' => $success,
            'data' => $data,
            'message' => $message,
            'code' => $code
        ];
    }
}
