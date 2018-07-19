<?php
Route::group(['prefix' => 'admin', 'namespace' => 'Modules\Admin\Http\Controllers'], function() {
    Route::post('/send-notification', ['uses' => 'RecommendationController@sendNotification', 'permission' => 'index']);
});


Route::group(['prefix' => 'admin', 'namespace' => 'Modules\Admin\Http\Controllers', 'before' => 'auth'], function() {
    Route::get('/', ['uses' => 'DashboardController@index', 'permission' => 'index']);
    Route::get('/dashboard', ['uses' => 'DashboardController@index', 'permission' => 'index']);

    //User Management
    Route::get('user/data', ['as' => 'admin.user.apilist', 'uses' => 'UserController@getData', 'permission' => 'index']);
    Route::get('user/trashed', ['as' => 'admin.user.trashedlisting.index', 'uses' => 'UserController@trashed', 'permission' => 'index']);
    Route::get('user/trashed-data', ['as' => 'admin.user.apitrashedlist.index', 'uses' => 'UserController@getTrashedData', 'permission' => 'index']);
    Route::get('user/links', ['as' => 'admin.user.apiuserlinks.index', 'uses' => 'UserController@getUserLinks', 'permission' => 'index']);
    Route::post('user/group-action', ['as' => 'admin.user.groupaction', 'uses' => 'UserController@groupAction', 'permission' => 'update']);
    Route::post('user/check-avalability', ['as' => 'admin.user.checkfieldavalability.update', 'uses' => 'UserController@checkAvalability', 'permission' => 'update']);
    Route::post('user/sync-user', ['as' => 'admin.user.sync-user', 'uses' => 'UserController@syncUser', 'permission' => 'index']);
    Route::resource('user', 'UserController');

    //Link Category
    Route::get('link-category/data', ['as' => 'admin.link-category.apilist', 'uses' => 'LinkCategoryController@getData', 'permission' => 'index']);
    Route::post('link-category/group-action', ['as' => 'admin.link-category.groupaction', 'uses' => 'LinkCategoryController@groupAction', 'permission' => 'update']);
    Route::resource('link-category', 'LinkCategoryController');

    //Permission Link Management
    Route::get('links/linkData/{lid}', ['as' => 'admin.links.linkList', 'uses' => 'LinksController@getLinksData', 'permission' => 'index']);
    Route::get('links/data', ['as' => 'admin.links.apilist', 'uses' => 'LinksController@getData', 'permission' => 'index']);
    Route::post('links/group-action', ['as' => 'admin.links.groupaction', 'uses' => 'LinksController@groupAction', 'permission' => 'update']);
    Route::resource('links', 'LinksController');

    //Login Process
    Route::post('auth/authenticate', ['as' => 'admin.auth.authenticate', 'uses' => 'Auth\AuthController@authUsername', 'permission' => 'index']);

    //Manage IP Adresses
    Route::get('ipaddress/data', ['as' => 'admin.ipaddress.apilist', 'uses' => 'IpAddressController@getData', 'permission' => 'index']);
    Route::post('ipaddress/group-action', ['as' => 'admin.ipaddress.groupaction', 'uses' => 'IpAddressController@groupAction', 'permission' => 'update']);
    Route::resource('ipaddress', 'IpAddressController');

    //Configuration Setting Management
    Route::get('config-settings/data', ['as' => 'admin.config-settings.list', 'uses' => 'ConfigSettingController@getData', 'permission' => 'index']);
    Route::resource('config-settings', 'ConfigSettingController');

    //Configuration Categories Management
    Route::get('config-categories/data', ['as' => 'admin.config-categories.list', 'uses' => 'ConfigCategoryController@getData', 'permission' => 'index']);
    Route::resource('config-categories', 'ConfigCategoryController');

    //User Types Management
    Route::get('user-type/data', ['as' => 'admin.user-type.list', 'uses' => 'UserTypeController@getData', 'permission' => 'index']);
    Route::resource('user-type', 'UserTypeController');

    //System Emails
    Route::resource('system-emails', 'SystemEmailController');

    //Pages Management
    Route::get('manage-pages/data', ['as' => 'admin.manage-pages.apilist', 'uses' => 'ManagePagesController@getData', 'permission' => 'index']);
    Route::post('manage-pages/group-action', ['as' => 'admin.manage-pages.groupaction', 'uses' => 'ManagePagesController@groupAction', 'permission' => 'update']);
    Route::resource('manage-pages', 'ManagePagesController');

    //User Type Links
    Route::resource('usertype-links', 'UserTypeLinksController');

    //Manage FAQ Category

    Route::get('faq-categories/data', ['as' => 'admin.faq-categories.list', 'uses' => 'FaqCategoryController@getData', 'permission' => 'index']);
    Route::resource('faq-categories', 'FaqCategoryController');

    //Manage FAQ
    Route::get('faqs/data', ['as' => 'admin.faqs.list', 'uses' => 'FaqController@getData', 'permission' => 'index']);
    Route::post('faqs/group-action', ['as' => 'admin.faqs.groupaction', 'uses' => 'FaqController@groupAction', 'permission' => 'update']);
    Route::resource('faqs', 'FaqController');

    //Admin My Profile
    Route::put('myprofile/update-avatar', ['as' => 'admin.myprofile.update-avatar', 'uses' => 'MyProfileController@updateAvatar', 'permission' => 'update']);
    Route::put('myprofile/change-password', ['as' => 'admin.myprofile.change-password', 'uses' => 'MyProfileController@changePassword', 'permission' => 'update']);
    Route::resource('myprofile', 'MyProfileController');

    //Manage Country Category
    Route::get('countries/data', ['as' => 'admin.countries.list', 'uses' => 'CountryController@getData', 'permission' => 'index']);
    Route::resource('countries', 'CountryController');

    //Manage State
    Route::get('states/data', ['as' => 'admin.states.list', 'uses' => 'StateController@getData', 'permission' => 'index']);
    Route::resource('states', 'StateController');

    //Mange Cities
    Route::get('cities/stateData/{cid}', ['as' => 'admin.cities.stateList', 'uses' => 'CityController@getStateData', 'permission' => 'index']);
    Route::get('cities/data', ['as' => 'admin.cities.list', 'uses' => 'CityController@getData', 'permission' => 'index']);
    Route::resource('cities', 'CityController');

    //Manage Locations
    Route::get('locations/stateData/{cid}', ['as' => 'admin.locations.stateList', 'uses' => 'LocationsController@getStateData', 'permission' => 'index']);
    Route::get('locations/cityData/{cid}', ['as' => 'admin.locations.cityList', 'uses' => 'LocationsController@getCityData', 'permission' => 'index']);
    Route::get('locations/data', ['as' => 'admin.locations.list', 'uses' => 'LocationsController@getData', 'permission' => 'index']);
    Route::resource('locations', 'LocationsController');

    //Manage Posts
    //Route::get('posts/data', ['as' => 'admin.posts.list', 'uses' => 'PostController@getData', 'permission' => 'index']);
    //Route::resource('posts', 'PostController');
    //View User Login Logs
    Route::get('login-logs/data', ['as' => 'admin.login-logs.apilist', 'uses' => 'LoginLogsController@getData', 'permission' => 'index']);
    Route::post('login-logs/group-action', ['as' => 'admin.login-logs.groupaction', 'uses' => 'LoginLogsController@groupAction', 'permission' => 'update']);
    Route::resource('login-logs', 'LoginLogsController');

    //Manage Rooms
    Route::get('rooms/data', ['as' => 'admin.rooms.list', 'uses' => 'RoomController@getData', 'permission' => 'index']);
    Route::resource('rooms', 'RoomController');

    //Manage Machines Type
    Route::get('machine-type/data', ['as' => 'admin.machine-type.list', 'uses' => 'MachineTypeController@getData', 'permission' => 'index']);
    Route::resource('machine-type', 'MachineTypeController');

    //Manage Machines
    Route::get('machines/data', ['as' => 'admin.machines.list', 'uses' => 'MachineController@getData', 'permission' => 'index']);
    Route::post('machines/group-action', ['as' => 'admin.machines.groupaction', 'uses' => 'MachineController@groupAction', 'permission' => 'update']);
    Route::resource('machines', 'MachineController');

    //Manage Machines Availability
    Route::get('machines-availability/machineData/{cid}', ['as' => 'admin.machines-availability.machineList', 'uses' => 'MachineAvailabilityController@getMachineData', 'permission' => 'index']);
    Route::get('machines-availability/data', ['as' => 'admin.machines-availability.list', 'uses' => 'MachineAvailabilityController@getData', 'permission' => 'index']);
    Route::post('machines-availability/group-action', ['as' => 'admin.machines-availability.groupaction', 'uses' => 'MachineAvailabilityController@groupAction', 'permission' => 'update']);
    Route::resource('machines-availability', 'MachineAvailabilityController');

    //Manage Rooms Availability
    Route::get('rooms-availability/roomData/{cid}', ['as' => 'admin.rooms-availability.roomList', 'uses' => 'RoomAvailabilityController@getRoomData', 'permission' => 'index']);
    Route::get('rooms-availability/data', ['as' => 'admin.rooms-availability.list', 'uses' => 'RoomAvailabilityController@getData', 'permission' => 'index']);
    Route::post('rooms-availability/group-action', ['as' => 'admin.rooms-availability.groupaction', 'uses' => 'RoomAvailabilityController@groupAction', 'permission' => 'update']);
    Route::resource('rooms-availability', 'RoomAvailabilityController');

    //Manage Staff Availability
    Route::get('staff-availability/staffData/{cid}', ['as' => 'admin.staff-availability.staffList', 'uses' => 'StaffAvailabilityController@getStaffData', 'permission' => 'index']);
    Route::get('staff-availability/data', ['as' => 'admin.staff-availability.list', 'uses' => 'StaffAvailabilityController@getData', 'permission' => 'index']);
    Route::post('staff-availability/group-action', ['as' => 'admin.staff-availability.groupaction', 'uses' => 'StaffAvailabilityController@groupAction', 'permission' => 'update']);
    Route::resource('staff-availability', 'StaffAvailabilityController');


    //Manage Staff
    //Route::get('staff/data', ['as' => 'admin.staff.list', 'uses' => 'StaffController@getData', 'permission' => 'index']);
    //Route::resource('staff', 'StaffController');
    //File Management
    Route::get('filemanager/show', ['as' => 'admin.filemanager.show', 'uses' => 'FilemanagerLaravelController@getShow']);
    Route::get('filemanager/connectors', ['as' => 'admin.filemanager', 'uses' => 'FilemanagerLaravelController@getConnectors']);
    Route::post('filemanager/connectors', ['as' => 'admin.filemanager', 'uses' => 'FilemanagerLaravelController@postConnectors']);
    Route::resource('medias', 'MediasController');

    //Menu Groups
    Route::get('menu-group/data', ['as' => 'admin.menu-group.apilist', 'uses' => 'MenuGroupController@getData', 'permission' => 'index']);
    Route::post('menu-group/group-action', ['as' => 'admin.menu-group.groupaction', 'uses' => 'MenuGroupController@groupAction', 'permission' => 'update']);
    Route::resource('menu-group', 'MenuGroupController');

    //Diet Schedule Type
    Route::get('diet-schedule-type/data', ['as' => 'admin.diet-schedule-type.list', 'uses' => 'DietScheduleTypeController@getData', 'permission' => 'index']);
    Route::resource('diet-schedule-type', 'DietScheduleTypeController');

    //Manage Diet Plans
    Route::get('diet-plan/data', ['as' => 'admin.diet-plan.list', 'uses' => 'DietPlanController@getData', 'permission' => 'index']);
    Route::resource('diet-plan', 'DietPlanController');

    //Manage Foods
    Route::get('food/data', ['as' => 'admin.food.list', 'uses' => 'FoodController@getData', 'permission' => 'index']);
    Route::resource('food', 'FoodController');

    //Manage Measurements
    Route::get('measurement/data', ['as' => 'admin.measurement.list', 'uses' => 'MeasurementController@getData', 'permission' => 'index']);
    Route::resource('measurement', 'MeasurementController');

    //Manage Reminder Types
    Route::get('reminder-type/data', ['as' => 'admin.reminder-type.list', 'uses' => 'ReminderTypeController@getData', 'permission' => 'index']);
    Route::resource('reminder-type', 'ReminderTypeController');

    //Manage Diet Plan Details
    Route::get('diet-plan-detail/data', ['as' => 'admin.diet-plan-detail.list', 'uses' => 'DietPlanDetailController@getData', 'permission' => 'index']);
    Route::get('diet-plan-detail/get-food-list', ['as' => 'admin.diet-plan-detail.get-food-list', 'uses' => 'DietPlanDetailController@getFoodList', 'permission' => 'index']);
    Route::resource('diet-plan-detail', 'DietPlanDetailController');

    //Manage Recommendations
    Route::get('recommendation/data/{mid}', ['as' => 'admin.recommendation.list', 'uses' => 'RecommendationController@getData', 'permission' => 'index']);
    Route::resource('recommendation', 'RecommendationController');

    //Manage Activity Types
    Route::get('activity-type/data', ['as' => 'admin.activity-type.list', 'uses' => 'ActivityTypeController@getData', 'permission' => 'index']);
    Route::resource('activity-type', 'ActivityTypeController');

    //Manage Member Diet Plan
    Route::get('member-diet-plan/get-member-diet-plan', ['as' => 'admin.member-diet-plan.get-member-diet-plan', 'uses' => 'MemberDietPlanController@getPlan', 'permission' => 'index']);
    Route::post('member-diet-plan/data', ['as' => 'admin.member-diet-plan.list', 'uses' => 'MemberDietPlanController@getData', 'permission' => 'index']);
    Route::post('member-diet-plan/select-new-food', ['as' => 'admin.member-diet-plan.select_food', 'uses' => 'MemberDietPlanController@getFoodList', 'permission' => 'index']);
    Route::post('member-diet-plan/add-new-food', ['as' => 'admin.member-diet-plan.add_food', 'uses' => 'MemberDietPlanController@addDieticianFood', 'permission' => 'index']);
    Route::resource('member-diet-plan', 'MemberDietPlanController');
    Route::get('member-diet-plan/get-food-details/{fid}', ['as' => 'admin.member-diet-plan.get-food-details', 'uses' => 'MemberDietPlanController@getFoodDetails', 'permission' => 'index']);
    Route::post('member-diet-plan/foodListByFoodType', ['as' => 'admin.member-diet-plan.get-foodlist-byfoodtype', 'uses' => 'MemberDietPlanController@getFoodListByFoodType', 'permission' => 'index']);
    Route::post('member-diet-plan/get-calories', ['as' => 'admin.member-diet-plan.get-calories', 'uses' => 'MemberDietPlanController@getCalories', 'permission' => 'index']);
    Route::get('download-diet-history', ['as' => 'admin.member-diet-plan.download-diet-history', 'uses' => 'MemberDietPlanController@downloadDietHistory', 'permission' => 'index']);

    //Manage Member Activity Log
    Route::get('member-activity-log/get-deviation', ['as' => 'admin.member-activity-log.get-deviation', 'uses' => 'MemberActivityLogController@getDeviation', 'permission' => 'index']);
    Route::get('member-activity-log/data', ['as' => 'admin.member-activity-log.list', 'uses' => 'MemberActivityLogController@getData', 'permission' => 'index']);
    Route::resource('member-activity-log', 'MemberActivityLogController');

    //Manage Member Diet Log
    Route::get('member-diet-log/get-food-list', ['as' => 'admin.member-diet-log.get-food-list', 'uses' => 'MemberDietLogController@getFoodList', 'permission' => 'index']);

    Route::post('member-diet-log/data', ['as' => 'admin.member-diet-log.list', 'uses' => 'MemberDietLogController@getData', 'permission' => 'index']);
    Route::get('member-diet-log/{mid}', ['as' => 'admin.member-diet-log.index', 'uses' => 'MemberDietLogController@index', 'permission' => 'index']);
    Route::get('member-diet-log/get-diet-plan-details/{mid}', ['as' => 'admin.member-diet-log.get-diet-plan-details', 'uses' => 'MemberDietLogController@getDietPlanDetails', 'permission' => 'index']);
    Route::post('member-diet-log/get-member-diet-recommendation', ['as' => 'admin.member-diet-log.get-member-diet-recommendation', 'uses' => 'MemberDietLogController@getMemberDietRecommendation', 'permission' => 'index']);
    Route::resource('member-diet-log', 'MemberDietLogController');
    Route::get('member-diet-log/get-food-details/{fid}', ['as' => 'admin.member-diet-log.get-food-details', 'uses' => 'MemberDietLogController@getFoodDetails', 'permission' => 'index']);
    Route::post('member-diet-log/get-new-row', ['as' => 'admin.member-diet-log.get-new-row', 'uses' => 'MemberDietLogController@getNewRow', 'permission' => 'index']);

    //Manage Member OTP
    Route::get('member-otp/data/{mid}', ['as' => 'admin.member-otp.list', 'uses' => 'MemberOtpController@getData', 'permission' => 'index']);
    Route::resource('member-otp', 'MemberOtpController');

    //Manage Members
    Route::get('members/{memberId}/display', ['as' => 'admin.members.display', 'uses' => 'MembersController@display', 'permission' => 'index']);
    Route::post('member/packages', ['as' => 'admin.members.packages', 'uses' => 'MembersController@memberPackages', 'permission' => 'index']);
    Route::get('members/data', ['as' => 'admin.members.list', 'uses' => 'MembersController@getData', 'permission' => 'index']);
    Route::post('members/centerwise-members', ['as' => 'admin.members.centerwise-members', 'uses' => 'MembersController@getCenterWiseMembersList', 'permission' => 'index']); // Renaming route
    Route::get('members/{memberId}/display-member-details', ['as' => 'admin.members.display-member-details', 'uses' => 'MembersController@displayMemberDetails', 'permission' => 'index']);
    Route::post('members/{memberId}/edit-member', ['as' => 'admin.members.edit-member', 'uses' => 'MembersController@editMember', 'permission' => 'index']);
    Route::resource('members', 'MembersController');

    //Manage Centers
    // Fetch Members list from centers
    //Route::post('center/members', ['as' => 'admin.center.memberslist', 'uses' => 'CenterController@getCenterWiseMembersList', 'permission' => 'index']); // Renaming route
    Route::get('center/stateData/{cid}', ['as' => 'admin.center.stateList', 'uses' => 'CenterController@getStateData', 'permission' => 'index']);
    Route::get('center/cityData/{cid}', ['as' => 'admin.center.cityList', 'uses' => 'CenterController@getCityData', 'permission' => 'index']);
    Route::get('center/data', ['as' => 'admin.center.list', 'uses' => 'CenterController@getData', 'permission' => 'index']);
    Route::resource('center', 'CenterController');

    //Manage Availability
    Route::post('availability/check-session-time', ['as' => 'admin.availability.check-session-time', 'uses' => 'AvailabilityController@checkSessionTime', 'permission' => 'index']);

    Route::get('availability/data', ['as' => 'admin.availability.list', 'uses' => 'AvailabilityController@getData', 'permission' => 'index']);
    Route::resource('availability', 'AvailabilityController');

    //Manage Sessions
    Route::post('session-bookings/fetch-resource', ['as' => 'admin.session-bookings.fetch-resource', 'uses' => 'SessionBookingsController@fetchResources', 'permission' => 'index']);

    Route::post('session-bookings/fetch-availability', ['as' => 'admin.session-bookings.fetch-availability', 'uses' => 'SessionBookingsController@fetchAvailability', 'permission' => 'index']);

    Route::post('session-bookings/get-availability-list', ['as' => 'admin.session-bookings.get-availability-list', 'uses' => 'SessionBookingsController@getAvailabilityList', 'permission' => 'index']);
    Route::post('session-bookings/get-center-availability-list', ['as' => 'admin.session-bookings.get-center-availability-list', 'uses' => 'SessionBookingsController@getCenterAvailabilityList', 'permission' => 'index']);

    Route::post('session-bookings/data', ['as' => 'admin.session-bookings.list', 'uses' => 'SessionBookingsController@getData', 'permission' => 'index']);
    Route::resource('session-bookings', 'SessionBookingsController');

    Route::get('booking-history', ['as' => 'admin.session-bookings.booking-history', 'uses' => 'SessionBookingsController@bookingHistory']);
	Route::get('download-booking-history', ['as' => 'admin.session-bookings.download-booking-history', 'uses' => 'SessionBookingsController@downloadBookingHistory', 'permissions' => 'index']);

    Route::get('view-todays-sessions', ['as' => 'admin.view-todays-sessions.list', 'uses' => 'SessionBookingsController@viewTodaysSessions', 'permission' => 'index']);
    Route::post('view-todays-sessions/data', ['as' => 'admin.view-todays-sessions.data', 'uses' => 'SessionBookingsController@getTodaysSessions', 'permission' => 'index']);
    Route::post('check-session-booking', ['as' => 'admin.session-bookings.check_session_booking', 'uses' => 'SessionBookingsController@checkSessionBooking', 'permission' => 'index']);
    Route::post('members/packages', ['as' => 'admin.session-bookings.packageList', 'uses' => 'SessionBookingsController@getPackagesList', 'permission' => 'index']);
    Route::post('members/services', ['as' => 'admin.session-bookings.serviceList', 'uses' => 'SessionBookingsController@getServicesList', 'permission' => 'index']);
    Route::post('update-session-status', ['as' => 'admin.session-bookings.updateSessionStatus', 'uses' => 'SessionBookingsController@updateSessionStatus', 'permission' => 'index']);

    Route::get('calendar/view', ['as' => 'admin.calendar.view', 'uses' => 'FullCalendarController@index', 'permission' => 'index']);

    //Manage Member Profile Images
    Route::get('member-profile-image/get-customer-packages/{pid}', ['as' => 'admin.member-profile-image.get-customer-packages', 'uses' => 'MemberProfileImageController@getPackages', 'permission' => 'index']);
    Route::get('member-profile-image/data/{mid}', ['as' => 'admin.member-profile-image.list', 'uses' => 'MemberProfileImageController@getData', 'permission' => 'index']);
    Route::resource('member-profile-image', 'MemberProfileImageController');

    //Manage Member Diet Deviation
    Route::get('member-diet-deviation/{dateScheduleTypeId}', ['as' => 'admin.member-diet-deviation.index', 'uses' => 'DeviationController@deviations', 'permission' => 'index']);
    Route::post('member-diet-deviation/data', ['as' => 'admin.member-diet-deviation.list', 'uses' => 'DeviationController@getData', 'permission' => 'index']);
    Route::post('member-diet-deviation/getListData', ['as' => 'admin.member-diet-deviation.list', 'uses' => 'DeviationController@getListData', 'permission' => 'index']);
    Route::resource('member-diet-deviation', 'DeviationController');

    //Manage Notifications
    Route::post('notifications/read-notifications', ['as' => 'admin.notifications.read_notifications', 'uses' => 'NotificationController@readNotifications', 'permission' => 'index']);
    Route::get('notifications/data', ['as' => 'admin.notifications.list', 'uses' => 'NotificationController@getData', 'permission' => 'index']);
    Route::resource('notifications', 'NotificationController');

    //Manage CPR

    Route::post('cpr/store-medical-review', ['as' => 'admin.cpr.store-medical-review', 'uses' => 'CPRController@storeMedicalReview', 'permission' => 'index']);
    Route::post('cpr/store-measurement-record', ['as' => 'admin.cpr.store-measurement-record', 'uses' => 'CPRController@storeMemberMeasurementRecord', 'permission' => 'index']);
    Route::post('cpr/store-skin-hair-analysis', ['as' => 'admin.cpr.store-skin-hair-analysis', 'uses' => 'CPRController@storeSkinHairAnalysis', 'permission' => 'index']);
    Route::post('cpr/store-review-fitness-activity-records', ['as' => 'admin.cpr.store-review-fitness-activity-records', 'uses' => 'CPRController@storeReviewFitnessActivity', 'permission' => 'index']);
    Route::post('cpr/store-medical-assessment', ['as' => 'admin.cpr.store-medical-assessment', 'uses' => 'CPRController@storeMedicalAssessment', 'permission' => 'index']);
    Route::post('cpr/upload-csv', ['as' => 'admin.cpr.upload-csv', 'uses' => 'CPRController@uploadCsv', 'permission' => 'index']);
    Route::post('cpr/store-fitness-assessment', ['as' => 'admin.cpr.store-fitness-assessment', 'uses' => 'CPRController@storeFitnessAssessment', 'permission' => 'index']);
    Route::post('cpr/store-dietary-assessment', ['as' => 'admin.cpr.store-dietary-assessment', 'uses' => 'CPRController@storeDietaryAssessment', 'permission' => 'index']);
    Route::get('cpr/get-bca-record', ['as' => 'admin.cpr.get-bca-record', 'uses' => 'CPRController@getBcaRecord', 'permission' => 'index']);
    Route::get('cpr/get-measurement-record', ['as' => 'admin.cpr.get-measurement-record', 'uses' => 'CPRController@getMeasurementRecord', 'permission' => 'index']);
    Route::get('cpr/get-session-record', ['as' => 'admin.cpr.get-session-record', 'uses' => 'CPRController@getSessionRecord', 'permission' => 'index']);
    Route::get('cpr/get-review-record', ['as' => 'admin.cpr.get-review-record', 'uses' => 'CPRController@getReviewRecord', 'permission' => 'index']);
    Route::get('cpr/get-member-measurement-record', ['as' => 'admin.cpr.get-member-measurement-record', 'uses' => 'CPRController@getMemberMeasurementRecord', 'permission' => 'index']);
    Route::post('cpr/data', ['as' => 'admin.cpr.list', 'uses' => 'CPRController@getData', 'permission' => 'index']);
    Route::get('cpr/{session_id?}', ['as' => 'admin.cpr.index', 'uses' => 'CPRController@index', 'permission' => 'index']);
    Route::resource('cpr', 'CPRController', ['except' => 'index']);
    Route::post('cpr/get-new-row', ['as' => 'admin.cpr.get-new-row', 'uses' => 'CPRController@getNewRow', 'permission' => 'index']);
    Route::post('cpr/store-bca-records', ['as' => 'admin.cpr.store-bca-records', 'uses' => 'CPRController@storeBcaRecords', 'permission' => 'index']);
    Route::post('cpr/store-measurements-records', ['as' => 'admin.cpr.store-measurements-records', 'uses' => 'CPRController@storeMeasurementRecords', 'permission' => 'index']);
    Route::post('cpr/store-session-records', ['as' => 'admin.cpr.store-session-records', 'uses' => 'CPRController@storeSessionRecords', 'permission' => 'index']);
    Route::post('cpr/store-session-records-summary', ['as' => 'admin.cpr.store-session-records-summary', 'uses' => 'CPRController@storeSessionRecordsSummary', 'permission' => 'index']);
    Route::post('cpr/check-bca-date', ['as' => 'admin.cpr.check-bca-date', 'uses' => 'CPRController@checkBcaDate', 'permission' => 'index']);
    Route::post('cpr/fetch-dietary-assessment-data', ['as' => 'admin.cpr.fetch-dietary-assessment-data', 'uses' => 'CPRController@fetchDietaryAssessmentData', 'permission' => 'index']);
    Route::post('cpr/fetch-fitness-assessment-data', ['as' => 'admin.cpr.fetch-fitness-assessment-data', 'uses' => 'CPRController@fetchfitnessAssessmentData', 'permission' => 'index']);
    Route::post('cpr/fetch-medical-assessment-data', ['as' => 'admin.cpr.fetch-medical-assessment-data', 'uses' => 'CPRController@fetchMedicalAssessmentData', 'permission' => 'index']);
    Route::post('cpr/fetch-skin-hair-analysis-data', ['as' => 'admin.cpr.fetch-skin-hair-analysis-data', 'uses' => 'CPRController@fetchSkinHairAnalysisData', 'permission' => 'index']);
    Route::post('cpr/get-medical-review-record', ['as' => 'admin.cpr.get-medical-review-record', 'uses' => 'CPRController@fetchMedicalReviewRecord', 'permission' => 'index']);
    Route::post('cpr/cpr-ajax', ['as' => 'admin.cpr.cpr-ajax-data', 'uses' => 'CPRController@fetchCprForm', 'permission' => 'index']);
    Route::post('cpr/send-otp', ['as' => 'admin.cpr.send-otp', 'uses' => 'CPRController@sendOtp', 'permission' => 'index']);
    Route::post('cpr/verify-otp', ['as' => 'admin.cpr.verify-otp', 'uses' => 'CPRController@verifyOtp', 'permission' => 'index']);
    Route::post('cpr/clm-service-execution', ['as' => 'admin.cpr.clm-service-execution', 'uses' => 'CPRController@serviceExecution', 'permission' => 'index']);
    Route::post('cpr/clm-update-service-execution-flag', ['as' => 'admin.cpr.clm-service-execution-flag', 'uses' => 'CPRController@updateServiceExecutionFlag', 'permission' => 'index']);


    //Manage member Activity recommendation
    Route::get('member-activity-recommendation/fetch-calories/{dateScheduleTypeId}', ['as' => 'admin.member-activity-recommendation.fetch-calories', 'uses' => 'MemberActivityRecommendationController@getCalories', 'permission' => 'index']);
    Route::get('member-activity-recommendation/data/{mid}', ['as' => 'admin.member-activity-recommendation.list', 'uses' => 'MemberActivityRecommendationController@getData', 'permission' => 'index']);
    Route::resource('member-activity-recommendation', 'MemberActivityRecommendationController');

    //Food Type
    Route::get('food-type/data', ['as' => 'admin.food-type.list', 'uses' => 'FoodTypeController@getData', 'permission' => 'index']);
    Route::resource('food-type', 'FoodTypeController');

    //Manage Products
    Route::get('products/data', ['as' => 'admin.products.list', 'uses' => 'ProductsController@getData', 'permission' => 'index']);
    Route::resource('products', 'ProductsController');

    //Manage Product Recommendation
    Route::get('product-recommendation/data/{mid?}', ['as' => 'admin.product-recommendation.list', 'uses' => 'ProductRecommendationController@getData', 'permission' => 'index']);
    Route::resource('product-recommendation', 'ProductRecommendationController');

    //Manage Offers
    Route::get('offers/data', ['as' => 'admin.offers.list', 'uses' => 'OffersController@getData', 'permission' => 'index']);
    Route::resource('offers', 'OffersController');

    //Manage Offer Recommendation
    Route::get('offer-recommendation/data/{mid?}', ['as' => 'admin.offer-recommendation.list', 'uses' => 'OfferRecommendationController@getData', 'permission' => 'index']);
    Route::resource('offer-recommendation', 'OfferRecommendationController');

    //Manage Escalation Matrix
    Route::post('cpr/escalation-matrix/add-comment', ['as' => 'admin.escalation-matrix.add-comment', 'uses' => 'EscalationMatrixController@addAthComment', 'permission' => 'index']);
    Route::post('cpr/escalation-matrix/get-comment', ['as' => 'admin.escalation-matrix.get-comment', 'uses' => 'EscalationMatrixController@getAthComment', 'permission' => 'index']);
    Route::get('escalation-matrix/data', ['as' => 'admin.escalation-matrix.list', 'uses' => 'EscalationMatrixController@getData', 'permission' => 'index']);
    Route::resource('escalation-matrix', 'EscalationMatrixController');

    //Session resources
    Route::post('session-resources/fetch-resource', ['as' => 'admin.session-resources.fetch-resource', 'uses' => 'SessionResourcesController@fetchResources', 'permission' => 'index']);
    Route::post('session-resources/resource-availability', ['as' => 'admin.session-resources.resource-availability', 'uses' => 'SessionResourcesController@getResourcesAvailability', 'permission' => 'index']);
    Route::get('session-resources/download', ['as' => 'admin.session-resources.download', 'uses' => 'SessionResourcesController@downloadExcel', 'permission' => 'index']);
    Route::post('session-resources/data-download', ['as' => 'admin.session-resources.data-download', 'uses' => 'SessionResourcesController@setDownloadData', 'permission' => 'index']);
    Route::get('session-resources/data', ['as' => 'admin.session-resources.list', 'uses' => 'SessionResourcesController@getData', 'permission' => 'index']);
    Route::resource('session-resources', 'SessionResourcesController');

    //Manage Reports
    /*     * ** New Custom Route *** */
    Route::get('reports/centerwise-users', ['as' => 'admin.reports.centerwise-users', 'uses' => 'ReportsController@centerWiseUsers', 'permission' => 'index']);

    Route::get('reports/data', ['as' => 'admin.reports.list', 'uses' => 'ReportsController@index', 'permission' => 'index']);
    Route::get('reports/download', ['as' => 'admin.reports.download', 'uses' => 'ReportsController@downloadExcel', 'permission' => 'index']);
    Route::post('reports/view-centerwise-users', ['as' => 'admin.reports.view-centerwise-users', 'uses' => 'ReportsController@viewCenterwiseUsers', 'permission' => 'index']);
//    Route::get('reports/centerwise-login', ['as' => 'admin.reports.centerwise-login', 'uses' => 'ReportsController@centerwiseLogin', 'permission' => 'index']);
//    Route::get('reports/download-centerwise-login', ['as' => 'admin.reports.download-centerwise-login', 'uses' => 'ReportsController@downloadCenterwiseLogin', 'permission' => 'index']);
    //centerwise logged in users
    Route::get('reports/centerwise-logged-users', ['as' => 'admin.reports.centerwise-logged-users', 'uses' => 'ReportsController@centerwiseLoggedUsers', 'permission' => 'index']);
    Route::get('reports/download-centerwise-logged-users', ['as' => 'admin.reports.download-centerwise-logged-users', 'uses' => 'ReportsController@downloadCenterwiseLoggedUsers', 'permission' => 'index']);
    Route::post('reports/view-centerwise-logged-users', ['as' => 'admin.reports.view-centerwise-logged-users', 'uses' => 'ReportsController@viewCenterwiseLoggedUsers', 'permission' => 'index']);

    Route::get('reports/centerwise-customers', ['as' => 'admin.reports.centerwise-customers', 'uses' => 'ReportsController@centerwiseCustomers', 'permission' => 'index']);

    Route::get('reports/download-centerwise-customer', ['as' => 'admin.reports.download-centerwise-customer', 'uses' => 'ReportsController@downloadCenterwiseCustomers', 'permission' => 'index']);

    Route::post('reports/view-centerwise-customer', ['as' => 'admin.reports.view-centerwise-customer', 'uses' => 'ReportsController@viewCenterwiseCustomers', 'permission' => 'index']);
    Route::post('reports/centersListByCityType', ['as' => 'admin.reports.get-centerlist-bycityname', 'uses' => 'ReportsController@getCenterListByCityName', 'permission' => 'index']);

    Route::get('reports/categorywise-customers', ['as' => 'admin.reports.categorywise-customers', 'uses' => 'ReportsController@categorywiseCustomers', 'permission' => 'index']);
    Route::post('reports/view-categorywise-customer', ['as' => 'admin.reports.view-categorywise-customer', 'uses' => 'ReportsController@viewCategorywiseCustomers', 'permission' => 'index']);
    Route::get('reports/download-categorywise-customer', ['as' => 'admin.reports.download-categorywise-customer', 'uses' => 'ReportsController@downloadCategorywiseCustomers', 'permission' => 'index']);

    Route::get('reports/new-users', ['as' => 'admin.reports.new-users', 'uses' => 'ReportsController@newUsers', 'permission' => 'index']);
    Route::post('reports/get-new-users', ['as' => 'admin.reports.get-new-users', 'uses' => 'ReportsController@getNewUsers', 'permission' => 'index']);
    Route::get('reports/download-new-users', ['as' => 'admin.reports.download-new-users', 'uses' => 'ReportsController@downloadNewUsers', 'permission' => 'index']);

    Route::get('reports/userwise-cpr-count', ['as' => 'admin.reports.userwise-cpr-count', 'uses' => 'ReportsController@userwiseCprCount', 'permission' => 'index']);
    Route::post('reports/view-userwise-cpr-count', ['as' => 'admin.reports.view-userwise-cpr-count', 'uses' => 'ReportsController@viewUserwiseCPRCount', 'permission' => 'index']);
    Route::get('reports/download-cpr-count', ['as' => 'admin.reports.download-cpr-count', 'uses' => 'ReportsController@downloadCPRCount', 'permission' => 'index']);

    // Escalation Report
    Route::get('reports/centerwise-escalation', ['as' => 'admin.reports.centerwise-escalation', 'uses' => 'ReportsController@centerwiseEscalation', 'permission' => 'index']);
    Route::post('reports/view-centerwise-escalation', ['as' => 'admin.reports.view-centerwise-escalation', 'uses' => 'ReportsController@viewCenterwiseEscalation', 'permission' => 'index']);
    Route::get('reports/download-centerwise-escalation', ['as' => 'admin.reports.download-centerwise-escalation', 'uses' => 'ReportsController@downloadCenterwiseEscalation', 'permission' => 'index']);

    // Notification Report
    Route::get('reports/centerwise-notification', ['as' => 'admin.reports.centerwise-notification', 'uses' => 'ReportsController@centerwiseNotification', 'permission' => 'index']);
    Route::post('reports/view-centerwise-notification', ['as' => 'admin.reports.view-centerwise-notification', 'uses' => 'ReportsController@viewCenterwiseNotification', 'permission' => 'index']);
    Route::get('reports/download-centerwise-notification', ['as' => 'admin.reports.download-centerwise-notification', 'uses' => 'ReportsController@downloadCenterwiseNotification', 'permission' => 'index']);

    //Route::get('reports/view-centerwise-login', ['as' => 'admin.reports.view-centerwise-login', 'uses' => 'ReportsController@viewCenterwiseLogin', 'permission' => 'index']);
    Route::resource('reports', 'ReportsController');

    //Manage Beauty Services
    Route::get('beauty-service/data', ['as' => 'admin.beauty-service.list', 'uses' => 'BeautyServiceController@getData', 'permission' => 'index']);
    Route::resource('beauty-service', 'BeautyServiceController');

    ################ PLEASE WRITE YOUR ROUTES ABOVE THIS CODE ##################################
    Route::controllers([
        'auth' => 'Auth\AuthController',
        'password' => 'Auth\PasswordController',
    ]);
});
