Многофункциональный класс для отправки запросов с помощью функций cURL.

***Решаемые задачи:*** 
<br> 1. Единая точка входа для отправки запросов
<br> 2. Простой интерфейс и настройка
<hr>

## Код использования.
```php
/** @var andy87\curl_requester\Curl $curl */

//GET зпрос
$response = $curl->get( 'vk.com/id806034' )
    ->response(); // string

// Получение ответа в качестве объекта с запросом методом POST
$object = $curl->post( 'vk.com/user/add', [ 'name' => 'and_y87' ])
    ->run()
    ->asObject(); // object


// Имитация запроса методом PATCH с получением тестовых данных
$resp = $curl->patch( 'vk.com/user/get', ['id' => 806034])
    ->setTestResponse('{"name" : "Андрей", "do" : "code"}')
    ->run();

//Получение данных
$response   = $resp->asArray(); // ['name' => 'Андрей', 'do'=> 'code']
$http_code  = $resp->http_code;
```



# Использование.

Доступно 6 методов/запросов: GET, POST, PUT, PATCH, HEAD, DELETE  
Все методы вызываются идентично.  
1. **конструктор** - принимает 4 аргумента:
- *string* **url** - адрес на который будет осуществлён запрос
- *array* **params** - параметры запроса *(не обязательный)*
- *class* **logger** - ORM/ActiveRecord логгер запросов *(не обязательный)*
- *bool* **logger_status** - Статус активности логгера по умолчанию *(не обязательный)*
 
```php
/** @var andy87\curl_requester\Requester $curl */
// Конструктор: простой запрос
$requester->get('https://andy87.ru');

// Конструктор: запрос с данными
$requester->post('www.andy87.ru/search', [ 'text' => 'php' ])
```

2. Ответ(***response***).  
Возможно получить ответ тремя типами:
- Текст ( string )
```php
/** @var andy87\curl_requester\Requester $curl */

$resp = $requester->get('www.andy87.ru')->response(); // string
```
- Объект ( object )
```php
/** @var andy87\curl_requester\Requester $curl */

$object = $requester->get('www.andy87.ru/data')->asObject(); // object
```
- Массив ( array )
```php
/** @var andy87\curl_requester\Requester $curl */

$array = $requester->get('www.andy87.ru/data')->asArray(); // array
```

### Информация об ответе.
`Response::class`
- ***response*** - оригинальный ответ на запрос
- ***http_code*** - код ответа
```php
/** @var andy87\curl_requester\Requester $curl */

$query = $requester->post( 'www.andy87.ru')->run(); //Вернёт `Response` информацию об ответе.

$response   = $query->asArray(); // ['name' => 'Андрей', 'do'=> 'code']
$http_code  = $query->http_code; //Код ответа сервера
$query  = $query->getQuery(); //Вернёт `Query` информацию о запросе.
```

### Информация о запросе.
`Query::class`
 - **method** - метод запроса
 - **url** - адрес запроса
 - **postFields** - данные запроса
 - **headers** - Заголовки
 - **curlOptions** - опции cURL 
 - **response** - ответ сервера
 - **http_code** - код ответа сервера
```php
/** @var andy87\curl_requester\Requester $curl */

$query = $requester->post( 'www.andy87.ru')->run()->getQuery();

$method     = $query->method;
$url        = $query->url;
$headers    = $query->headers;
$postFields = $query->postFields;
$curlOptions= $query->curlOptions;
$response   = $query->response;
$http_code  = $query->http_code;

$isPost     = $query->isPost();
//... и т.д.
```


## Дополнительные возможности
### Тестовые данные
- **setTestResponse( *string* $response, *int* $http_code )** - запрос не будет выполнен, вернётся ваш ответ.  
  - *string* $response - имитируемый ответ
  - *int* $http_code - имитируемый код ответа ( По умолчанию 200 )
