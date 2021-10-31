<?php

namespace andy87\curl_requester;

use andy87\curl_requester\entity\Method;
use andy87\curl_requester\entity\methods\{Get,Post,Put,Patch,Head,Delete};

/**
 * Class `Curl`
 *
 *  Строитель/Декоратор над вызовами cURL функций
 *  Единая точка входа для осуществления запросов
 *
 * @method Get get( string $url, array $params, ?string $logger, ?bool $logger_status )
 * @method Post post( string $url, array $params, ?string $logger, ?bool $logger_status )
 * @method Put put( string $url, array $params, ?string $logger, ?bool $logger_status )
 * @method Patch patch( string $url, array $params, ?string $logger, ?bool $logger_status )
 * @method Head head( string $url, array $params, ?string $logger, ?bool $logger_status )
 * @method Delete delete( string $url, array $params, ?string $logger, ?bool $logger_status )
 *
 * @package andy87\src
 */
class Curl
{
    /** @var ?string ORM/ActiveRecord ЛОггер запросов */
    const LOGGER = null;

    /** @var bool Статус активности логгера по умолчанию  */
    const DEFAULT_LOGGER_STATUS = false;

    /** @var string[] список поддерживаемых методов */
    const METHOD_LIST = [
        Method::GET     => Get::class,
        Method::POST    => Post::class,
        Method::PUT     => Put::class,
        Method::PATCH   => Patch::class,
        Method::HEAD    => Head::class,
        Method::DELETE  => Delete::class,
    ];



    /**
     * Magic
     *
     * @param string $name
     * @param mixed $arg
     * @return null|Delete|Get|Head|Method|Patch|Post|Put
     */
    public function __call( string $name, mixed $arg )
    {
        $method = strtoupper($name);

        if ( isset( self::METHOD_LIST[ $method ] ) )
        {
            $method = self::METHOD_LIST[ $method ];

            $url  = $arg[0];
            $data = $arg[1] ?? [];

            $url = ( $method == Get::class ) ? $this->constructUri( $url, $data ) : $this->constructUri( $url );

            return new $method( $url, $data, static::LOGGER, static::DEFAULT_LOGGER_STATUS );
        }

        return null;
    }



    /**
     * Конструктор `uri` с возможностью добавить в строку GET параметры
     *
     * @param string $url
     * @param ?array $params
     * @return string
     */
    public function constructUri( string $url, ?array $params = null ): string
    {
        $resp = mb_strtolower( $url );

        if ( strpos( $resp, '://') === false ) $resp = 'https://' . $resp;

        if ( !empty($params) ) {
            $symbol = ( strpos($resp, '?' ) === false ) ? '?' : '&';
            $resp  .=  ( $symbol . http_build_query( $params ) );
        }

        return $resp;
    }
}