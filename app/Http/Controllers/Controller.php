<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function successResponse($data = [], ?string $message = null, $code = null)
    {
        $code = $code ?? 200;
        $message = $message ?: 'Success';

        return response()->json([
            'status' => true,
            'message' => $message,
            'code' => $code,
            'data' => $data,
        ], $code);
    }

    public function errorResponse($data = [], ?string $message = null, $code = null)
    {
        $code = $code ?? 400;
        $message = $message ?: 'Error';

        return response()->json([
            'status' => false,
            'message' => $message,
            'code' => $code,
            'data' => $data,
        ], $code);
    }

    public function paginateResponse($data = null, $code = null, ?string $message = null)
    {
        $code = $code ?? 200;
        $message = $message ?: 'Fetched all successfully';

        // Handle both standard Paginator and ResourceCollection
        $paginator = $data instanceof \Illuminate\Http\Resources\Json\ResourceCollection ? $data->resource : $data;
        $items = $data instanceof \Illuminate\Http\Resources\Json\ResourceCollection ? $data->resolve() : $paginator->items();

        return response()->json([
            'status' => true,
            'message' => $message,
            'code' => $code,
            'data' => $items,
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'links' => $paginator->linkCollection(),
                'path' => $paginator->path(),
                'per_page' => $paginator->perPage(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ]
        ], $code);
    }
}
