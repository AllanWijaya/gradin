<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    private function dataPaginate(Request $request, Builder $instance, array $searchColumns, array $orderBy)
    {
        $search = $request->query('search');

        if ($search) {
            $keywords = preg_split('/\s+/', trim($search));

            $instance->where(function ($query) use ($keywords, $searchColumns) {
                foreach ($keywords as $keyword) {
                    $query->where(function ($subQuery) use ($keyword, $searchColumns) {
                        foreach ($searchColumns as $column) {
                            $subQuery->orWhere(
                                $column,
                                'like',
                                "%{$keyword}%"
                            );
                        }
                    });
                }
            });
        }

        $requestedOrder = $request->query('order_by', []);
        if (is_array($requestedOrder)) {
            foreach ($requestedOrder as $column => $direction) {
                if (
                    array_key_exists($column, $orderBy) &&
                    in_array(strtolower($direction), ['asc', 'desc'], true)
                ) {
                    $instance->orderBy(
                        $column,
                        strtolower($direction)
                    );
                }
            }
        }

        if (empty($requestedOrder)) {
            foreach ($orderBy as $column => $direction) {
                $instance->orderBy($column, $direction);
            }
        }

        return $instance->paginate($request->query('limit', 10));
    }

    public function index(Request $request)
    {
        $instance = Courier::query();

        if ($request->filled('level')) {
            $levels = array_values(array_filter(
                array_map('trim', explode(',', $request->query('level'))),
                fn($level) => ctype_digit($level)
            ));

            if (!empty($levels)) {
                $instance->whereIn('level', $levels);
            }
        }

        $data = $this->dataPaginate(
            $request,
            $instance,
            ['name'],
            ['name' => 'asc',]
        );

        return response()->json($data);
    }

    public function show()
    {
        $data = Courier::all();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:255',
            'grouping' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'level' => 'required|integer',
        ]);

        $id = $request->id;

        $data = Courier::updateOrCreate(['id' => $id], $request->all());

        return response()->json($data, $id ? 200 : 201);
    }

    public function destroy(Courier $courier)
    {
        $courier->delete();

        return response()->json(null, 204);
    }
}
