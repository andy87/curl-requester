<?php

/** @var andy87\curl_requester\Curl $curl */

//GET зпрос
$response = $curl->get( 'vk.com/id806034' )->response(); // string

// Получение ответа в качестве объекта с запросом методом POST
$object = $curl->post( 'vk.com/user/add', [ 'name' => 'and_y87' ])->run()->asObject(); // object


// Имитация запроса методом PATCH с получением тестовых данных
$resp = $curl->patch( 'vk.com/user/get', ['id' => 806034])
    ->setTestResponse('{"name" : "Андрей", "do" : "code"}')
    ->run();

//Получение данных
$response   = $resp->asArray(); // ['name' => 'Андрей', 'do'=> 'code']
$http_code  = $resp->http_code;