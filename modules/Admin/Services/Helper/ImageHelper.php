<?php

/**
 * The helper library class for user image processing ang geting
 *
 *
 * @author Nilesh Pangul <nileshp@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Services\Helper;

use DB;

//use Approached\LaravelImageOptimizer\ImageOptimizer;
//use ImageOptimizer;

class ImageHelper {

    /**
     * upload user avatar
     * @return String
     */
    public static function uploadUserAvatar($fileObj, $user = '', $fileName = '') {
        if (empty($fileName)) {
            $fileName = 'avatar' . '-' . time() . '.' . $fileObj->getClientOriginalExtension();
        }
        $path = self::getUserUploadFolder($user->id);
        $fileObj->move(public_path() . $path, $fileName);

        return $fileName;
    }

    /**
     * get user avatar upload folder path
     * @return String
     */
    public static function getUserUploadFolder($userId = '') {
        if (empty($userId)) {
            $userId = \Auth::guard('admin')->user()->id;
        }
        $path = DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $userId . DIRECTORY_SEPARATOR;
        self::checkDirectory(public_path() . $path);

        return $path;
    }

    /**
     * check and create directory
     */
    public static function checkDirectory($dirPath = '') {
        if (!\File::exists($dirPath)) {
            \File::makeDirectory($dirPath, 0775, true);
        }
    }

    /**
     * get default image link
     */
    public static function getDefaultImageLink() {
        return \URL::asset('images/default-user-icon-profile.png ');
    }

    /**
     * get default image link
     */
    public static function getDefaultCategoryIconLink() {
        return \URL::asset('images/default-offer-category-icon.png');
    }

    /**
     * get default image
     */
    public static function getDefaultImage($class = 'img-thumbnail img-responsive') {
        return \Form::image(self::getDefaultImageLink(), ' ', ['class' => $class]);
    }

    /**
     * check and create directory
     */
    public static function getUserAvatar($userId = '', $avatar = '', $class = 'img-thumbnail img-responsive') {
        $default = self::getDefaultImageLink();
        if (!empty($userId) && empty($avatar)) {
            $user = DB::table('admins')->select('avatar')->where('id', $userId)->first();
            $avatar = $user->avatar;
        }
        if (!empty($avatar)) {

            return \HTML::image(\URL::asset(self::getUserUploadFolder($userId) . $avatar), $avatar, ['class' => $class, 'onerror' => 'this.src="' . $default . '"']);
        }

        return \HTML::image($default, ' ', ['class' => $class]);
    }

    /**
     * upload offer category icon
     * @return String
     */
    public static function uploadOfferCategoryIcon($fileObj, $offercategory = '', $fileName = '') {
        if (empty($fileName)) {
            $fileName = 'offer-category-' . $offercategory->id . '-' . time() . '.' . $fileObj->getClientOriginalExtension();
        }
        $path = self::getOfferCategoryUploadFolder($offercategory->id);
        $fileObj->move(public_path() . $path, $fileName);

        return $fileName;
    }

    /**
     * upload offer category icon
     * @return String
     */
    public static function uploadTestimonialIcon($fileObj, $offercategory = '', $fileName = '') {
        if (empty($fileName)) {
            $fileName = 'testimonial-' . $offercategory->id . '-' . time() . '.' . $fileObj->getClientOriginalExtension();
        }
        $path = self::getTestimonialUploadFolder($offercategory->id);
        $fileObj->move(public_path() . $path, $fileName);

        return $fileName;
    }

    /**
     * get offer category icon upload folder path
     * @return String
     */
    public static function getTestimonialUploadFolder($offercategory = '') {
        $offer_category_folder = 'testimonial';
        $path = DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $offer_category_folder . DIRECTORY_SEPARATOR;
        self::checkDirectory(public_path() . $path);

        return $path;
    }

    /**
     * get offer category icon upload folder path
     * @return String
     */
    public static function getOfferCategoryUploadFolder($offercategory = '') {
        $offer_category_folder = 'offer-category';
        $path = DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $offer_category_folder . DIRECTORY_SEPARATOR;
        self::checkDirectory(public_path() . $path);

        return $path;
    }

    /**
     * check and create directory
     */
    public static function getOfferCategoryIcon($offercategory = '', $category_icon = '', $class = 'img-thumbnail img-responsive') {
        $default = self::getDefaultCategoryIconLink();
        if (!empty($category_icon)) {

            return \HTML::image(\URL::asset(self::getOfferCategoryUploadFolder($offercategory) . $category_icon), $category_icon, ['class' => $class, 'onerror' => 'this.src="' . $default . '"']);
        }

        return \HTML::image($default, ' ', ['class' => $class]);
    }

    /**
     * check and create directory
     */
    public static function getTestimonialIcon($offercategory = '', $category_icon = '', $class = 'img-thumbnail img-responsive') {
        $default = self::getDefaultImageLink();
        if (!empty($category_icon)) {

            return \HTML::image(\URL::asset(self::getTestimonialUploadFolder($offercategory) . $category_icon), $category_icon, ['class' => $class, 'onerror' => 'this.src="' . $default . '"']);
        }

        return \HTML::image($default, ' ', ['class' => $class]);
    }

    /**
     * get page banner image upload folder path
     * @return String
     */
    public static function getPageBannerUploadFolder($pagebanner = '') {
        $page_banner_folder = 'page-banners';
        if (!empty($pagebanner->page_id)) {
            $path = DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $page_banner_folder . DIRECTORY_SEPARATOR . $pagebanner->page_id;
            self::checkDirectory(public_path() . $path);
        } else {
            $path = DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $page_banner_folder . DIRECTORY_SEPARATOR;
        }
        return $path;
    }

    /**
     * upload Page Banner Image
     * @return String
     */
    public static function uploadPageBannerImage($fileObj, $pageBanner = '', $fileName = '') {
        if (empty($fileName)) {
            $fileName = 'page-banner-' . $pageBanner->id . '-' . time() . '.' . $fileObj->getClientOriginalExtension();
        }
        $path = self::getPageBannerUploadFolder($pageBanner);
        $fileObj->move(public_path() . $path, $fileName);

        return $fileName;
    }

    /**
     * check and create directory
     */
    public static function getOfferBannerImage($offerbanner = '', $banner_image = '', $class = 'img-thumbnail img-responsive') {
        $default = self::getDefaultImageLink();
        if (!empty($banner_image)) {

            return \HTML::image(\URL::asset(self::getOfferAllImagesUploadFolder('offer-banner') . $banner_image), $banner_image, ['class' => $class, 'onerror' => 'this.src="' . $default . '"']);
        }

        return \HTML::image($default, ' ', ['class' => $class]);
    }

    /**
     * upload offer image
     * @return String
     */
    public static function uploadOfferAllImages($fileObj, $offerimage, $imgFileName) {
        $fileName = $imgFileName . '-' . $offerimage->id . '-' . time() . '.' . $fileObj->getClientOriginalExtension();
        $path = self::getOfferAllImagesUploadFolder($imgFileName);
        $fileObj->move(public_path() . $path, $fileName);
        return $fileName;
    }

    /**
     * get offer image upload folder path
     * @return String
     */
    public static function getOfferAllImagesUploadFolder($imgFilePath) {
        $offer_folder = 'offers/' . $imgFilePath;
        $path = DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $offer_folder . DIRECTORY_SEPARATOR;
        self::checkDirectory(public_path() . $path);

        return $path;
    }

    /**
     * upload Page OG image
     * @return String
     */
    public static function uploadPageOgImage($fileObj, $pageimage, $imgFileName) {
        $fileName = $imgFileName . '-' . $pageimage->id . '-' . time() . '.' . $fileObj->getClientOriginalExtension();
        $path = self::getPageOgImageUploadFolder($imgFileName);
        $fileObj->move(public_path() . $path, $fileName);
        return $fileName;
    }

    /**
     * get offer image upload folder path
     * @return String
     */
    public static function getPageOgImageUploadFolder($imgFilePath) {
        $path = DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $imgFilePath . DIRECTORY_SEPARATOR;
        self::checkDirectory(public_path() . $path);

        return $path;
    }

    /**
     * get user avatar upload folder path
     * @return String
     */
    public static function getPackageFolder($userId = '') {
        if (empty($userId)) {
            $userId = \Auth::guard('admin')->user()->id;
        }
        $path = DIRECTORY_SEPARATOR . 'img/package_profile_img/' . DIRECTORY_SEPARATOR . $userId . DIRECTORY_SEPARATOR;
        self::checkDirectory(public_path() . $path);

        return $path;
    }

    /**
     * upload user before avatar
     * @return String
     */
    public static function uploadUserBeforeImage($fileObj, $user = '', $type = '', $imageOptimizer) {
        $fileName = '';
        if (empty($fileName)) {
            $fileName = $user[0]['id'] . '-' . time() . '-' . $type . '.' . $fileObj->getClientOriginalExtension();
            $imageOptimizer->optimizeUploadedImageFile($fileObj);
        }

        $path = self::getPackageFolder($user[0]['id']);
        $fileObj->move(public_path() . $path, $fileName);

        return $fileName;
    }

    /**
     * upload user after avatar
     * @return String
     */
    public static function uploadUserAfterImage($fileObj, $user = '', $type = '', $imageOptimizer) {

        $fileName = '';
        if (empty($fileName)) {
            $fileName = $user[0]['id'] . '-' . time() . '-' . $type . '.' . $fileObj->getClientOriginalExtension();
            $imageOptimizer->optimizeUploadedImageFile($fileObj);
        }

        $path = self::getPackageFolder($user[0]['id']);
        $fileObj->move(public_path() . $path, $fileName);
        return $fileName;
    }

    /**
     * check and create directory
     */
    public static function getUserBeforeImage($userId = '', $avatar = '', $class = 'img-thumbnail img-responsive') {
        $default = self::getDefaultImageLink();
        if (!empty($userId) && empty($avatar)) {
            $user = DB::table('member_package_images')->select('before_image')->where('id', $userId)->first();
            $avatar = $user->before_image;
        }
        if (!empty($avatar)) {

            return \HTML::image(\URL::asset(self::getPackageFolder($userId) . $avatar), $avatar, ['class' => $class, 'id' => 'before_img', 'onerror' => 'this.src="' . $default . '"']);
        }

        return \HTML::image($default, ' ', ['class' => $class, 'id' => 'before_img']);
    }

    public static function getUserAfterImage($userId = '', $avatar = '', $class = 'img-thumbnail img-responsive') {
        $default = self::getDefaultImageLink();
        if (!empty($userId) && empty($avatar)) {
            $user = DB::table('member_package_images')->select('after_image')->where('id', $userId)->first();
            $avatar = $user->after_image;
        }
        if (!empty($avatar)) {

            return \HTML::image(\URL::asset(self::getPackageFolder($userId) . $avatar), $avatar, ['class' => $class, 'id' => 'after_img', 'onerror' => 'this.src="' . $default . '"']);
        }

        return \HTML::image($default, ' ', ['class' => $class, 'id' => 'after_img']);
    }

    /**
     * upload product image
     * @return String
     */
    public static function uploadProductImage($fileObj, $product = '', $imageOptimizer) {
        $fileName = '';
        if (empty($fileName)) {
            $fileName = 'product' . '-' . time() . '.' . $fileObj->getClientOriginalExtension();

            $imageOptimizer->optimizeUploadedImageFile($fileObj);
        }
        $path = self::getProductUploadFolder($product->id);

        $fileObj->move(public_path() . $path, $fileName);

        return $fileName;
    }

    /**
     * get product upload folder path
     * @return String
     */
    public static function getProductUploadFolder($productId = '') {
        $path = DIRECTORY_SEPARATOR . 'img/product' . DIRECTORY_SEPARATOR . $productId . DIRECTORY_SEPARATOR;
        self::checkDirectory(public_path() . $path);
        return $path;
    }

    /**
     * check and create directory
     */
    public static function getProductImage($productId = '', $product_image = '', $class = 'img-thumbnail img-responsive') {

        $default = self::getDefaultImageLink();
        if (!empty($productId) && empty($product_image)) {
            $user = DB::table('products')->select('product_image')->where('id', $productId)->first();
            $product_image = $user->product_image;
        }
        if (!empty($product_image)) {

            return \HTML::image(\URL::asset(self::getProductUploadFolder($productId) . $product_image), $product_image, ['class' => $class, 'id' => 'product_image', 'onerror' => 'this.src="' . $default . '"']);
        }

        return \HTML::image($default, ' ', ['class' => $class, 'id' => 'product_image']);
    }

    /**
     * upload offer image
     * @return String
     */
    public static function uploadOfferImage($fileObj, $offer = '', $imageOptimizer) {
        $fileName = '';
        if (empty($fileName)) {
            $fileName = 'offer' . '-' . time() . '.' . $fileObj->getClientOriginalExtension();

            $imageOptimizer->optimizeUploadedImageFile($fileObj);
        }
        $path = self::getOfferUploadFolder($offer->id);

        $fileObj->move(public_path() . $path, $fileName);

        return $fileName;
    }

    /**
     * get offer upload folder path
     * @return String
     */
    public static function getOfferUploadFolder($offerId = '') {
        $path = DIRECTORY_SEPARATOR . 'img/offer' . DIRECTORY_SEPARATOR . $offerId . DIRECTORY_SEPARATOR;
        self::checkDirectory(public_path() . $path);
        return $path;
    }

    /**
     * check and create directory
     */
    public static function getOfferImage($offerId = '', $offer_image = '', $class = 'img-thumbnail img-responsive') {

        $default = self::getDefaultImageLink();
        if (!empty($offerId) && empty($offer_image)) {
            $user = DB::table('offers')->select('offer_image')->where('id', $offerId)->first();
            $offer_image = $user->offer_image;
        }
        if (!empty($offer_image)) {

            return \HTML::image(\URL::asset(self::getOfferUploadFolder($offerId) . $offer_image), $offer_image, ['class' => $class, 'id' => 'offer_image', 'onerror' => 'this.src="' . $default . '"']);
        }

        return \HTML::image($default, ' ', ['class' => $class, 'id' => 'offer_image']);
    }

    /**
     * upload product image
     * @return String
     */
    public static function uploadBcaFile($fileObj, $memberId, $packageId) {
        $fileName = '';
        if (empty($fileName)) {
            $fileName = time() . '.' . $fileObj->getClientOriginalExtension();
        }
        $path = self::getBcaUploadFolder($memberId, $packageId);
        $fileObj->move(public_path() . $path, $fileName);

        return $fileName;
    }

    public static function getBcaUploadFolder($memberId, $packageId) {
        $path = DIRECTORY_SEPARATOR . 'member_bca_data' . DIRECTORY_SEPARATOR . $memberId . '-' . $packageId;
        self::checkDirectory(public_path() . $path);

        return $path;
    }

    public static function getBcaUploadedFolder($memberId = '') {
        $path = DIRECTORY_SEPARATOR . 'img/bca_image' . DIRECTORY_SEPARATOR;
        self::checkDirectory(public_path() . $path);
        return $path;
    }

    public static function updateBcaImage($fileObj, $memberBcaDetails = '', $imageOptimizer) {
        $fileName = '';
        if (empty($fileName)) {
            $fileName = 'bca' . '-' . time() . '.' . $fileObj->getClientOriginalExtension();

            $imageOptimizer->optimizeUploadedImageFile($fileObj);
        }
        $path = self::getBcaUploadedFolder($memberBcaDetails->membr_id);

        $fileObj->move(public_path() . $path, $fileName);

        return $fileName;
    }

    /**
     * check and create directory
     */
    public static function getUserBcaImage($bcaId = '', $bca_image = '', $class = 'img-thumbnail img-responsive') {
        $default = self::getDefaultImageLink();
        if (!empty($bcaId) && empty($bca_image)) {
            $user = DB::table('member_bca_details')->select('bca_image')->where('id', $bcaId)->first();
            $bca_image = $user->bca_image;
        }
        if (!empty($bca_image)) {
            return \HTML::image(\URL::asset(self::getBcaUploadedFolder($bcaId) . $bca_image), $bca_image, ['class' => $class, 'id' => 'bca_image', 'onerror' => 'this.src="' . $default . '"']);
        }

        return \HTML::image($default, ' ', ['class' => $class, 'id' => 'bca_image']);
    }

}
