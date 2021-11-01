<?php

namespace andy87\curl_requester\entity;

/**
 *  Class `Method`
 *
 *
 * @property ?string $logger ORM/ActiveRecord логгер запросов
 * @property bool $logger_status Статус активности логгера по умолчанию
 * @property Query $query Данные запроса
 * @property ?string $group дополнительные данные для лога
 * @property ?string $comment дополнительные данные для лога
 * @property $callBack Функция вызываемая после запроса
 * @property ?string $testResponse Тестовый ответ
 * @property ?int $testHttpCode Тестовый код ответа
 *
 * @package andy87\curl_requester\entity
 */
abstract class Method
{
    /** @var string список методов */
    const GET    = 'GET';
    const POST   = 'POST';
    const PUT    = 'PUT';
    const HEAD   = 'HEAD';
    const DELETE = 'DELETE';
    const PATCH  = 'PATCH';

    /** @var string Установка метода запроса */
    const SELF_METHOD = self::GET;



    /** @var ?string ORM/ActiveRecord логгер запросов */
    protected ?string $logger;

    /** @var bool Статус активности логгера по умолчанию */
    protected bool $logger_status;


    /** @var Query Данные запроса */
    protected Query $query;

    /** @var string|null дополнительные данные для лога */
    protected ?string $group = null;

    /** @var string|null дополнительные данные для лога */
    protected ?string $comment = null;

    /** @var ?callback Функция вызываемая после запроса */
    protected $callBack = null;



    // Т Е С Т О В О Е
    /** @var string|null Тестовый ответ */
    public ?string $testResponse = null;

    /** @var int|null Тестовый код ответа */
    public ?int $testHttpCode = null;


    /**
     * Construct
     *
     * @param string $url
     * @param ?array $data
     * @param ?string $logger ORM/ActiveRecord ЛОггер запросов
     * @param ?bool $logger_status
     */
    public function __construct(string $url, ?array $data = null, ?string $logger = null, ?bool $logger_status = null )
    {
        $this->query = new Query();

        $this->query->url = $url;
        $this->query->postFields = $data ?? [];
        $this->query->method = static::SELF_METHOD;

        $this->logger = $logger;
        $this->logger_status = $logger_status;

        return $this;
    }



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
     * Получить значение доп. данных запроса
     *      используемого при логировании
     *
     * @return ?string
     */
    public function getGroup(): ?string
    {
        return $this->group;
    }

    /**
     *  Получить значение доп. данных запроса
     *      используемого при логировании
     *
     * @return ?string
     */
    public function getComment(): ?string
    {
        return $this->comment;
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
     * @return $this
     */
    public function addHeaders(array $headers = [] ): self
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
     * @return $this
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
     * @return $this
     */
    public function addCurlOptions(array $options = [] ): self
    {
        foreach ( $options as $option => $value )
        {
            $this->query->curlOptions[ $option ] = $value;
        }

        return $this;
    }

    /**
     * Задать занные запроса в виде JSON
     *
     * @param string $cookie
     * @param string $path
     * @return $this
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
     * @return $this
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
     * @return $this
     */
    public function addContentType( string  $type ): self
    {
        $this->addHeaders([ 'Content-Type: ' . $type ]);

        return $this;
    }

    /**
     * игнорирование сертификатов
     *
     * @return $this
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
     * @param int $code
     *
     * @return $this
     */
    public function setTestResponse( string $response, int $code = 200 ): self
    {
        $this->testResponse = $response;
        $this->testHttpCode = $code;

        return $this;
    }

    /**
     * Установить функцию вызываемую после запроса
     *
     * @param callable $callback
     * @return $this
     */
    public function setCallback( callable $callback ): self
    {
        $this->callBack = $callback;

        return $this;
    }



    // П О Л У Ч Е Н И Е   О Т В Е Т А

    /**
     * Отправка запроса
     *
     * @param ?bool $use_logger TRUE = писать логи / FALSE = не писать логи
     * @return Response
     */
    public function run( ?bool $use_logger = null ): Response
    {
        return ( new Request( $this, $this->logger ) )->run( $use_logger ?? $this->logger_status );
    }

    /**
     * Получение ответа на запрос
     *
     * @param ?bool $use_logger TRUE = писать логи / FALSE = не писать логи
     * @return ?string
     */
    public function response( ?bool $use_logger = null ): ?string
    {
        return $this->run( $use_logger ?? $this->logger_status )->response;
    }

    /**
     * Получение ответа на запрос в формате `Object`
     *
     * @param ?bool $use_logger TRUE = писать логи / FALSE = не писать логи
     * @return ?object
     */
    public function asObject( ?bool $use_logger = null ): ?object
    {
        return $this->run( $use_logger ?? $this->logger_status )->asObject();
    }

    /**
     * Получение ответа на запрос в формате `Array`
     *
     * @param ?bool $use_logger TRUE = писать логи / FALSE = не писать логи
     * @return ?array
     */
    public function asArray( ?bool $use_logger = null ): ?array
    {
        return $this->run( $use_logger ?? $this->logger_status )->asObject( true );
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
        return ( !empty($this->testResponse) || !empty($this->testHttpCode) );
    }

    /**
     * Реализация callback (вызов)
     *
     * @param Query $query
     */
    public function initCallBack( Query $query )
    {
        if ( $this->callBack ) call_user_func( $this->callBack, $query );
    }
}