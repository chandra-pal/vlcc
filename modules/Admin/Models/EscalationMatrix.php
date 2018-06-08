<?php

/**
 * The class to present Escalation Matrix model.
 * 
 * 
 * @author Priyank Deshpande <priyankad@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

class EscalationMatrix extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'member_escalation_matrix';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['admin_id', 'session_id', 'member_id', 'package_id', 'weight_loss', 'weight_gain', 'ath_comment', 'escalation_date', 'escalation_status', 'created_by', 'updated_by'];
    
    public function member() {
        return $this->belongsTo('Modules\Admin\Models\Member', 'member_id', 'id');
    }
    
    public function package() {
        return $this->belongsTo('Modules\Admin\Models\MemberPackage', 'package_id', 'id');
    }
    
}
