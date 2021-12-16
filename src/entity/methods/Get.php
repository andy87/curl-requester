<?php

namespace andy87\curl_requester\entity\methods;

use andy87\curl_requester\entity\Method;
use Exception;

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
     * @return ?string
     *
     * @throws Exception
     */
    public function response(): ?string
    {
        return $this->run()->response;
    }
}