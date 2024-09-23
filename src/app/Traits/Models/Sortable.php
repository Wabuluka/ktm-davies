<?php

namespace App\Traits\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use Exception;

trait Sortable
{
    public static function bootSortable()
    {
        static::creating(function ($model) {
            $model->sort ??= $model->sortable()->max('sort') + 1;
        });

        static::deleting(function ($model) {
            $model->sortable()->where('sort', '>', $model->sort)->decrement('sort');
        });
    }

    /**
     * sort 値を一意にする範囲
     */
    protected function sortable(): Builder
    {
        return static::query();
    }

    protected function getPrev(): ?static
    {
        return $this->sortable()->where('sort', '<', $this->sort)
            ->orderBy('sort', 'desc')
            ->first();
    }

    protected function getNext(): ?static
    {
        return $this->sortable()->where('sort', '>', $this->sort)
            ->orderBy('sort', 'asc')
            ->first();
    }

    public function moveUp()
    {
        if ($prev = $this->getPrev()) {
            DB::transaction(function () use ($prev) {
                $temp = $prev->sort;
                $prev->sort = $this->sort;
                $this->sort = $temp;

                $prev->save();
                $this->save();
            });
        }
    }

    public function moveDown()
    {
        if ($next = $this->getNext()) {
            DB::transaction(function () use ($next) {
                $temp = $next->sort;
                $next->sort = $this->sort;
                $this->sort = $temp;

                $next->save();
                $this->save();
            });
        }
    }

    public function updateSortingOrder(Request $request)
    {
        try{
            // $data = $request->validate([
            //     'sort' => 'required|array',
            //     'sort.*' => 'array',
            // ]);
            foreach($request as $st) {
                self::where('id', $st['id'])->update(['sort' => $st['sort']]);
            }
            return response()->json(['status' => 'success']);
        } catch (Exception $e){
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
