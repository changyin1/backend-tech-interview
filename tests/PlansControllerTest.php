<?php

class PlansControllerTest extends TestCase
{
    public function testPlans()
    {
        $this->json('GET', '/plans')
            ->assertResponseStatus(200)
            ->seeJson([
                [
                    'encoding'    => '4k',
                    'id'          => '4k_annual',
                    'name'        => '4K Definition Annual',
                    'is_annual'   => true,
                    'price'       => '$69.99',
                    'description' => '4K Definition Annual at $69.99 per year',
                ],
                [
                    'encoding'    => '4k',
                    'id'          => '4k_monthly',
                    'name'        => '4K Definition',
                    'is_annual'   => false,
                    'price'       => '$9.99',
                    'description' => '4K Definition at $9.99 per month',
                ],
                [
                    'encoding'    => 'hd',
                    'id'          => 'hd_annual',
                    'name'        => 'High Definition Annual',
                    'is_annual'   => true,
                    'price'       => '$19.99',
                    'description' => 'High Definition Annual at $19.99 per year',
                ],
                [
                    'encoding'    => 'hd',
                    'id'          => 'hd_monthly',
                    'name'        => 'High Definition',
                    'is_annual'   => false,
                    'price'       => '$2.99',
                    'description' => 'High Definition at $2.99 per month',
                ],
            ]);
    }

    public function testPlan()
    {
        $this->json('GET', '/plans/4k_annual')
            ->seeJson(
                array(
                    'encoding'    => '4k',
                    'id'          => '4k_annual',
                    'name'        => '4K Definition Annual',
                    'is_annual'   => true,
                    'price'       => '$69.99',
                    'description' => '4K Definition Annual at $69.99 per year',
                )
            );
    }
}