```php
/** @var andy87\curl_requester\Requester $curl */

$request = $curl->post('www.crm.ru/get-user', ['id' => 123])
    ->setTestResponse(json_encode(['name'=>'Андрей', 'do'=>'code']), 302 )
    ...
```
### Заголовки запроса
- **addHeaders( *array* $array )** - добавляются заголовки
```php
/** @var andy87\curl_requester\Requester $curl */

$request = $curl->post('www.crm.ru/get-user', ['id' => 123])
    ->addHeaders(['Content-Type: application/json'])
    ...
```
### Дополнительные cURL опции
- **addCurlOptions( *array* $array )** - дополнительные опции cURL
```php
/** @var andy87\curl_requester\Requester $curl */
// addCurlOptions
$request = $curl->post('www.crm.ru/get-user/delete', ['id' => 123])
    ->addCurlOptions([ CURLOPT_FOLLOWLOCATION => true])
    ...
```
### Использование Cookie
- **useCookie( *string* $cookie, *string* $path )** - использование cookie
```php
/** @var andy87\curl_requester\Requester $curl */
// useCookie
$request = $curl->post('www.crm.ru/get-user', ['id' => 123])
    ->useCookie('cookiename=cookievalue', '/tmp/cookies.txt')
    ... 
```
### Использование Basic авторизации
- **setBasicAuth( *string* $token )** - Создание заголовка вторизации
```php
/** @var andy87\curl_requester\Requester $curl */
// setBasicAuth
$request = $curl->post('www.crm.ru/get-user', ['id' => 123])
    ->setBasicAuth('token')
    ...
```
### Отключение проверки SSL
- **disableSSL()** - отключение проверки SSL
```php
/** @var andy87\curl_requester\Requester $curl */
// disableSSL
$request = $curl->post( 'www.crm.ru/get-user', ['id' => 123])
    ->disableSSL()
    ...  
```
### Разрешение редиректа
- **enableRedirect()** - разрешение на редирект, если ответ сервера требует редиректа   
```php
/** @var andy87\curl_requester\Requester $curl */
// enableRedirect
$request = $curl->post( 'www.vk.com/806034')
    ->enableRedirect()
    ...
```
### Подготовленные данные
- **prepareParams( *string* $postField )** данные для запроса не будут проходить обработку `http_build_query()` они будут считаться уже подготовленными для запроса
```php
/** @var andy87\curl_requester\Requester $curl */
// enableRedirect
$request = $curl->post( 'www.vk.com/806034')
    ->prepareParams( http_build_query(['id' => 123]) )
    ...
```
### callBack
- **setCallback( *callable* $callback )** callback функция которая будет вызвана сразу после формирования ответа от сервера
```php
/** @var andy87\curl_requester\Requester $curl */
// enableRedirect
$request = $curl->post('www.vk.com/806034')
    ->setCallback( function ( Query $query ){
        echo PHP_EOL . "Response: " . $query->response 
            . "\nCode: " . $query->http_code;
    })
    ->run();
```


## Логирование запросов.
Логирование происходит через ORM/ActiveRecord класс, из константы `LOGGER` при заданной константе `LOGGER` в классе расширяющего `Curl`
```php
/**
 * Класс у которого логирование по умолчанию включено
 */
class CurlRequestWithLogger extends andy87\curl_requester\Curl {
    const LOGGER = Log::class; // класс логера
    const DEFAULT_LOGGER_STATUS = true; // статус логирования
} 

/** @var CurlRequestWithLogger $R */
$resp = $R->post( 'www.vk.com/806034')->run(false); // Не логировать запрос

$resp = $R->post( 'www.vk.com/806034')->response();
$resp = $R->post( 'www.vk.com/806034')->response(false);  // Не логировать запрос

$resp = $R->post( 'www.vk.com/806034')->asObject();
$resp = $R->post( 'www.vk.com/806034')->asObject(false);  // Не логировать запрос

$resp = $R->post( 'www.vk.com/806034')->asArray();
$resp = $R->post( 'www.vk.com/806034')->asArray(false); // Не логировать запрос
```

```php
/**
 * Класс у которого логирование по умолчанию выключено
 */
class CurlRequestWithLogger extends andy87\curl_requester\Requester {
    const LOGGER = Log::class; // класс логера
    const DEFAULT_LOGGER_STATUS = false; // статус логирования
} 

/** @var CurlRequestWithLogger $R */
$resp = $R->post( 'www.vk.com/806034')->run(true); // Логировать запрос

$resp = $R->post( 'www.vk.com/806034')->response(true); // Логировать запрос
$resp = $R->post( 'www.vk.com/806034')->response();

$resp = $R->post( 'www.vk.com/806034')->asObject(true); // Логировать запрос
$resp = $R->post( 'www.vk.com/806034')->asObject();

$resp = $R->post( 'www.vk.com/806034')->asArray(true); // Логировать запрос
$resp = $R->post( 'www.vk.com/806034')->asArray();
```


# Установка
Добавить в `composer.json`  
<small>require</small>
```
"require": {
    ...
    "andy87/curl-requester" : "1.0.0"
},
```
<small>repositories</small>
```
"repositories": [
    ...,
    {
        "type"                  : "package",
        "package"               : {
            "name"                  : "andy87/curl-requester",
            "version"               : "1.0.0",
            "source"                : {
                "type"                  : "git",
                "reference"             : "master",
                "url"                   : "https://github.com/andy87/curl-requester"
            },
            "autoload": {
                "psr-4": {
                    "andy87\\curl_requester\\" : "src",
                    "andy87\\curl_requester\\entity\\" : "src/entity",
                    "andy87\\curl_requester\\entity\\methods\\": "src/entity/methods"
                }
            }
        }
    }
]
```
