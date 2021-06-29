<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Transformers\PlanTransformer;
use App\Plan;
use App\Promotions\PromotionRepository;
use Illuminate\Support\Facades\Cache;

class PlansController extends Controller
{
    public function index()
    {
        $transformer = app(PlanTransformer::class);
        $promos = new PromotionRepository();
        $coupon = $promos->getActivePromotionForDateAsCoupon(\Carbon\Carbon::now());
        if ($coupon) {
            if (Cache::has('show_all_plans_with_promo')) {
                return Cache::get('show_all_plans_with_promo');
            }

            $output = $transformer->transformCollectionWithCoupons(Plan::orderBy('id', 'ASC')->get(), [$coupon]);
            Cache::put('show_all_plans_with_promo', $output, 1440); // one day
        } else {
            if (Cache::has('show_all_plans')) {
                return Cache::get('show_all_plans');
            }

            $output = $transformer->transformCollection(Plan::orderBy('id', 'ASC')->get());
            Cache::put('show_all_plans', $output, 1440); // one day
        }


        return response()->json($output);
    }

    // @todo implement promo logic to single plan
    public function show(string $plan)
    {
        $transformer = app(PlanTransformer::class);
        return response()->json($transformer->transform(Plan::find($plan)));
    }


}