<?php

use App\Coupon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('coupon_code', 191)->index();
            $table->text('description');
            $table->integer('percent_off');
            $table->integer('max_redemption_count')->nullable()->index();
            $table->dateTime('redeem_by_date')->nullable();
            $table->boolean('for_annual_only');
            $table->boolean('for_hd_only');
            $table->boolean('for_4k_only');
            $table->boolean('admin_invalidated');
            $table->timestamps();
        });

        Coupon::create([
            'coupon_code'          => 'email25',
            'description'          => 'Save 25% by signing up for our mailing list',
            'percent_off'          => 25,
            'max_redemption_count' => 100000,
            'redeem_by_date'       => null,
            'for_annual_only'      => 0,
            'for_hd_only'          => 0,
            'for_4k_only'          => 0,
            'admin_invalidated'    => 0
        ]);

        Coupon::create([
            'coupon_code'          => 'new4ktv',
            'description'          => 'Save 20% on annual plans by using the coupon code "new4ktv" on 4k subscriptions.',
            'percent_off'          => 20,
            'max_redemption_count' => null,
            'redeem_by_date'       => null,
            'for_annual_only'      => 1,
            'for_hd_only'          => 0,
            'for_4k_only'          => 1,
            'admin_invalidated'    => 0
        ]);

        Coupon::create([
            'coupon_code'          => 'savein2020',
            'description'          => 'Save 30% on HD plans by using the coupon code "savein2020" before the end of the year 2020.',
            'percent_off'          => 30,
            'max_redemption_count' => null,
            'redeem_by_date'       => '2021-01-01 00:04:00',
            'for_annual_only'      => 0,
            'for_hd_only'          => 1,
            'for_4k_only'          => 0,
            'admin_invalidated'    => 0
        ]);

        Coupon::create([
            'coupon_code'          => 'halloween2020',
            'description'          => 'Save 31% this Halloween!',
            'percent_off'          => 31,
            'max_redemption_count' => null,
            'redeem_by_date'       => '2020-11-01 12:00:00',
            'for_annual_only'      => 0,
            'for_hd_only'          => 0,
            'for_4k_only'          => 0,
            'admin_invalidated'    => 0,
        ]);

        Coupon::create([
            'coupon_code'          => 'newyear2021',
            'description'          => 'New Year, New Deal! Save 40% on all annual plans this New Year',
            'percent_off'          => 40,
            'max_redemption_count' => null,
            'redeem_by_date'       => '2021-01-08 00:04:00',
            'for_annual_only'      => 1,
            'for_hd_only'          => 0,
            'for_4k_only'          => 0,
            'admin_invalidated'    => 0,
        ]);


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('coupons');
    }
}
