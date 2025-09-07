<?php

namespace App\Http\Controllers;

use Exception;
use App\Helpers\ResponseFormatter;
use App\Models\Table;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateTableRequest;

class ApiTableController extends Controller
{
    public function fetch(Request $request)
    {
        $floorId = $request->input('floor_id'); // filter berdasarkan floor_id
        $limit = $request->input('limit', 100);

        $tableQuery = Table::query();

        if ($floorId) {
            $tableQuery->where('floor_id', $floorId);
        }

        $tables = $tableQuery->paginate($limit);

        return response()->json([
            'status' => 'success',
            'message' => 'Tables found',
            'data' => $tables,
        ]);
    }

    public function update(UpdateTableRequest $request, $id)
    {
        try {
            $table = Table::find($id);
            if (!$table) {
                throw new Exception('Food not found');
            }
            $table->update([
                'status' => $request->status,
            ]);
            return ResponseFormatter::success($table, 'Status table updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
