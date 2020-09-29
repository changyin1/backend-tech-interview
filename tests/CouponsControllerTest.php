<?php

class CouponsControllerTest extends TestCase
{
    public function testCoupons()
    {
        $this->json('GET', '/coupons')
            ->assertResponseStatus(404);
    }

    public function testShowCoupon()
    {
        $this->json('GET', '/coupons/halloween2020')
            ->seeJson([
                'code'        => 'halloween2020',
                'description' => 'Save 31% this Halloween!',
                'percent_off' => 31,
                'hd'          => false,
                '4k'          => false,
                'annual'      => false,
                'disclaimer'  => 'Use coupon code "halloween2020" before Sun, Nov 1, 2020 12:00 PM to get 31% off.',
            ]);
    }

}
