<?php
declare(strict_types=1);

namespace App\Http\Transformers;

use App\Coupon;
use Carbon\Carbon;

class CouponTransformer
{

    public function transform(Coupon $coupon)
    {
        if ($coupon->admin_invalidated) {
            throw new \RuntimeException("Coupon has been deleted");
        }

        $disclaimer = 'Use coupon code "' . $coupon->coupon_code . '"';

        if ($coupon->redeem_by_date) {
            $disclaimer .= ' before ' . (new Carbon($coupon->redeem_by_date))->toDayDateTimeString();
        }

        $disclaimer .= ' to get ' . $coupon->percent_off . '% off';

        if ($coupon->for_annual_only) {
            $disclaimer .= ' annually billed ';
        }

        if ($coupon->for_hd_only) {
            $disclaimer .= 'HD plans';
        } elseif ($coupon->for_4k_only) {
            $disclaimer .= '4K plans';
        }

        $disclaimer .= '.';

        return [
            'code'        => $coupon->coupon_code,
            'description' => $coupon->description,
            'percent_off' => $coupon->percent_off,
            'hd'          => (bool)$coupon->for_hd_only,
            '4k'          => (bool)$coupon->for_4k_only,
            'annual'      => (bool)$coupon->for_annual_only,
            'disclaimer'  => $disclaimer,
        ];
    }

}