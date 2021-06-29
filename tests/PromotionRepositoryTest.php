<?php

use App\Promotions\PromotionRepository;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PromotionRepositoryTest extends TestCase
{
    public function testNoPromotion()
    {
        /** @var PromotionRepository $repo */
        $repo = app(PromotionRepository::class);
        $this->assertNull($repo->getActivePromotionForDateAsCoupon(new Carbon\Carbon('2020-11-01 00:00:01')));
    }

    public function testActivePromotion()
    {
        /** @var PromotionRepository $repo */
        $repo = app(PromotionRepository::class);
        $coupon = $repo->getActivePromotionForDateAsCoupon(new Carbon\Carbon('2021-01-01 09:30:00'));
        $this->assertInstanceOf(\App\Coupon::class,$coupon);
        $this->assertEquals('newyear2021', $coupon->coupon_code);
    }
}
