<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Coupon;
use App\Http\Transformers\CouponTransformer;
use Illuminate\Support\Facades\Cache;

class CouponsController extends Controller
{
    public function show(string $coupon)
    {
        if (Cache::has('show_coupon_' . strtolower($coupon)) . '_with_promo') {
            return Cache::get('show_coupon_' . strtolower($coupon) . '_with_promo');
        }

        $coupon = Coupon::where('coupon_code', '=', $coupon)->where('admin_invalidated', '=', '0')->firstOrFail();
        $promos = new PromotionRepository();
        $promo = $promos->getActivePromotionForDateAsCoupon(\Carbon\Carbon::now());

        $coupons = [$coupon, $promo];

        $transformer = app(PlanTransformer::class);
        $output = $transformer->transformCollectionWithCoupons(Plan::orderBy('id', 'ASC')->get(), $coupons);

        Cache::put('show_coupon_' . $coupon->coupon_code . '_with_promo', $output, 10);

        return response()->json($output);
    }


}