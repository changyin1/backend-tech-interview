<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Coupon;
use App\Plan;
use App\Promotions\PromotionRepository;
use App\Http\Transformers\PlanTransformer;
use Illuminate\Support\Facades\Cache;

class CouponsController extends Controller
{
    public function show(string $coupon)
    {
        if (Cache::has('show_coupon_' . strtolower($coupon)) . '_with_promo') {
            return Cache::get('show_coupon_' . strtolower($coupon) . '_with_promo');
        }

        // @todo fail gracefully on fail to find coupon?
        $coupon = Coupon::where('coupon_code', '=', $coupon)->where('admin_invalidated', '=', '0')->firstOrFail();
        $coupons = [$coupon];
        $promos = new PromotionRepository();
        $promo = $promos->getActivePromotionForDateAsCoupon(\Carbon\Carbon::now());
        if ($promo) {
            $coupons[] = $promo;
        }

        $transformer = app(PlanTransformer::class);
        $output = $transformer->transformCollectionWithCoupons(Plan::orderBy('id', 'ASC')->get(), $coupons);

        Cache::put('show_coupon_' . $coupon->coupon_code . '_with_promo', $output, 10);

        return response()->json($output);
    }


}