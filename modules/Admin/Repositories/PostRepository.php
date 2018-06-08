<?php
/**
 * The repository class for managing post actions.
 *
 *
 * @author Bhawna Thadhani <bhawnat@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace Modules\Admin\Repositories;

use Modules\Admin\Models\Post;
use Cache;

class PostRepository extends BaseRepository
{

  /**
     * Create a new repository instance.
     *
     * @param  Modules\Admin\Models\Post $post
     * @return void
     */
    public function __construct(Post $post)
    {
        $this->model = $post;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function data($request, $params = [])
    {

        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(Post::table())->remember($cacheKey, $this->ttlCache, function() {
            return Post::orderBy('name')->get();
        });

        return $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function listPostData()
    {
        $cacheKey = str_replace(['\\'], [''], __METHOD__);
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(Post::table())->remember($cacheKey, $this->ttlCache, function() {
            return Post::orderBY('name')->lists('name', 'id');
        });

        return $response;
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  Form data posted from ajax $inputs
     * @return $result array with status and message elements
     */
    public function store($inputs)
    {
        try {
            $post = new $this->model;
            $allColumns = $post->getTableColumns($post->getTable());
            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $post->$key = $value;
                }
            }

            $save = $post->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/post.post')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/post.post')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/post.post')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/post.post')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update a post.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\Post $post
     * @return $result array with status and message elements
     */
    public function update($inputs, $post)
    {
        try {

            foreach ($inputs as $key => $value) {
                if (isset($post->$key)) {
                    $post->$key = $value;
                }
            }

            $save = $post->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/post.post')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/post.post')]);
            }

            return $response;
        } catch (Exception $e) {

            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/post.post')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/post.post')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }
}
