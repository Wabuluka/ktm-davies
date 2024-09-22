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

     /**
     * Sorts an array of data and saves it to the specified table.
     *
     * @param array $data Array of data to be sorted.
     * @param string $table Table name where the data will be saved.
     * @param string $column Column name where the sorted data will be saved.
     * @param string $sortDirection Sort direction (asc or desc). Default is 'asc'.
     * @return bool True if the data is successfully saved, otherwise false.
     * @throws Exception If there is an error during sorting or saving.
     */
    public function sortObj(array $data, string $table, string $column, string $sortDirection = 'asc')
    {
        try {
            // Validate sort direction
            if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
                throw new Exception('Invalid sort direction. Use "asc" or "desc".');
            }

            // Sort the data
            if ($sortDirection === 'asc') {
                sort($data);
            } else {
                rsort($data);
            }

            // Prepare data for insertion
            $insertData = [];
            foreach ($data as $value) {
                $insertData[] = [
                    $column => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Insert sorted data into the specified table
            DB::table($table)->insert($insertData);

            return true;
        } catch (Exception $e) {
            // Handle exception (log the error, return false, etc.)
            report($e);
            return false;
        }
    }


    public function updateSortingOrder(Request $request)
    {
        try{
            $data = $request->validate([
                'order' => 'required|array',
                'order.*' => 'integer',
            ]);
            foreach ($data['order'] as $position => $id) {
                self::where('id', $id)->update(['sort' => $position + 1]);
            }
            return response()->json(['status' => 'success']);
        } catch (Exception $e){
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
