<?php

namespace Modules\Admin\Http\Controllers;

use Auth;
use Modules\Admin\Repositories\SessionBookingsRepository;
use Modules\Admin\Repositories\EscalationMatrixRepository;
use Modules\Admin\Repositories\DeviationRepository;
use Modules\Admin\Repositories\MembersRepository;

class DashboardController extends Controller {

    /**
     * The SessionBookingsRepository instance.
     *
     * @var Modules\Admin\Repositories\SessionBookingsRepository
     */
    protected $sessionRepository;

    /**
     * The escalationRepository instance.
     *
     * @var Modules\Admin\Repositories\escalationRepository
     */
    protected $escalationRepository;

    /**
     * The deviationRepository instance.
     *
     * @var Modules\Admin\Repositories\deviationRepository
     */
    protected $deviationRepository;
    /**
     * The membersRepository instance.
     *
     * @var Modules\Admin\Repositories\membersRepository
     */
    protected $membersRepository;

    public function __construct(SessionBookingsRepository $sessionRepository, EscalationMatrixRepository $escalationRepository, DeviationRepository $devationRepository, MembersRepository $membersRepository) {
        parent::__construct();
        $this->sessionRepository = $sessionRepository;
        $this->escalationRepository = $escalationRepository;
        $this->deviationRepository = $devationRepository;
        $this->membersRepository = $membersRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        // session booking
        $user_id = Auth::guard('admin')->user()->id;
        $user_type_id = Auth::guard('admin')->user()->user_type_id;
        $params['user_id'] = Auth::guard('admin')->user()->id;
        $params['user_type_id'] = Auth::guard('admin')->user()->user_type_id;
        $params['user_name'] = Auth::guard('admin')->user()->username;
        $params['session_date'] = date('Y-m-d');
        $today_date = date('Ymd');
        $sessionBookings = $this->sessionRepository->dataCount($params);
        if ($sessionBookings == '') {
            $sessionBookings = 0;
        }
        // escalation
        $escalation = $this->escalationRepository->dataCount($params);
        if ($escalation == '') {
            $escalation = 0;
        }
        //deviation
        $deviation = $this->deviationRepository->dataCount($params);
        if ($deviation == '') {
            $deviation = 0;
        }
        //members
        $members = $this->membersRepository->dataCount($params);
        if ($members == '') {
            $members = 0;
        }        
        
        // Categorywise customers count
        $categoryWiseCustomers = [];
        
        // Get Successful, Un Successful, Regular, Irregular customers count
        $successfulCustomers = $this->membersRepository->getSuccessfulCustomerCount($params);
        $unsuccessfulCustomers = $this->membersRepository->getUnsuccessfulCustomerCount($params);
        $regularCustomers = $this->membersRepository->getRegularCustomerCount($params);
        $irregularCustomers = $this->membersRepository->getIrregularCustomerCount($params);        
        
        $SR = array_intersect($successfulCustomers, $regularCustomers);
        $UR = array_intersect($unsuccessfulCustomers, $regularCustomers);
        $SIR = array_intersect($successfulCustomers, $irregularCustomers);
        $UIR = array_intersect($unsuccessfulCustomers, $irregularCustomers);        
       
        $categoryWiseCustomers["success_regular"] = count($SR);
        $categoryWiseCustomers["unsuccess_regular"] = count($UR);
        $categoryWiseCustomers["success_irregular"] = count($SIR);
        $categoryWiseCustomers["unsuccess_irregular"] = count($UIR);        
        
        return view('admin::index', compact('sessionBookings', 'escalation', 'deviation', 'members', 'today_date', 'user_id', 'user_type_id', 'categoryWiseCustomers'));
    }

}
