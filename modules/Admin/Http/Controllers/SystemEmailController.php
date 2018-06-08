<?php
/**
 * The class for managing system emails specific actions.
 * 
 * 
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Controllers;

use Auth;
use Modules\Admin\Models\SystemEmail;
use Modules\Admin\Repositories\SystemEmailRepository;
use Modules\Admin\Http\Requests\SystemEmailCreateRequest;
use Modules\Admin\Http\Requests\SystemEmailUpdateRequest;

class SystemEmailController extends Controller
{

    /**
     * The SystemEmailRepository instance.
     *
     * @var Modules\Admin\Repositories\SystemEmailRepository $repository
     */
    protected $repository;

    /**
     * Create a new SystemEmailController instance.
     *
     * @param  Modules\Admin\Repositories\SystemEmailRepository $repository
     * @return void
     */
    public function __construct(SystemEmailRepository $repository)
    {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
    }

    /**
     * List all the data
     *
     * @return type
     */
    public function index()
    {
        $emailTo = $this->repository->emailTypesData();
        $emails = $this->getEmailsList();

        return view('admin::system-email.index', compact('emails', 'emailTo'));
    }

    /**
     * Display form to create new System Email
     *
     * @return view
     */
    public function create()
    {
        $emailTo = $this->repository->emailTypesData();
        $emails = $this->getEmailsList();

        return view('admin::system-email.create', compact('emails', 'emailTo'));
    }

    /**
     * Listing of email templates in dropdown compatible array format
     * 
     * @return response
     */
    public function getEmailsList()
    {
        $emails = [];
        $emailTo = $this->repository->emailTypesData();
        $allEmails = $this->repository->data();
        //Filter to show own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $allEmails = $allEmails->filter(function($item) {
                    if ($item->created_by == Auth::guard('admin')->user()->id) {
                        return $item;
                    }
                })->values();
        }
        foreach ($allEmails as $email) {
            $email_type_desc = $emailTo[$email['email_type']];
            $emails[$email_type_desc][$email['id']] = $email['description'];
        }

        return $emails;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\SystemEmailCreateRequest $request
     * @return json encoded Response
     */
    public function store(SystemEmailCreateRequest $request)
    {
        $response = $this->repository->create($request->all());
        $emailTo = $this->repository->emailTypesData();
        $emails = $this->getEmailsList();
        $response['form'] = view('admin::system-email.dropdown', compact('emails', 'emailTo'))->render();

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified system email.
     *
     * @param  Modules\Admin\Models\SystemEmail $systemEmail
     * @return json encoded Response
     */
    public function edit(SystemEmail $systemEmail)
    {
        $emailTo = $this->repository->emailTypesData();

        $response['success'] = true;
        $response['form'] = view('admin::system-email.edit', compact('emailTo', 'systemEmail'))->render();

        return response()->json($response);
    }

    /**
     * Store a updated resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\SystemEmailUpdateRequest $request, Modules\Admin\Models\SystemEmail $systemEmail 
     * @return json encoded Response
     */
    public function update(SystemEmailUpdateRequest $request, SystemEmail $systemEmail)
    {
        $response = $this->repository->update($request->all(), $systemEmail);
        $emailTo = $this->repository->emailTypesData();
        $emails = $this->getEmailsList();

        $response['form'] = view('admin::system-email.dropdown', compact('emails', 'emailTo'))->render();

        return response()->json($response);
    }
}
