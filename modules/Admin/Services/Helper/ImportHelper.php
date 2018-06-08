<?php

/**
 * The helper library class for getting information of a client from dietician ID
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Services\Helper;

use Auth;
use Datatables;
use Illuminate\Support\Str;
use Modules\Admin\Repositories\MembersRepository;
use Modules\Admin\Models\Member;
use Session;

class ImportHelper {

    /**
     * fetch user details
     * @return String
     */
    public function importMemberData() {
        $membersRepository = new MembersRepository(new Member);
        if (Auth::guard('admin')->user()->userType->id == 4 || Auth::guard('admin')->user()->userType->id == 8) {
            $params['username'] = Auth::guard('admin')->user()->username;
//            $membersList = $membersRepository->listMemberData($params);
//            $memberData = $membersList['data']['customers']['response']['Customer'];
//            $memberList = array();
//
//            if (!empty($membersList['data']['customers']['response']['Customer'])) {
//                foreach ($memberData as $data) {
//                    $memberList[] = $data['mobile_number'];
//                }
//            }
//            $params['memberList'] = $memberList;

            $membersList = $membersRepository->importMemberData($params);
        }
    }

}
