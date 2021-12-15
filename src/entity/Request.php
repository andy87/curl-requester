<?php

namespace andy87\curl_requester\entity;

use andy87\curl_requester\entity\methods\{Get,Post,Put,Patch,Head,Delete};

/**
 *  Class `Request`
 *
 *  Базовый функционал отправки запроса, получения ответа
 *
 * @property ?string $logger
 * @property object|Method|Get|Post|Put|Patch|Head|Delete $query
 *
 * @package andy87\curl_requester\entity
 */
class Request
{
    // Property

    /** @var object|Method|Get|Post|Put|Patch|Head|Delete $query объект запроса */
    private object $query;



    // Magic

    /**
     * Construct
     *
     * @param object|Method|Get|Post|Put|Patch|Head|Delete $query
     */
    public function __construct( object $query )
    {
        $this->query = $query;
    }



    // Methods

    /**
     * @param string $url
     * @param array $options
     * @return resource
     */
    public static function createCurlHandler( string $url, array $options = [] )
    {
        $ch = curl_init( $url );

        curl_setopt_array( $ch, $options );

        return $ch;
    }

    /**
     * Выполнение запроса
     *
     * @return Response
     */
    public function run(): Response
    {
        $query = $this->query->getQuery();

        $curlOptions[ CURLOPT_RETURNTRANSFER ] = true;

        if ( $this->isExtend( $query ) )
        {
            $curlOptions[ CURLOPT_POST ] = 1;
            $curlOptions[ CURLOPT_HTTPHEADER ] = $query->headers;
            $curlOptions[ CURLOPT_POSTFIELDS ] = $this->preparePostField();
        }

        foreach ( $curlOptions as $option => $value )
        {
            if ( !isset($query->curlOptions[ $option ]) ) $query->curlOptions[ $option ] = $value;
        }

        if ( $this->query->isTest() )
        {
            $query->response  = $this->query->tests[ Method::KEY_RESPONSE ];
            $query->httpCode = $this->query->tests[ Method::KEY_HTTP_CODE ];

        } else {

            $ch = self::createCurlHandler( $query->url, $query->curlOptions );

            $query->response  = curl_exec( $ch );

            $info = [];

            foreach ( $query->info as $code ) $info[ $code ] = curl_getinfo( $ch, $code );

            $query->info = $info;

            $query->httpCode = $query->info[ CURLINFO_HTTP_CODE ];

            $this->query->initCallBack( $query, $ch );

            curl_close($ch);
        }

        return new Response( $query );
    }

    /**
     * Определение необходимости дополнительных опций запроса
     *
     * @param Query $query
     * @return bool
     */
    private function isExtend( Query $query ): bool
    {
        if ( $query->isGet() ) return false;

        return true;
    }

    /**
     * Метод возвращает данные для POST запроса
     *  если `params_is_ready` == TURE значит данные подготовлены к запросу
     *  если `params_is_ready` == FALSE значит надо обработать данные http_build_query()
     *
     * @return array|string
     */
    public function preparePostField()
    {
        $postFields = $this->query->getPostFields();

        return ( $this->query->params_is_ready ) ? $postFields : http_build_query( $postFields );
    }
}