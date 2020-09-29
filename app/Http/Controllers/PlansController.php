<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Transformers\PlanTransformer;
use App\Plan;
use Illuminate\Support\Facades\Cache;

class PlansController extends Controller
{
    public function index()
    {
        if (Cache::has('show_all_plans')) {
            return Cache::get('show_all_plans');
        }

        $transformer = app(PlanTransformer::class);
        $output = $transformer->transformCollection(Plan::orderBy('id', 'ASC')->get());

        Cache::put('show_all_plans', $output, 1440); // one day

        return response()->json($output);
    }

    public function show(string $plan)
    {
        $transformer = app(PlanTransformer::class);
        return response()->json($transformer->transform(Plan::find($plan)));
    }


}