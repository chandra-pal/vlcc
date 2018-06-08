<?php
/**
 * The helper library class for getting information of a client from dietician ID
 *
 *
 * @author Priyanka D <priyankad@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Services\Helper;

use Auth;
use Datatables;
use Illuminate\Support\Str;
use Modules\Admin\Repositories\MembersRepository;
use Modules\Admin\Models\Member;
use Illuminate\Http\Request;

class DataHelper
{

    /**
     * set member_id value in session
     * @return String
     */
    protected static $request;

    public function __construct(Request $request)
    {
        //self::request = $request;
        self::$request = $request;
    }
//    public static function setSessionMemberId($memberId=0) {
//        self::$request->session()->put('member_id', $memberId);
//    }
//
//    public static function getSessionMemberId() {
//        $value = self::$request->session()->get('member_id', 0);
//        return $value;
//    }
}
