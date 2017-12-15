<?php

/*
|--------------------------------------------------------------------------
| Master Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are related to master methods
|
*/

Route::group(['middleware' => ['auth']], function () {

    /**
     * Master Admin Module(s)
     */
    Route::group(['middleware' => ['master']], function () {

        /**
         * Master Module(s)
         */

        /** User (Include Employee) **/
        Route::get('user', 'UserController@index');
        Route::get('user/create', 'UserController@create');
        Route::post('user', 'UserController@store');
        Route::get('user/edit/{id}', 'UserController@edit');
        Route::patch('user/{id}', 'UserController@update');
        Route::delete('user/{id}', 'UserController@destroy');

        /** Area **/
        Route::get('area', 'Master\AreaController@index');
        Route::post('area', 'Master\AreaController@store');
        Route::get('area/edit/{id}', 'Master\AreaController@edit');
        Route::patch('area/{id}', 'Master\AreaController@update');
        Route::delete('area/{id}', 'Master\AreaController@destroy');

        /** District **/
        Route::get('district', 'Master\DistrictController@index');
        Route::post('district', 'Master\DistrictController@store');
        Route::get('district/edit/{id}', 'Master\DistrictController@edit');
        Route::patch('district/{id}', 'Master\DistrictController@update');
        Route::delete('district/{id}', 'Master\DistrictController@destroy');

        /** Channel **/
        Route::get('channel', 'Master\ChannelController@index');
        Route::post('channel', 'Master\ChannelController@store');
        Route::get('channel/edit/{id}', 'Master\ChannelController@edit');
        Route::patch('channel/{id}', 'Master\ChannelController@update');
        Route::delete('channel/{id}', 'Master\ChannelController@destroy');

        /** Sub Channel **/
        Route::get('subchannel', 'Master\SubChannelController@index');
        Route::post('subchannel', 'Master\SubChannelController@store');
        Route::get('subchannel/edit/{id}', 'Master\SubChannelController@edit');
        Route::patch('subchannel/{id}', 'Master\SubChannelController@update');
        Route::delete('subchannel/{id}', 'Master\SubChannelController@destroy');

        /** Distributor **/
        Route::get('distributor', 'Master\DistributorController@index');
        Route::post('distributor', 'Master\DistributorController@store');
        Route::get('distributor/edit/{id}', 'Master\DistributorController@edit');
        Route::patch('distributor/{id}', 'Master\DistributorController@update');
        Route::delete('distributor/{id}', 'Master\DistributorController@destroy');

        /** Store **/
        Route::get('store', 'Master\StoreController@index'); 
        Route::get('store/create', 'Master\StoreController@create');       
        Route::post('store', 'Master\StoreController@store');
        Route::get('store/edit/{id}', 'Master\StoreController@edit');
        Route::patch('store/{id}', 'Master\StoreController@update');
        Route::delete('store/{id}', 'Master\StoreController@destroy');

        /** Employee **/
        /*
        Route::get('employee', 'Master\EmployeeController@index');
        Route::get('employee/create', 'Master\EmployeeController@create');
        Route::post('employee', 'Master\EmployeeController@store');
        Route::get('employee/edit/{id}', 'Master\EmployeeController@edit');
        Route::patch('employee/{id}', 'Master\EmployeeController@update');
        Route::delete('employee/{id}', 'Master\EmployeeController@destroy');
        */

        /** Place **/
        Route::get('place', 'Master\PlaceController@index');        
        Route::post('place', 'Master\PlaceController@store');
        Route::get('place/edit/{id}', 'Master\PlaceController@edit');
        Route::patch('place/{id}', 'Master\PlaceController@update');
        Route::delete('place/{id}', 'Master\PlaceController@destroy');

        /** Group Competitor **/
        Route::get('groupcompetitor', 'Master\GroupCompetitorController@index');        
        Route::post('groupcompetitor', 'Master\GroupCompetitorController@store');
        Route::get('groupcompetitor/edit/{id}', 'Master\GroupCompetitorController@edit');
        Route::patch('groupcompetitor/{id}', 'Master\GroupCompetitorController@update');
        Route::delete('groupcompetitor/{id}', 'Master\GroupCompetitorController@destroy');

        /** Group Product **/
        Route::get('groupproduct', 'Master\GroupProductController@index');
        Route::post('groupproduct', 'Master\GroupProductController@store');
        Route::get('groupproduct/edit/{id}', 'Master\GroupProductController@edit');
        Route::patch('groupproduct/{id}', 'Master\GroupProductController@update');
        Route::delete('groupproduct/{id}', 'Master\GroupProductController@destroy');

        /** Group **/
        Route::get('group', 'Master\GroupController@index');        
        Route::post('group', 'Master\GroupController@store');
        Route::get('group/edit/{id}', 'Master\GroupController@edit');
        Route::patch('group/{id}', 'Master\GroupController@update');
        Route::delete('group/{id}', 'Master\GroupController@destroy');

        /** Category **/
        Route::get('category', 'Master\CategoryController@index');        
        Route::post('category', 'Master\CategoryController@store');
        Route::get('category/edit/{id}', 'Master\CategoryController@edit');
        Route::patch('category/{id}', 'Master\CategoryController@update');
        Route::delete('category/{id}', 'Master\CategoryController@destroy');

        /** Product **/
        Route::get('product', 'Master\ProductController@index');        
        Route::post('product', 'Master\ProductController@store');
        Route::get('product/edit/{id}', 'Master\ProductController@edit');
        Route::patch('product/{id}', 'Master\ProductController@update');
        Route::delete('product/{id}', 'Master\ProductController@destroy');

        /** POS Material **/
        Route::get('posm', 'Master\PosmController@index');
        Route::post('posm', 'Master\PosmController@store');
        Route::get('posm/edit/{id}', 'Master\PosmController@edit');
        Route::patch('posm/{id}', 'Master\PosmController@update');
        Route::delete('posm/{id}', 'Master\PosmController@destroy');

        /** Price **/
        Route::get('price', 'Master\PriceController@index');
        Route::post('price', 'Master\PriceController@store');
        Route::get('price/edit/{id}', 'Master\PriceController@edit');
        Route::patch('price/{id}', 'Master\PriceController@update');
        Route::delete('price/{id}', 'Master\PriceController@destroy');

        /** Target **/
        Route::get('target', 'Master\TargetController@index');
        Route::post('target', 'Master\TargetController@store');
        Route::get('target/edit/{id}', 'Master\TargetController@edit');
        Route::patch('target/{id}', 'Master\TargetController@update');
        Route::delete('target/{id}', 'Master\TargetController@destroy');

        /** Product Focus **/
        Route::get('productfocus', 'Master\ProductFocusController@index');
        Route::post('productfocus', 'Master\ProductFocusController@store');
        Route::get('productfocus/edit/{id}', 'Master\ProductFocusController@edit');
        Route::patch('productfocus/{id}', 'Master\ProductFocusController@update');
        Route::delete('productfocus/{id}', 'Master\ProductFocusController@destroy');

        /** Fanspage **/
        // Route::resource('fanspage', 'Master\FanspageController');
        Route::get('fanspage', 'Master\FanspageController@index');
        Route::post('fanspage', 'Master\FanspageController@store');
        Route::get('fanspage/edit/{id}', 'Master\FanspageController@edit');
        Route::patch('fanspage/{id}', 'Master\FanspageController@update');
        Route::delete('fanspage/{id}', 'Master\FanspageController@destroy');

        /** MessageToAdmin **/
        // Route::resource('MessageToAdmin', 'Master\MessageToAdminController');
        Route::get('messageToAdmin', 'MessageToAdminController@index');
        Route::post('messageToAdmin', 'MessageToAdminController@store');
        Route::get('messageToAdmin/show/{id}', 'MessageToAdminController@show');
        Route::get('messageToAdmin/edit/{id}', 'MessageToAdminController@edit');
        Route::patch('messageToAdmin/{id}', 'MessageToAdminController@update');
        Route::delete('messageToAdmin/{id}', 'MessageToAdminController@destroy');

        /** Feedback Category **/
        // Route::resource('fanspage', 'Master\FanspageController');
        Route::get('feedbackCategory', 'Master\FeedbackCategoryController@index');
        Route::post('feedbackCategory', 'Master\FeedbackCategoryController@store');
        Route::get('feedbackCategory/edit/{id}', 'Master\FeedbackCategoryController@edit');
        Route::patch('feedbackCategory/{id}', 'Master\FeedbackCategoryController@update');
        Route::delete('feedbackCategory/{id}', 'Master\FeedbackCategoryController@destroy');

        /** Feedback Question **/
        // Route::resource('fanspage', 'Master\FanspageController');
        Route::get('feedbackQuestion', 'Master\FeedbackQuestionController@index');
        Route::post('feedbackQuestion', 'Master\FeedbackQuestionController@store');
        Route::get('feedbackQuestion/edit/{id}', 'Master\FeedbackQuestionController@edit');
        Route::patch('feedbackQuestion/{id}', 'Master\FeedbackQuestionController@update');
        Route::delete('feedbackQuestion/{id}', 'Master\FeedbackQuestionController@destroy');

        /** Feedback Answer **/
        // Route::resource('fanspage', 'Master\FanspageController');
        Route::get('feedbackAnswer', 'Master\FeedbackAnswerController@index');
        Route::post('feedbackAnswer', 'Master\FeedbackAnswerController@store');
        Route::get('feedbackAnswer/edit/{id}', 'Master\FeedbackAnswerController@edit');
        Route::patch('feedbackAnswer/{id}', 'Master\FeedbackAnswerController@update');
        Route::delete('feedbackAnswer/{id}', 'Master\FeedbackAnswerController@destroy');
    });

    /**
     * Master Admin & Admin Module(s)
     */

    Route::middleware(['admin'])->group(function () {

        /** News **/
        Route::get('news', 'Master\NewsController@index');
        Route::get('news/create', 'Master\NewsController@create');
        Route::post('news', 'Master\NewsController@store');
        Route::get('news/edit/{id}', 'Master\NewsController@edit');
        Route::patch('news/{id}', 'Master\NewsController@update');
        Route::delete('news/{id}', 'Master\NewsController@destroy');

        /** Product Knowledge **/
        Route::get('product-knowledge', 'Master\ProductKnowledgeController@index');
        Route::get('product-knowledge/create', 'Master\ProductKnowledgeController@create');
        Route::post('product-knowledge', 'Master\ProductKnowledgeController@store');
        Route::get('product-knowledge/edit/{id}', 'Master\ProductKnowledgeController@edit');
        Route::patch('product-knowledge/{id}', 'Master\ProductKnowledgeController@update');
        Route::delete('product-knowledge/{id}', 'Master\ProductKnowledgeController@destroy');

        /** Quiz **/
        Route::get('quiz', 'Master\QuizController@index');
        Route::get('quiz/create', 'Master\QuizController@create');
        Route::post('quiz', 'Master\QuizController@store');
        Route::get('quiz/edit/{id}', 'Master\QuizController@edit');
        Route::patch('quiz/{id}', 'Master\QuizController@update');
        Route::delete('quiz/{id}', 'Master\QuizController@destroy');
        


    });

    /**
     * Reporting Module(s)
     */
    Route::get('sellinreport', 'Master\ReportController@sellInIndex');
    Route::get('selloutreport', 'Master\ReportController@sellOutIndex');
    Route::get('retconsumentreport', 'Master\ReportController@retConsumentIndex');
    Route::get('retdistributorreport', 'Master\ReportController@retDistributorIndex');
    Route::get('freeproductreport', 'Master\ReportController@freeproductIndex');
    Route::get('tbatreport', 'Master\ReportController@tbatIndex');
    Route::get('sohreport', 'Master\ReportController@sohIndex');
    Route::get('sosreport', 'Master\ReportController@sosIndex');
    Route::get('displaysharereport', 'Master\ReportController@displayShareIndex');
    Route::get('maintenancerequest', 'Master\ReportController@maintenanceRequestIndex');
    Route::get('competitoractivityreport', 'Master\ReportController@competitorActivityIndex');
    Route::get('promoactivityreport', 'Master\ReportController@promoActivityIndex');
    Route::get('posmactivityreport', 'Master\ReportController@posmActivityIndex');
    Route::get('attendancereport', 'Master\ReportController@attendanceIndex');
    Route::get('attendancereport/detail/{id}', 'Master\ReportController@attendanceForm');

    

    /** Profile **/
    Route::get('profile', 'ProfileController@index');
    Route::post('profile', 'ProfileController@update');
        
});


