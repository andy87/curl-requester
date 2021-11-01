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
 * @method Get get( string $url, array $params = [], ?string $logger = null, ?bool $logger_status = null )
 * @method Post post( string $url, array $params = [], ?string $logger = null, ?bool $logger_status = null )
 * @method Put put( string $url, array $params = [], ?string $logger = null, ?bool $logger_status = null )
 * @method Patch patch( string $url, array $params = [], ?string $logger = null, ?bool $logger_status = null )
 * @method Head head( string $url, array $params = [], ?string $logger = null, ?bool $logger_status = null )
 * @method Delete delete( string $url, array $params = [], ?string $logger = null, ?bool $logger_status = null )
 *
 * @package andy87\curl_requester
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
     * @param array $arg
     * @return null|Delete|Get|Head|Method|Patch|Post|Put
     */
    public function __call( string $name, array $arg = [] )
    {
        $method = strtoupper($name);

        if ( $method = ( static::METHOD_LIST[ $method ] ?? null ) )
        {
            $url  = $arg[0];
            $data = ( ( $method === Get::class ) ? ( $arg[1] ?? null ) : null ) ;
            $url  = $this->constructUri( $url, $data );

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