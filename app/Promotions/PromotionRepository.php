<?php
declare(strict_types=1);

namespace App\Promotions;

use App\Coupon;

class PromotionRepository
{
    public function getActivePromotionForDateAsCoupon(\Carbon\Carbon $now): ?Coupon
    {
        $promo = \App\Promotion::where('start_date', '<', $now)->where('end_date', '>=', $now)->first();

        if ($promo) {
            return $promo->coupon;
        }

        return null;

    }

}