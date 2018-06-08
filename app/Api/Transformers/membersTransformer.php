<?php

namespace App\Api\Transformers;

use Modules\Admin\Models\Member;
use League\Fractal\TransformerAbstract;

class membersTransformer extends TransformerAbstract {

    /**
     * Turn this item object into a generic array.
     *
     * @param $item
     * @return array
     */
    public function transform(Member $item) {
        return [
            'id' => (int) $item->id,
            'first_name' => (string) $item->first_name,
            'last_name' => (string) $item->last_name,
            'mobile_number' => (int) $item->mobile_number,
            'crm_center_id' => (string) $item->crm_center_id,
            'crm_customer_id' => (string) $item->crm_customer_id,
            'dietician_username' => (string) $item->dietician_username,
//            'id'         => (int)$item->id,
//            'created_at' => (string)$item->created_at,
//            'updated_at' => (string)$item->updated_at,
        ];
    }

}
