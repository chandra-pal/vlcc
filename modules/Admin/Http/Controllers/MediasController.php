<?php
/**
 * The class for Filemanager for medias.
 *
 *
 * @author Gaurav Patel <gauravp@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace Modules\Admin\Http\Controllers;

use File;
use Illuminate\Support\Str;
use FilemanagerLaravel;
use Auth;
use Config;
use Session;

class MediasController extends Controller
{

    /**
     * Create a new FilemanagerController instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Show the media panel.
     *
     * @return Response
     */
    public function index()
    {
        $url = Config::get('filemanager.url');
        return view('admin::filemanager.index', compact('url'));
    }
}
