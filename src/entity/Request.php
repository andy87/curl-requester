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
    /** @var ?string ORM Класс ActiveRecord для логирования */
    private ?string $logger;


    /** @var object|Method|Get|Post|Put|Patch|Head|Delete $query объект запроса */
    private object $query;


    /**
     * Construct
     *
     * @param object|Method|Get|Post|Put|Patch|Head|Delete $query
     * @param ?string $logger
     */
    public function __construct( object $query, ?string $logger )
    {
        $this->query = $query;
        $this->logger = $logger;
    }

    /**
     * @param string $url
     * @param array $options
     * @return resource
     */
    public static function createCurlHandler(string $url, array $options = [] )
    {
        $ch = curl_init( $url );

        curl_setopt_array( $ch, $options );

        return $ch;
    }


    /**
     * Выполнение запроса
     *
     * @param bool $use_logger TRUE = писать логи / FALSE = не писать логи
     * @return Response
     */
    public function run( bool $use_logger ): Response
    {
        $query = $this->query->getQuery();

        $curlOptions[ CURLOPT_RETURNTRANSFER ] = true;

        if ( $this->isExtend( $query ) ) {
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
            $query->response  = $this->query->testResponse;
            $query->http_code = $this->query->testHttpCode;

        } else {

            $ch = self::createCurlHandler( $query->url, $query->curlOptions );

            $query->response  = curl_exec( $ch );

            $info = [];

            foreach ( $query->info as $code ) $info[ $code ] = curl_getinfo( $ch, $code );

            curl_close($ch);

            $query->info = $info;

            $query->http_code = $query->info[ CURLINFO_HTTP_CODE ];

            $this->query->initCallBack( $query );
        }

        $response = new Response( $query );

        if ( $use_logger ) $this->addLog();

        return $response;
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



    /**
     * Добавление лога
     */
    public function addLog()
    {
        if ( $log = $this->logger )
        {
            $log = new $log();

            $property = $this->query->getQuery();

            $log->url        = $property->url;
            $log->method     = $property->method;
            $log->headers    = (string) json_encode( $property->headers, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
            $log->data       = (string) json_encode( $property->postFields, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
            $log->response   = (string) $property->response;
            $log->http_code  = (string) $property->http_code;
            $log->group      = $this->query->getGroup();
            $log->comment    = $this->query->getComment();

            $log->save();
        }
    }
}