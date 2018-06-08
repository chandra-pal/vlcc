<?php

Route::model('user', 'Modules\Admin\Models\User');
Route::model('link-category', 'Modules\Admin\Models\LinkCategory');
Route::model('config_categories', 'Modules\Admin\Models\ConfigCategory');
Route::model('links', 'Modules\Admin\Models\Links');
Route::model('config_settings', 'Modules\Admin\Models\ConfigSetting');
Route::model('user_type', 'Modules\Admin\Models\UserType');
Route::model('system_emails', 'Modules\Admin\Models\SystemEmail');
Route::model('myprofile', 'Modules\Admin\Models\User');
Route::model('faq_categories', 'Modules\Admin\Models\FaqCategory');
Route::model('faqs', 'Modules\Admin\Models\Faq');
Route::model('manage_pages', 'Modules\Admin\Models\Page');
Route::model('ipaddress', 'Modules\Admin\Models\IpAddress');
Route::model('countries', 'Modules\Admin\Models\Country');
Route::model('states', 'Modules\Admin\Models\State');
Route::model('cities', 'Modules\Admin\Models\City');
Route::model('locations', 'Modules\Admin\Models\Locations');
Route::model('rooms', 'Modules\Admin\Models\Room');
Route::model('machines', 'Modules\Admin\Models\Machine');
Route::model('machine_type', 'Modules\Admin\Models\MachineType');
//Route::model('staff', 'Modules\Admin\Models\Staff');
//Route::model('posts', 'Modules\Admin\Models\Post');
Route::model('machines-availability', 'Modules\Admin\Models\MachineAvailability');
Route::model('rooms-availability', 'Modules\Admin\Models\RoomAvailability');
Route::model('staff-availability', 'Modules\Admin\Models\StaffAvailability');
Route::model('menu-group', 'Modules\Admin\Models\MenuGroup');
Route::model('diet-schedule-type', 'Modules\Admin\Models\DietScheduleType');
Route::model('diet-plan', 'Modules\Admin\Models\DietPlan');
Route::model('food', 'Modules\Admin\Models\Food');
Route::model('measurement', 'Modules\Admin\Models\Measurement');
Route::model('reminder-type', 'Modules\Admin\Models\ReminderType');
Route::model('diet-plan-detail', 'Modules\Admin\Models\DietPlanDetail');
Route::model('recommendation', 'Modules\Admin\Models\Recommendation');
Route::model('activity-type', 'Modules\Admin\Models\ActivityType');
Route::model('member-diet-plan', 'Modules\Admin\Models\MemberDietPlan');
Route::model('member-activity-log', 'Modules\Admin\Models\MemberActivityLog');
Route::model('member-diet-log', 'Modules\Admin\Models\MemberDietLog');
Route::model('member-otp', 'Modules\Admin\Models\MemberOtp');
Route::model('center', 'Modules\Admin\Models\Center');
Route::model('member-profile-image', 'Modules\Admin\Models\MemberProfileImage');
Route::model('deviation', 'Modules\Admin\Models\Deviation');
Route::model('session-bookings', 'Modules\Admin\Models\SessionBookings');
Route::model('food-type', 'Modules\Admin\Models\FoodType');
Route::model('product-recommendation', 'Modules\Admin\Models\ProductRecommendation');
Route::model('offer-recommendation', 'Modules\Admin\Models\OfferRecommendation');
Route::model('session-resources', 'Modules\Admin\Models\SessionResources');
Route::model('reports', 'Modules\Admin\Models\Reports');
Route::model('beauty-service', 'Modules\Admin\Models\BeautyServices');

Route::bind('usertype_links', function($type) {
    $userTypeRepository = new Modules\Admin\Repositories\UserTypeRepository(new Modules\Admin\Models\UserType);
    $userTypeLinks = $userTypeRepository->getLinksByUserType($type);
    if ($userTypeLinks->isEmpty()) {
        abort(404);
        Log::info("Invalid Input");
    }
    return $userTypeLinks;
});
