<?php
declare(strict_types=1);

namespace App\Http\Transformers;

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

}