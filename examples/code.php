<?php

/** @var Curl $curl */

use andy87\curl_requester\Curl;
use andy87\curl_requester\entity\Query;
use andy87\curl_requester\entity\Request;

try {

    //GET зпрос
    $respString = $curl->get( 'vk.com/id806034' )->response(); // string
    $respArray  = $curl->get( 'vk.com/id806034' )->asArray(); // Array
    $respObject = $curl->get( 'vk.com/id806034' )->asObject(); // Object

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

    // Имитация запроса методом post с получением тестовых данных
    $request = $curl->post( 'vk.com/user/get', [])
        ->prepareParams()
        ->disableSSL()
        ->enableRedirect()
        ->setPostFields([])
        ->addContentType('json')
        ->setBasicAuth('12345')
        ->useCookie('cookiename=cookievalue', '/tmp/cookies.txt')
        ->addCurlInfo([])
        ->setCallback( function ( $query, $ch ){
        });

    $a = $request->getQuery();
    $b = $request->getUrl();
    $c = $request->getMethod();
    $d = $request->getHeaders();
    $e = $request->getCurlOptions();
    $f = $request->getPostFields();

    $response = $request->run();

    $query = $response->getQuery();

    $resp = $response->response;


    $ch = Request::createCurlHandler( 'www.vk.com/806034', [
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_POST            => 1,
        CURLOPT_HTTPHEADER      => [ 'some headers' ],
        CURLOPT_POSTFIELDS      => [ 'some params' ]
    ]);

    $resp = curl_exec( $ch );

    curl_close($ch);

    // Аналог(кратная запись)
    $resp = Request::send( 'www.vk.com/806034', [
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_POST            => 1,
        CURLOPT_HTTPHEADER      => [ 'some headers' ],
        CURLOPT_POSTFIELDS      => [ 'some params' ]
    ]);

} catch ( Exception $e ) {

    exit( $e->getMessage() );
}
