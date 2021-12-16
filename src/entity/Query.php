<?php

namespace andy87\curl_requester\entity;

use Exception;

/**
 *  Class Property
 *
 *      Данные запроса
 *
 * @property string $method Метод запроса
 * @property string $url URL адрес на который отправляется запрос
 * @property array|string $postFields данные запроса
 * @property array $headers заголовки запроса
 * @property array $curlOptions cURL опции запроса
 * @property ?string $response ответ сервера
 * @property ?int $httpCode код ответа сервера
 * @property array $info Список информации о запросе из `curl_getinfo()`
 * @property callable[] $behavior функции вызываемые при событиях
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

    /** @var array cURL опции запроса */
    public array $curlOptions = [];

    /** @var string|null ответ сервера */
    public ?string $response = null;

    /** @var ?int код ответа сервера */
    public ?int $httpCode = null;

    /** @var array Список информации о запросе из `curl_getinfo()` */
    public array $info = [
        CURLINFO_HTTP_CODE
    ];

    /**
     * @var callable[] функции вызываемые при событиях:
     *  - callback
     *  - before_request
     *  - after_request
     */
    protected array $behavior = [];



    // Magic

    /**
     * Обработчик вызова несуществующих методов
     *
     * @param string $name имя вызываемого метода
     * @param mixed $arguments аргументы
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
     * Определение метода запроса
     *
     * @param string $method
     * @return bool
     */
    public function isMethod( string $method ): bool
    {
        return $this->method === $method;
    }

    /**
     * Установить функцию для `events`
     *
     * @param string $event ключ события
     * @param callable $callback вызываемая функция
     */
    public function setEvent( string $event, callable $callback )
    {
        $this->behavior[ $event ] = $callback;
    }

    /**
     * Обработка событий
     *
     * @param Query $query Query object
     * @param resource $ch Curl link
     * @throws Exception
     */
    public function behavior( string $event, Query $query, $ch )
    {
        if ( isset($this->behavior[ $event ]) )
        {
            if ( !is_callable($this->behavior[ $event ]) )
            {
                throw new Exception('behavior event not callable func');
            }

            call_user_func( $this->behavior[ $event ], $query, $ch );
        }
    }
}