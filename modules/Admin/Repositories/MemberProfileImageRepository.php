<?php

/**
 * The repository class for managing member profile image specific actions.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\MemberProfileImage;
use Modules\Admin\Services\Helper\ImageHelper;
use Exception;
use Route;
use Log;
use Cache;
use File;
use Approached\LaravelImageOptimizer\ImageOptimizer;

class MemberProfileImageRepository extends BaseRepository {

    /**
     * Create a new MemberProfileImageRepository instance.
     *
     * @param  Modules\Admin\Models\MemberProfileImage $model
     * @return void
     */
    public function __construct(MemberProfileImage $MemberProfileImage) {
        $this->model = $MemberProfileImage;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function data($params = []) {
        return MemberProfileImage::select([
                    'id', 'member_id', 'package_id', 'before_image', 'after_image'
                ])->whereMemberId($params['member_id'])->orderBy('id')->get();
    }

    public function getPackages($params) {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
//        $response = Cache::tags(MemberProfileImage::table())->remember($cacheKey, $this->ttlCache, function() use($params) {
        return MemberProfileImage::select([
                    'package_name', 'package_id as package_order_id', 'package_validity as validity_date'
                ])->whereMemberId($params['id'])->orderBy('id')->get();
//        });

        return $response;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Form data posted from ajax $inputs
     * @return $result array with status and message elements
     */
    public function create($inputs, ImageOptimizer $imageOptimizer) {
        try {
            $memberProfileImage = new $this->model;

            $whereClause = [
                'id' => $inputs['id'],
            ];
            $availabilityInsertOrUpdate = [
                'member_id' => (int) $inputs['member_id'],
            ];

            $save = $memberProfileImage->updateOrCreate($whereClause, $availabilityInsertOrUpdate);
            if ($inputs['id'] == '') {
                $inputs['id'] = $save->id;
            }
            $memberProfileImage = MemberProfileImage::select()->where(['id' => $save->id])->get();
            if (isset($inputs['before_image']) && '' != $inputs['before_image']) {
                $this->updateBeforeAvatar($inputs, $memberProfileImage, $imageOptimizer);
            }

            if (isset($inputs['after_image']) && '' != $inputs['after_image']) {
                $this->updateAfterAvatar($inputs, $memberProfileImage, $imageOptimizer);
            }

            $imageData = MemberProfileImage::select()->where(['id' => $save->id])->get()->toArray();
            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/member-profile-image.member-package-image')]);
                $response['id'] = $inputs['id'];
                $response['before_image'] = $imageData[0]['before_image'];
                $response['after_image'] = $imageData[0]['after_image'];
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/member-profile-image.member-package-image')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/member-profile-image.member-package-image')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/member-profile-image.member-package-image')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update user image.
     *
     * @param  array  $inputs
     * @param  Modules\Admin\Models\MemberProfileImage $memberProfileImage
     * @return void
     */
    public function updateBeforeAvatar($inputs, $memberProfileImage, $imageOptimizer) {
        $newMemberProfileImage = new $this->model;
        if (!empty($inputs['before_image'])) {
            //unlink old file
            if (!empty($memberProfileImage[0]['before_image'])) {
                File::Delete(public_path() . ImageHelper::getUserUploadFolder($memberProfileImage[0]['id']) . $memberProfileImage[0]['before_image']);
            }
            $type = "before";
            $before_img = ImageHelper::uploadUserBeforeImage($inputs['before_image'], $memberProfileImage, $type, $imageOptimizer);
            $whereClause = [
                'id' => $inputs['id'],
            ];
            $availabilityInsertOrUpdate = [
                'before_image' => $before_img,
            ];
            $save = $newMemberProfileImage->updateOrCreate($whereClause, $availabilityInsertOrUpdate);
        } else {
            $memberProfileImage->save();
        }
    }

    public function updateAfterAvatar($inputs, $memberProfileImage, $imageOptimizer) {
        $newMemberProfileImage = new $this->model;
        if (!empty($inputs['after_image'])) {
            //unlink old file
            if (!empty($memberProfileImage[0]['after_image'])) {
                File::Delete(public_path() . ImageHelper::getUserUploadFolder($memberProfileImage[0]['id']) . $memberProfileImage[0]['after_image']);
            }
            $type = "after";
            $after_img = ImageHelper::uploadUserAfterImage($inputs['after_image'], $memberProfileImage, $type, $imageOptimizer);
            $whereClause = [
                'id' => $inputs['id'],
            ];
            $availabilityInsertOrUpdate = [
                'after_image' => $after_img,
            ];
            $save = $newMemberProfileImage->updateOrCreate($whereClause, $availabilityInsertOrUpdate);
        } else {
            $memberProfileImage->save();
        }
    }

}
