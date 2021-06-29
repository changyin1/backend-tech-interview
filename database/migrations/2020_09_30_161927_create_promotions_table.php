<?php

use App\Coupon;
use App\Promotion;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->unsignedInteger('coupon_id')->index();
            $table->timestamps();
        });

        $newYearsCoupon = Coupon::where('coupon_code', '=', 'newyear2021')->first();

        $newYearsPromo = Promotion::create([
            'start_date' => '2020-12-29 00:00:01',
            'end_date'   => $newYearsCoupon->redeem_by_date,
        ]);

        $newYearsPromo->coupon()->associate($newYearsCoupon);
        $newYearsPromo->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('promotions');
    }
}
