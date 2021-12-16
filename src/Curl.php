<?php

namespace andy87\curl_requester;

use andy87\curl_requester\entity\Method;
use andy87\curl_requester\entity\methods\{Get,Post,Put,Patch,Head,Delete};

/**
 * Class `Curl`
 *
 *  Единая точка входа для осуществления запросов
 *  Строитель/Декоратор над cURL функциями
 *
 * @method Get get( string $url, ?array $params = null )
 * @method Post post( string $url, ?array $params = null )
 * @method Put put( string $url, ?array $params = null )
 * @method Patch patch( string $url, ?array $params = null )
 * @method Head head( string $url, ?array $params = null )
 * @method Delete delete( string $url, ?array $params = null )
 *
 * @package andy87\curl_requester
 */
class Curl
{
    // Constants

    /** @var string just const */
    const HTTPS = 'https';

    /** @var string[] список поддерживаемых методов */
    const METHOD_LIST = [
        Method::GET     => Get::class,
        Method::POST    => Post::class,
        Method::PUT     => Put::class,
        Method::PATCH   => Patch::class,
        Method::HEAD    => Head::class,
        Method::DELETE  => Delete::class,
    ];



    // Magic

    /**
     * Обработчик вызова несуществующих методов
     *
     * @param string $name
     * @param array $arg
     * @return null|Method|Get|Post|Patch|Put|Head|Delete
     */
    public function __call( string $name, array $arg = [] )
    {
        $method = strtoupper( $name );

        if ( $method = ( static::METHOD_LIST[$method] ?? false ) )
        {
            $data = $arg[1] ?? [];
            $url  = $this->constructUri( $arg[0], ( ( $method === Get::class ) ? $data : [] ) );

            return new $method( $url, $data, );
        }

        return null;
    }



    // Methods

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

        if ( strpos( $resp, '://') === false ) $resp = ( $_SERVER['REQUEST_SCHEME'] ?? self::HTTPS ) . '://' . $resp;

        if ( !empty($params) ) {
            $symbol = ( strpos($resp, '?' ) === false ) ? '?' : '&';
            $resp  .=  ( $symbol . http_build_query( $params ) );
        }

        return $resp;
    }
}