<?php
declare(strict_types=1);

namespace App\Http\Transformers;

use App\Coupon;
use App\Plan;
use Illuminate\Database\Eloquent\Collection;

class PlanTransformer
{
    public function transformCollection(Collection $collection)
    {
        $data = [];
        foreach ($collection as $item) {
            $data[] = [
                'encoding'    => $item->encoding,
                'id'          => $item->id,
                'name'        => $item->name,
                'is_annual'   => (bool)$item->is_annual,
                'price'       => '$' . $item->price,
                'description' => $item->getFullPlanDescription(),
            ];
        }
        return $data;
    }

    public function transform(Plan $plan)
    {
        return [
            'id'        => $plan->id,
            'name'      => $plan->name,
            'is_annual' => (bool)$plan->is_annual,
            'encoding'  => $plan->encoding,
            'price'     => '$' . $plan->price,
            'description' => $plan->getFullPlanDescription(),
        ];
    }

    public function transformCollectionWithCoupons(Collection $collection, Array $coupons)
    {
        $data = [];
        $promo_applied = false;
        foreach ($collection as $item) {
            $item = $this->applyCoupons($item, $coupons);
            $plan_data = [
                'encoding'    => $item->encoding,
                'id'          => $item->id,
                'name'        => $item->name,
                'is_annual'   => (bool)$item->is_annual,
                'price'       => '$' . $item->price,
                'description' => $item->getFullPlanDescription(),
            ];

            if (isset($item->discount_price)) {
                $plan_data['discount_price'] = $item->discount_price;
                $promo_applied = true;
            }

            $data['plans'][] = $plan_data;
        }

        if ($promo_applied) {
            $data['promo_applied'] = true;
        }
        return $data;
    }

    // this can be moved to object model
    private function applyCoupons(Plan $plan, Array $coupons)
    {
        foreach($coupons as $key => $coupon) {
            $apply_discount = true;
            // annual only, no quality restriction
            if ($coupon->for_annual_only && !$coupon->for_hd_only && !$coupon->for_4k_only) {
                if (!$plan->is_annual) {
                    $apply_discount = false;
                }
            }
            // hd only, check if should only apply to annual
            if ($coupon->for_hd_only) {
                if ($coupon->for_annual_only && !$plan->is_annual) {
                    $apply_discount = false;
                }
            }
            // 4k only, check if should only apply to annual
            if ($coupon->for_4k_only) {
                if ($coupon->for_annual_only && !$plan->is_annual) {
                    $apply_discount = false;
                }
            }
            // if no restriction, apply discount
            if ($apply_discount) {
                $discount_price = round($plan->price * (1 - $coupon->percent_off / 100), 2);

                if (!isset($price->discount_price) || $plan->discount_price > $discount_price) {
                    $plan->discount_price = $discount_price;
                }
            }
        }

        return $plan;
    }
}