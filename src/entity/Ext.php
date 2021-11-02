<?php

namespace andy87\curl_requester\entity;

/**
 * Class `Ext`
 *  Расширеный функционал
 *
 * @property bool $params_is_ready если данные уже готовы и их не надо `http_build_query` ставится `TRUE`
 *
 * @package andy87\curl_requester\entity
 */
abstract class Ext extends Method
{
    /** @var bool если данные уже готовы и их не надо `http_build_query` ставится `TRUE` */
    public bool $params_is_ready = false;


    /**
     * Construct
     *
     * @param string $url
     * @param ?array $data
     * @param ?string $logger
     * @param ?bool $logger_status
     */
    public function __construct( string $url, ?array $data = null, ?string $logger = null, ?bool $logger_status = false )
    {
        parent::__construct( $url, $data, $logger, $logger_status );

        $this->query->curlOptions = [ CURLOPT_CUSTOMREQUEST => static::SELF_METHOD ];

        return $this;
    }




    /**
     * Использовать подготовленные данные для запроса, не нуждающиеся в http_build_query()
     *
     * @param array $postField
     * @return $this
     */
    public function prepareParams( array $postField ): self
    {
        $this->query->postFields = $postField;
        $this->params_is_ready = true;

        return $this;
    }



    /**
     * Установить значение доп.данных запроса
     * @param string $group
     * @return Ext
     */
    public function setGroup( string $group ): Ext
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Установить значение доп.данных запроса
     *
     * @param string $comment
     * @return Ext
     */
    public function setComment( string $comment ): Ext
    {
        $this->comment = $comment;

        return $this;
    }
}