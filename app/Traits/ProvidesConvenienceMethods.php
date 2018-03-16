<?php

namespace App\Traits;

use Auth;
use Illuminate\Http\Resources\Json\JsonResource;
use ElemenX\ApiPagination\Paginator as ElemenXPaginator;
use Illuminate\Pagination\Paginator as IlluminatePaginator;

trait ProvidesConvenienceMethods
{
    public function success($data = [], $code = 200)
    {
        $result = [
            'code'    => $code,
            'message' => $code == 200 ? 'OK' : trans('success.' . $code),
            'data'    => []
        ];
        if (is_object($data)) {
            if ($data instanceof JsonResource) {
                $result['data'] = $data->resolve();
                if (method_exists($data, 'getMeta')) {
                    $result['meta'] = $data->getMeta();
                }
            } elseif ($data instanceof ElemenXPaginator) {
                $data = $data->toArray();
                $result['data'] = $data['data'];
                $result['meta'] = $data['meta'];
            } elseif ($data instanceof IlluminatePaginator) {
                $data = $data->toArray();
                $result['data'] = $data['data'];
                $result['meta'] = array_only($data, ['current_page', 'per_page', 'to']);
            } else {
                $data = $data->toArray();
                $result['data'] = $data;
            }
        } else {
            $result['data'] = $data;
        }
        return response()->json($result, substr($code, 0, 3));
    }

    public function error($code = 400)
    {
        return response()->json([
            'code'    => $code,
            'message' => trans('errors.' . $code)
        ], substr($code, 0, 3));
    }

    protected function user()
    {
        return Auth::user();
    }
}
