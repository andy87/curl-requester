<?php

namespace andy87\curl_requester\entity;

/**
 *  Class `Response`
 *
 *  Класс аозвращается как ответ на запрос
 *
 * @property-read ?string $response Ответ сервера
 * @property-read ?int $httpCode Код ответа
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
     * @param string $name всё просто
     *
     * @return mixed
     */
    public function __get( string $name )
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