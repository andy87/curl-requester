<?php

namespace andy87\curl_requester\entity\methods;

use andy87\curl_requester\entity\Method;

/**
 *  Class `Get`
 *
 * @package andy87\curl_requester\entity\methods
 */
class Get extends Method
{
    const SELF_METHOD = self::GET;

    /**
     * Получение ответа на запрос
     *
     * @param ?bool $use_logger TRUE = писать логи / FALSE = не писать логи
     * @return ?string
     */
    public function response( ?bool $use_logger = null ): ?string
    {
        if ( !$use_logger ) return file_get_contents( $this->query->url );

        return $this->run( $use_logger ?? $this->logger_status )->response;
    }
}