<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Food;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateFoodRequest;
use App\Http\Requests\UpdateFoodRequest;

class ApiFoodController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $foodQuery = Food::query();

        if ($id) {
            $food = $foodQuery->find($id);

            if ($food) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Food found',
                    'data' => $food,
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Food not found',
            ], 404);
        }

        if ($name) {
            $foodQuery->where('name', 'like', '%' . $name . '%');
        }

        $foods = $foodQuery->paginate($limit);

        return response()->json([
            'status' => 'success',
            'message' => 'Foods found',
            'data' => $foods,
        ]);
    }

    public function create(CreateFoodRequest $request)
    {
        try {
            $food = Food::create([
                'name' => $request->name,
                'category' => $request->category,
                'price' => $request->price,
            ]);
            if (!$food) {
                throw new Exception('Food not created');
            }
            $user = User::find(Auth::id());
            $user->foods()->attach($food->id);
            $food->load('users');
            return ResponseFormatter::success($food, 'Food created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function update(UpdateFoodRequest $request, $id)
    {
        try {
            $food = Food::find($id);
            if (!$food) {
                throw new Exception('Food not found');
            }
            $food->update([
                'name' => $request->name,
                'category' => $request->category,
                'price' => $request->price,
            ]);
            return ResponseFormatter::success($food, 'Food updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $food = Food::find($id);
            if (!$food) {
                throw new Exception('Food not found');
            }
            $food->delete();
            return ResponseFormatter::success('Food deleted');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
