<?php

namespace andy87\curl_requester\entity;

/**
 *  Class `Response`
 *
 *  Класс аозвращается как ответ на запрос
 *
 * @property-read ?string $response
 * @property-read ?int $httpCode
 *
 * @package andy87\curl_requester\entity
 */
class Response
{
    // Property

    /** @var Query свойства запроса */
    private Query $query;

    /** @var array $propertyList свойства доступные через метод __get() */
    private array $propertyList = ['response', 'httpCode'];



    // Magic

    /**
     * Конструктор объекта
     *
     * @param Query $query
     */
    public function __construct( Query $query )
    {
        $this->query = $query;
    }

    /**
     * Magic
     *
     * @param $name
     * @return mixed
     */
    public function __get( $name )
    {
        return ( in_array( $name, $this->propertyList ) )
            ? $this->query->{$name}
            : null;
    }



    // Methods

    /**
     * возвращает ответ сервера как объект
     *
     * @property bool $asArray
     *  Если true, объекты JSON будут возвращены как ассоциативные массивы (array);
     *  если false, объекты JSON будут возвращены как объекты (object)
     *
     * @return mixed
     */
    public function asObject( bool $asArray = false )
    {
        return json_decode( $this->query->response, $asArray );
    }

    /**
     * возвращает ответ сервера как массив
     *
     * @property bool $asArray
     *
     * @return mixed
     */
    public function asArray()
    {
        return $this->asObject( true );
    }

    /**
     * Метод возвращает объект запроса
     *
     * @return Query
     */
    public function getQuery(): Query
    {
        return $this->query;
    }
}