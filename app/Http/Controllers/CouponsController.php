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
        if (Cache::has('show_coupon_' . strtolower($coupon))) {
            return Cache::get('show_coupon_' . strtolower($coupon));
        }

        $coupon = Coupon::where('coupon_code', '=', $coupon)->where('admin_invalidated', '=', '0')->firstOrFail();

        $transformer = app(CouponTransformer::class);
        $output = $transformer->transform($coupon);

        Cache::put('show_coupon_' . $coupon->coupon_code, $output, 10);

        return response()->json($output);
    }


}