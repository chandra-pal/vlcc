<?php

namespace App\Api\Transformers;

use Modules\Admin\Models\MemberPackage;
use League\Fractal\TransformerAbstract;

class PackagesTransformer extends TransformerAbstract {

    /**
     * Turn this item object into a generic array.
     *
     * @param $item
     * @return array
     */
    public function transform(MemberPackage $item) {
        return [
            'id' => (int) $item->id,
            'member_id' => (int) $item->member_id,
            'crm_package_id' => (string) $item->crm_package_id,
            'package_title' => (string) $item->package_title,
            'start_date' => (string) $item->start_date,
            'end_date' => (string) $item->end_date,
            'height' => (string) $item->height,
            'weight' => (string) $item->weight,
            'waist' => (string) $item->waist,
            'total_payment' => (string) $item->total_payment,
            'payment_made' => (string) $item->payment_made,
            'programme_booked' => (string) $item->programme_booked,
            'programme_needed' => (string) $item->programme_needed,
            'conversion' => (string) $item->conversion,
            'programme_re_booked' => (string) $item->programme_re_booked,
            'remarks' => (string) $item->remarks,
            'created_at' => (string) $item->created_at,
            'updated_at' => (string) $item->updated_at,
        ];
    }

}
