<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use Illuminate\Http\Request;

class ApiFloorController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $floorQuery = Floor::query();

        if ($id) {
            $floor = $floorQuery->find($id);

            if ($floor) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Floor found',
                    'data' => $floor,
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Floor not found',
            ], 404);
        }

        if ($name) {
            $floorQuery->where('name', 'like', '%' . $name . '%');
        }

        $floors = $floorQuery->paginate($limit);

        return response()->json([
            'status' => 'success',
            'message' => 'Foods found',
            'data' => $floors,
        ]);
    }
}
