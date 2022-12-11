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
    /**
     * @param $request
     * @param Builder $query
     * @param array $columnsSearch
     * @param array $filterBy
     * @param null $date
     * @return array|CursorPaginator|LengthAwarePaginator|Collection
     */
    public function formatQuery(
        $request,
        Builder $query,
        array $columnsSearch = [],
        array $filterBy = [],
        $date = null,
    ): array|CursorPaginator|LengthAwarePaginator|Collection
    {
        // search value from search request
        if (!empty($request['search']) && count($columnsSearch) > 0) {
            if (count($columnsSearch) == 1) $query->where($columnsSearch[0], 'like', '%' . $request['search'] . '%');
            else {
                $query->where(function ($q) use ($request, $columnsSearch) {
                    $q->where($columnsSearch[0], 'like', '%' . $request['search'] . '%');
                    unset($columnsSearch[0]);
                    foreach ($columnsSearch as $filterKey) {
                        $q->orWhere($filterKey, 'like', '%' . $request['search'] . '%');
                    }
                });
            }
        }

        // filter by date
        if (!empty($request['start_date']) && !empty($request['end_date']) && isset($date)) {
            $format = ['Y-m-d H:i:s', 'Y-m-d H:i', 'Y-m-d H', 'Y-m-d'];
            $startFormat = '';
            $endFormat = '';
            foreach ($format as $item) {
                $startDate = self::checkDateFormat($request['start_date'], $item);
                if ($startDate) {
                    $startFormat = $item;
                }
                $enDate = self::checkDateFormat($request['end_date'], $item);
                if ($enDate) {
                    $endFormat = $item;
                }
            }
            if ($startFormat == $endFormat) {
                if ($startFormat == 'Y-m-d H:i:s') $query->whereBetween($date, [$request['start_date'], $request['end_date']]);
                if ($startFormat == 'Y-m-d H:i') $query->whereBetween($date, [$request['start_date'] . ':00', $request['end_date'] . ':59']);
                if ($startFormat == 'Y-m-d H') $query->whereBetween($date, [$request['start_date'] . ':00:00', $request['end_date'] . ':59:59']);
                if ($startFormat == 'Y-m-d') $query->whereBetween(DB::raw("DATE($date)"), [$request['start_date'], $request['end_date']]);
            }
        }

        // filter by value in column
        foreach ($filterBy as $column) {
            $checkColumn = explode('.', $column);
            $parameter = (count($checkColumn) > 1) ? $checkColumn[1] : $checkColumn[0];
            if (!empty($request[$parameter])) {
                $query->where($column, $request[$parameter]);
            }
        }

        // Paginate
        if (!empty($request['per_page'])) {
            $data = $query->paginate($request['per_page']);
        } else if (!empty($request['cursor_paginate'])) {
            $data = $query->cursorPaginate($request['cursor_paginate']);
        } else {
            $data = $query->get();
        }
        return $data;
    }

    /**
     * @param bool $success
     * @param $message
     * @param $code
     * @param $data
     * @return array
     */
    public function serviceResponse(bool $success, $message, $code, $data): array
    {
        return [
            'success' => $success,
            'data' => $data,
            'message' => $message,
            'code' => $code
        ];
    }

    /**
     * @param $date
     * @param $format
     * @return bool
     */
    public function checkDateFormat($date, $format): bool
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}
