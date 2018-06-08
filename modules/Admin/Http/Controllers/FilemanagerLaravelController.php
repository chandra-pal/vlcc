<?php
/**
 * The class for Filemanager for Laravel app.
 *
 *
 * @author Gaurav Patel <gauravp@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace Modules\Admin\Http\Controllers;

use FilemanagerLaravel;
use Config;
use Auth;
use File;

class FilemanagerLaravelController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        // $this->middleware('auth');
    }

    public function getShow()
    {
        $rootFolderPath = $_SERVER['DOCUMENT_ROOT'];
        $configUrlFiles = Config::get('filemanager.url-files');
        $folderPath = base_path() . $configUrlFiles;
        $folderPath = str_replace($rootFolderPath, '', $folderPath);

        $loggedUserId = Auth::guard('admin')->user()->id;
        return view('admin::filemanager.layout', compact('loggedUserId', 'folderPath'));
    }

    public function getConnectors()
    {
        $folderPath = $this->getUserAccessPath();
        $extraConfig = array('dir_filemanager' => '/admintheme');
        $f = FilemanagerLaravel::Filemanager($extraConfig);
        $f->connector_url = url('/') . '/admin/filemanager/connectors';
        $f->setFileRoot($folderPath);
        $f->run();
    }

    public function postConnectors()
    {
        $folderPath = $this->getUserAccessPath();
        $extraConfig = array('dir_filemanager' => '/admintheme');
        $f = FilemanagerLaravel::Filemanager($extraConfig);
        $f->connector_url = url('/') . '/admin/filemanager/connectors';
        $f->setFileRoot($folderPath);
        $f->run();
    }

    private function getUserAccessPath()
    {
        $configUrlFiles = Config::get('filemanager.url-files');
        // Folder path
        $folderPath = base_path() . $configUrlFiles;

        if (Auth::guard('admin')->check()) {
            //$userFolderExists = Str::contains($configUrlFiles, '/' . Auth::guard('admin')->user()->id . '/');
            $folderPath = $folderPath . Auth::guard('admin')->user()->id;
            // Check for logged in user's user files folder path exist or not
            if (!File::exists($folderPath)) {
                // Create use specific folder first
                $result = File::makeDirectory($folderPath, 0775);
                if ($result) {
                    //Set the filemanage user files path in config
                    $configUrlFiles = $configUrlFiles . Auth::guard('admin')->user()->id . '/';
                }
            }
        }
        return $folderPath;
    }
}
