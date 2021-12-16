<?php

namespace andy87\curl_requester\entity;

/**
 * Class `Ext`
 *
 *  Расширеный функционал
 *
 * @property bool $params_is_ready если данные уже готовы и их не надо `http_build_query` ставится `TRUE`
 *
 * @package andy87\curl_requester\entity
 */
abstract class Ext extends Method
{
    // Property

    /** @var bool если данные уже готовы и их не надо `http_build_query` ставится `TRUE` */
    public bool $params_is_ready = false;



    // Magic

    /**
     * Construct
     *
     * @param string $url
     * @param ?array $data
     */
    public function __construct( string $url, ?array $data = null )
    {
        parent::__construct( $url, $data );

        $this->query->curlOptions = [ CURLOPT_CUSTOMREQUEST => static::SELF_METHOD ];

        return $this;
    }



    // Methods

    /**
     * Использовать подготовленные данные для запроса, не нуждающиеся в `http_build_query()`
     *
     * @param null|array|string $params
     * @return $this
     */
    public function prepareParams( $params = null ): self
    {
        if ( $params ) $this->query->postFields = $params;

        $this->params_is_ready = true;

        return $this;
    }
}