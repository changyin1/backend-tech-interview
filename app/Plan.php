<?php
declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    public $incrementing = false;

    protected $table = 'plans';

    protected $fillable = [
        'id',
        'name',
        'encoding',
        'price',
        'is_annual',
    ];

    public function getFullPlanDescription(): string
    {
        return $this->name . ' at $' . $this->price . ' per ' . $this->getBillingInterval() ;
    }

    private function getBillingInterval(): string
    {
        return $this->is_annual ? 'year' : 'month';
    }

    public function getWithCoupon()
    {
        $allPlans = $this->get();
        return $allPlans;
    }

}