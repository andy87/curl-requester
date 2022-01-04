<?php

namespace andy87\curl_requester\entity;

use Exception;
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
    // Constants

    /** @var string события */
    const EVENT_RUN             = 'run';
    const EVENT_BEFORE_REQUEST  = 'before_request';
    const EVENT_AFTER_REQUEST   = 'after_request';



    // Property

    /** @var object|Method|Get|Post|Put|Patch|Head|Delete $query объект запроса */
    private object $query;



    // Magic

    /**
     * Конструктор объекта
     *
     * @param object|Method|Get|Post|Put|Patch|Head|Delete $query
     */
    public function __construct( object $query )
    {
        $this->query = $query;
    }



    // Methods

    /**
     * Конструктор cURL ресурса. Возвращает cURL ресурс или response запроса
     *
     * @param string $url URL куда делается запрос
     * @param array $options опции для cURL
     *
     * @return resource
     */
    public static function createCurlHandler( string $url, array $options = [] )
    {
        $ch = curl_init( $url );

        curl_setopt_array( $ch, $options );

        return $ch;
    }

    /**
     * @param string $url
     * @param array $options
     *
     * @return bool|string
     */
    public static function send( string $url, array $options = [] )
    {
        $ch = self::createCurlHandler( $url, $options );

        $resp = curl_exec( $ch );

        curl_close( $ch );

        return $resp;
    }

    /**
     * Выполнение запроса через class
     *
     * @return Response
     *
     * @throws Exception
     */
    public function run(): Response
    {
        $query = $this->query->getQuery();

        $query->behavior( self::EVENT_RUN, $query, null );

        $curlOptions[ CURLOPT_RETURNTRANSFER ] = 1;

        if ( $this->isExtend( $query ) )
        {
            $curlOptions[ CURLOPT_POST ] = 1;
            $curlOptions[ CURLOPT_HTTPHEADER ] = $query->headers;
            $curlOptions[ CURLOPT_POSTFIELDS ] = $this->preparePostField();
        }

        foreach ( $curlOptions as $option => $value )
        {
            // фильтр для предотвращения перезаписи опций которые задаются по стандарту
            if ( !isset($query->curlOptions[ $option ]) )
            {
                $query->curlOptions[ $option ] = $value;
            }
        }

        if ( $this->query->isTest() )
        {
            // если запрос тестовый
            $query->response = $this->query->tests[ Method::KEY_RESPONSE ];
            $query->httpCode = $this->query->tests[ Method::KEY_HTTP_CODE ];

        } else {

            $ch = self::createCurlHandler( $query->url, $query->curlOptions );

            $query->behavior( self::EVENT_BEFORE_REQUEST, $query, $ch );

            $query->response  = curl_exec( $ch );

            foreach ( $query->info as $code )
            {
                $query->info[ $code ] = curl_getinfo( $ch, $code );
            }

            $query->httpCode = $query->info[ CURLINFO_HTTP_CODE ];

            $query->behavior( self::EVENT_AFTER_REQUEST, $query, $ch );

            curl_close( $ch );
        }

        return new Response( $query );
    }

    /**
     * Определение необходимости дополнительных опций запроса
     *
     * @param Query $query
     *
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