<?php

namespace andy87\curl_requester\entity;

/**
 * Abstract class `Ext`
 *
 *  Расширеный функционал для class @property Method
 *
 * @property bool $params_is_ready если данные уже готовы и их не надо `http_build_query` ставится `TRUE`
 *
 * @package andy87\curl_requester\entity
 */
abstract class Ext extends Method
{
    // Property

    /** @var bool при готовы данных без необходимости `http_build_query()` значение == `TRUE` */
    public bool $params_is_ready = false;



    // Magic

    /**
     * Конструктор объекта
     *
     * @param string $url куда отправляется запрос
     * @param ?array $data параметры запроса
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
     * @param null|array|string $params параметры для запроса
     *
     * @return $this
     */
    public function prepareParams( $params = null ): self
    {
        if ( $params ) $this->query->postFields = $params;

        $this->params_is_ready = true;

        return $this;
    }
}