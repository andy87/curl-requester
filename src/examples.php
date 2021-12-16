<?php

/** @var Curl $curl */

use andy87\curl_requester\Curl;
use andy87\curl_requester\entity\Query;

//GET зпрос
$resp = $curl->get( 'vk.com/id806034' )->response(); // string

// Получение ответа в качестве объекта с запросом методом POST
$object = $curl->post( 'vk.com/user/add', [ 'name' => 'and_y87' ])->run()->asObject(); // object


// Имитация запроса методом PATCH с получением тестовых данных
$response = $curl->patch( 'vk.com/user/get', ['id' => 806034])
    ->setTestResponse('{"name" : "Андрей", "do" : "code"}')
    ->run();

//Получение данных
$res      = $response->asArray(); // ['name' => 'Андрей', 'do'=> 'code']
$httpCode = $response->httpCode;

// CallBack
$request = $curl->post('url' );
$request->setCallback(function ( Query $query, $curlHandler){

    if ( $query->httpCode !== Query::OK )
    {
        $errors = curl_error( $curlHandler );

        print_r($errors);
        die;
    }
});
$resp = $request->run()->response;