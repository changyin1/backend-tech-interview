<?php

use App\Plan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->string('id')->unique();
            $table->string('name', 191);
            $table->string('encoding', 191);
            $table->boolean('is_annual');
            $table->float('price');
            $table->timestamps();
        });

        Plan::create([
            'id' => 'hd_monthly',
            'name' => 'High Definition',
            'encoding' => 'hd',
            'is_annual' => false,
            'price' => 2.99
        ]);

        Plan::create([
            'id' => 'hd_annual',
            'name' => 'High Definition Annual',
            'encoding' => 'hd',
            'is_annual' => true,
            'price' => 19.99
        ]);

        Plan::create([
            'id' => '4k_monthly',
            'name' => '4K Definition',
            'encoding' => '4k',
            'is_annual' => false,
            'price' => 9.99
        ]);

        Plan::create([
            'id' => '4k_annual',
            'name' => '4K Definition Annual',
            'encoding' => '4k',
            'is_annual' => true,
            'price' => 69.99
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('plans');
    }
}
