<?php

namespace andy87\curl_requester\entity;

/**
 *  Class `Method`
 *
 *  Общий/родительский класс с базовым функционалом
 *
 * @property Query $query Данные запроса
 * @property array $tests Тестовый ответ
 * @property ?callable $callBack Функция вызываемая после запроса
 *
 * @package common\components\curl_requester\entity
 */
abstract class Method
{
    // Constants

    /** @var string список методов */
    const GET           = 'GET';
    const POST          = 'POST';
    const PUT           = 'PUT';
    const HEAD          = 'HEAD';
    const DELETE        = 'DELETE';
    const PATCH         = 'PATCH';

    const KEY_RESPONSE  = 'response';
    const KEY_HTTP_CODE = 'httpCode';

    /** @var string Установка метода запроса */
    const SELF_METHOD   = self::GET;



    // Property

    /** @var Query Данные запроса */
    protected Query $query;

    /** @var array $tests Тестовые данные */
    public array $tests = [
        self::KEY_RESPONSE  => null,
        self::KEY_HTTP_CODE => null,
    ];

    /** @var ?callback Функция вызываемая после запроса */
    protected $callBack = null;



    // Magic

    /**
     * Construct
     *
     * @param string $url куда слать запрос
     * @param ?array $data параметры/данные запроса
     */
    public function __construct( string $url, ?array $data = null )
    {
        $this->query = new Query();

        $this->query->url = $url;
        $this->query->postFields = $data ?? [];
        $this->query->method = static::SELF_METHOD;

        return $this;
    }



    // Methods

    /**
     * Получить значение `url`
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->query->url;
    }

    /**
     * Получение данных запроса
     *
     * @return array|string
     */
    public function getPostFields(): array
    {
        return $this->query->postFields;
    }

    /**
     * Задать данные запроса
     *
     * @param array|string $data
     * @return $this
     */
    public function setPostFields( $data ): self
    {
        $this->query->postFields = $data;

        return $this;
    }

    /**
     * Получить значение `метода`
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->query->method;
    }



    // Д О П  Ф У Н К Ц И О Н А Л

    /**
     * Получить данные `headers`
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->query->headers;
    }

    /**
     * Установить данные для `headers`
     *
     * @param array $headers
     * @return static
     */
    public function addHeaders( array $headers = [] ): self
    {
        $this->query->headers = array_merge( $this->query->headers, $headers );

        return $this;
    }

    /**
     * Получить данные для cURL опций
     *
     * @return array
     */
    public function getCurlOptions(): array
    {
        return $this->query->curlOptions;
    }

    /**
     * Добавление заголовков авторизации методом
     *      'Authorization: Basic ...'
     *
     * @param string $token
     * @return static
     */
    public function setBasicAuth( string $token ): self
    {
        $this->addHeaders([ 'Authorization: Basic ' . $token ]);

        return $this;
    }

    /**
     * Установить данные для cURL опций
     *
     * @param array $options
     * @return static
     */
    public function addCurlOptions( array $options = [] ): self
    {
        foreach ( $options as $option => $value ) $this->query->curlOptions[ $option ] = $value;

        return $this;
    }

    /**
     * Задать занные запроса в виде JSON
     *
     * @param string $cookie
     * @param string $path
     * @return static
     */
    public function useCookie( string $cookie, string $path ): self
    {
        $this->addCurlOptions([
            CURLOPT_COOKIE      => $cookie,
            CURLOPT_COOKIEJAR   => $path,
            CURLOPT_COOKIEFILE  => $path,
        ]);

        return $this;
    }

    /**
     * Разрешить переходить при редиректе
     *
     * @return static
     */
    public function enableRedirect(): self
    {
        $this->addCurlOptions([ CURLOPT_FOLLOWLOCATION => true ]);

        return $this;
    }

    /**
     * Добаление заголовка 'Content-Type: ... '
     *
     * @param string $type
     *
     * @return static
     */
    public function addContentType( string $type ): self
    {
        $this->addHeaders([ 'Content-Type: ' . $type ]);

        return $this;
    }

    /**
     * игнорирование сертификатов
     *
     * @return static
     */
    public function disableSSL(): self
    {
        $this->addCurlOptions([
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        return $this;
    }

    /**
     * Установить ответ для теста.
     *
     *  Запроса не будет вернётся этот ответ
     *
     * @param string $response
     * @param int $httpCode
     *
     * @return static
     */
    public function setTestResponse( string $response, int $httpCode = 200 ): self
    {
        $this->tests = [
            self::KEY_RESPONSE  => $response,
            self::KEY_HTTP_CODE => $httpCode,
        ];

        return $this;
    }

    /**
     * Установить функцию вызываемую после запроса
     *
     * @param callable $callback
     * @return static
     */
    public function setCallback( callable $callback ): self
    {
        $this->callBack = $callback;

        return $this;
    }

    /**
     * Дополняет список информации по запросу которую надо получить
     *
     * @param array $curl_info
     * @return static
     */
    public function addCurlInfo( array $curl_info ): self
    {
        $this->query->info = array_merge( $this->query->info, $curl_info );

        return $this;
    }



    // П О Л У Ч Е Н И Е   О Т В Е Т А

    /**
     * Отправка запроса
     *
     * @return Response
     */
    public function run(): Response
    {
        return ( new Request( $this ) )->run();
    }

    /**
     * Получение ответа на запрос
     *
     * @return ?string
     */
    public function response(): ?string
    {
        return $this->run()->response;
    }

    /**
     * Получение ответа на запрос в формате `Object`
     *
     * @return ?object
     */
    public function asObject(): ?object
    {
        return $this->run()->asObject();
    }

    /**
     * Получение ответа на запрос в формате `Array`
     *
     * @return ?array
     */
    public function asArray(): ?array
    {
        return $this->run()->asObject( true );
    }

    /**
     * Метод возвращает данные запроса
     *
     * @return Query
     */
    public function getQuery(): Query
    {
        return $this->query;
    }

    /**
     * Проверка являетя ли запрос тестовым
     *
     * @return bool
     */
    public function isTest(): bool
    {
        foreach ( $this->tests as $value )
        {
            if ( $value ) return true;
        }

        return false;
    }

    /**
     * Реализация callback (вызов)
     *
     * @param Query $query Query object
     * @param resource $ch Curl link
     */
    public function initCallBack( Query $query, $ch )
    {
        if ( $this->callBack )
        {
            call_user_func( $this->callBack, $query, $ch );
        }
    }

}