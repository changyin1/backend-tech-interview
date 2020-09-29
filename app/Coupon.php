<?php
declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $table = 'coupons';

    protected $fillable = [
        'coupon_code',
        'description',
        'percent_off',
        'max_redemption_count',
        'redeem_by_date',
        'for_annual_only',
        'for_hd_only',
        'for_4k_only',
        'admin_invalidated',
    ];


}