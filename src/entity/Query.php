<?php

namespace andy87\curl_requester\entity;

/**
 *  Class Property
 *
 *      Данные запроса
 *
 * @property string $method
 * @property string $url
 * @property array|string $postFields
 * @property array $headers
 * @property array $curlOptions
 * @property ?string $response
 * @property ?int $httpCode
 * @property array $info
 *
 * @method isGet()
 * @method isPost()
 * @method isPut()
 * @method isPatch()
 * @method isHead()
 * @method isDelete()
 *
 * @package andy87\curl_requester\entity
 */
class Query
{
    // Constants

    const OK = 200;



    // Property

    /** @var string Метод запроса */
    public string $method;

    /** @var string URL адрес на который отправляется запрос */
    public string $url;

    /** @var array|string данные запроса */
    public $postFields = '';

    /** @var array заголовки запроса */
    public array $headers = [];

    /** @var array CURL OPTIONS запроса */
    public array $curlOptions = [];

    /** @var string|null ответ сервера */
    public ?string $response = null;

    /** @var ?int код ответа сервера */
    public ?int $httpCode = null;

    /** @var array Список информации о запросе из `curl_getinfo()` */
    public array $info = [
        CURLINFO_HTTP_CODE
    ];



    // Magic

    /**
     * Magic
     *
     * @param string $name
     * @param mixed $arguments
     * @return bool|void
     */
    public function __call( string $name, $arguments )
    {
        if ( strpos($name, 'is') === 0 )
        {
            $method = strtoupper( substr( $name, 2) );

            return $this->isMethod( $method );
        }
    }



    // Methods

    /**
     * Определение метода
     *
     * @param string $method
     * @return bool
     */
    public function isMethod( string $method ): bool
    {
        return $this->method === $method;
    }

}