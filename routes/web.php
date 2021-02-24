<?php

Route::group(['namespace' => 'Subash\Khalti\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::get('khalti/payment/success', [
        'as'   => 'khalti.payment.success',
        'uses' => 'KhaltiController@paymentSuccess',
    ]);
    Route::get('khalti/payment/failure', [
        'as'   => 'khalti.payment.failure',
        'uses' => 'KhaltiController@paymentFailure',
    ]);
});