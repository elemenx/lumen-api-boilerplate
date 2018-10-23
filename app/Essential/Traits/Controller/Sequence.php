<?php

namespace App\Essential\Traits\Controller;

use Cache;
use Illuminate\Http\Request;

trait Sequence
{
    public function sequence(Request $request)
    {
        $this->validate($request, [
            'ids' => 'required|array'
        ]);

        $items = $this->model->get()->keyBy('id');

        $i = 0;
        foreach ($request->ids as $id) {
            if (!isset($items[$id])) {
                continue;
            }
            $items[$id]->update([
                'sequence' => $i
            ]);
            $i++;
        }

        Cache::flush();

        return $this->success(null);
    }
}
