<p align="center">
    <img src="https://raw.githubusercontent.com/andy87/curl-requester/main/logo.png">
    <h1 align="center">curl-requester</h1>
</p>

Многофункциональный класс для отправки запросов с помощью функций cURL.

***Решаемые задачи/цели:*** 
<br> 1. Единая точка входа для отправки запросов
<br> 2. Легко использовать (простой интерфейс и настройка)
<hr>

## Пример кода использования.
```php
/** @var andy87\curl_requester\Curl $curl */

//GET зпрос
$resp = $curl->get( 'vk.com/id806034' )->response(); // string

// Получение ответа в качестве объекта с запросом методом POST
$respObject = $curl->post( 'vk.com/user/add', [ 'name' => 'and_y87' ])->run()->asObject(); // object

// Имитация запроса методом PATCH с получением тестовых данных
$response = $curl->patch( 'vk.com/user/get', ['id' => 806034])
    ->setTestResponse('{"name" : "Андрей", "do" : "code"}')
    ->run();

//Получение данных
$respArray  = $response->asArray(); // ['name' => 'Андрей', 'do'=> 'code']
$http_code  = $response->httpCode;
```

<hr>

###Mapping
Переменные в примерах кода:
 - ***$response*** - объект класа `andy87\curl_requester\entity\Response`
 - ***$request*** - объект класа `andy87\curl_requester\entity\Request`
 - ***$query*** - объект класа `andy87\curl_requester\entity\Query`

###Детальнее
Доступно 6 методов/запросов: GET, POST, PUT, PATCH, HEAD, DELETE  
Все методы вызываются идентично.  
1. **конструктор** - принимает аргументы:
- *string* **url** - адрес на который будет осуществлён запрос
- *array* **params** - параметры запроса *(не обязательный)*
 
```php
/** @var andy87\curl_requester\Curl $curl */
/** @var andy87\curl_requester\entity\Response $curl */

$request = $curl->get('https://andy87.ru');

$request = $curl->post('www.andy87.ru/search', [ 'text' => 'php' ]);
```

2. Ответ(***response***).  
Возможно получить ответ несколькими способами:
- Преобразует ответ в объект( object )
```php
/** @var andy87\curl_requester\Curl $curl */

$respObject = $curl->get('www.andy87.ru/data')->asObject(); // object
```
- Преобразует ответ в массив( array )
```php
/** @var andy87\curl_requester\Curl $curl */

$respArray = $curl->get('www.andy87.ru/data')->asArray(); // array
```
- Текст ( string )
```php
/** @var andy87\curl_requester\Curl $curl */
/** @var andy87\curl_requester\entity\Response $response */

$resp = $curl->get('www.andy87.ru')->response(); // string

//Аналог

$response = $curl->get('www.andy87.ru')->run(); // Вернёт объект класса `Response` (информацию о ответе)
$resp     = $response->response;

//Аналог(краткая запись)
$resp = $curl->get('www.andy87.ru')->run()->response;
$resp = $curl->get('www.andy87.ru')->response();
```

### Информация об ответе.  
`Response::class`  
- ***response*** - ответ на запрос
- ***httpCode*** - код ответа на запрос
```php
/** @var andy87\curl_requester\Curl $curl */

$response  = $curl->post( 'www.andy87.ru')->run(); //Вернёт объект класса `Request` (данные запроса).

$respArray = $response->asArray(); // ['name' => 'Андрей', 'do'=> 'code']
$httpCode  = $response->httpCode; //Код ответа сервера

$query = $response->getQuery(); //Вернёт объект класса `Query` (информацию о запросе.)
```

### Информация о запросе.  
`Query::class`  
 - **method** - метод запроса
 - **url** - адрес запроса
 - **postFields** - данные запроса
 - **headers** - Заголовки запроса
 - **curlOptions** - опции cURL запроса
 - **response** - ответ на запрос
 - **httpCode** - код ответа на запрос
```php
/** @var andy87\curl_requester\Curl $curl */
/** @var andy87\curl_requester\entity\Query $query */

$query = $curl->post( 'www.andy87.ru')->run()->getQuery();

$method     = $query->method;
$url        = $query->url;
$headers    = $query->headers;
$postFields = $query->postFields;
$curlOptions= $query->curlOptions;
$response   = $query->response;
$http_code  = $query->httpCode;

$isPost     = $query->isPost();
//... и т.д. ( isGet(), isPut() ... )
```

<hr>

## Дополнительные возможности
### Вернуть тестовые данные
- **setTestResponse( *string* $response, *int* $http_code )** - запрос не выполнится и вернётся заданный ответ.  
  - *string* **$response** - имитируемый ответ
  - *int* **$http_code** - имитируемый код ответа ( По умолчанию 200 )
```php
/** @var andy87\curl_requester\Curl $curl */

$resp = $curl->post('www.crm.ru/get-user', ['id' => 123])
    ->setTestResponse(json_encode(['name'=>'Андрей', 'do'=>'code']), 302 )
    ->response();
```
### Расширить/дополнить заголовки запроса
- **addHeaders( *array* $array )** - добавляемые заголовки
```php
/** @var andy87\curl_requester\Curl $curl */

$resp = $curl->post('www.crm.ru/get-user', ['id' => 123])
    ->addHeaders(['Content-Type: application/json'])
    ->response();
```
### Подготовленные данные
- **prepareParams( *string* $postField )** - данные для запроса не будут проходить обработку [http_build_query()](https://www.php.net/manual/ru/function.http-build-query.php) они считаются уже подготовленными для запроса
```php
/** @var andy87\curl_requester\Curl $curl */

$resp = $curl->post( 'www.vk.com/806034')
    ->prepareParams( http_build_query(['id' => 123]) )
    ->response();
    
// Аналог
$params = http_build_query(['id' => 123]);
$resp   = $curl->post( 'www.vk.com/806034', $params )->prepareParams()->response();
```
### Использование Basic авторизации
- **setBasicAuth( *string* $token )** - Добавляет в заголовки  
`Authorization: Basic eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9`
```php
/** @var andy87\curl_requester\Curl $curl */

$resp = $curl->post('www.crm.ru/get-user', ['id' => 123])
    ->setBasicAuth('eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9')
    ->response();
```
### Дополнительные cURL опции
- **addCurlOptions( *array* $array )** - дополнительные опции cURL для [curl_setopt()](https://www.php.net/manual/ru/function.curl-setopt.php)
```php
/** @var andy87\curl_requester\Curl $curl */

$resp = $curl->post('www.crm.ru/get-user/delete', ['id' => 123])
    ->addCurlOptions([ CURLOPT_FOLLOWLOCATION => true])
    ->response();
```
### Использование Cookie
- **useCookie( *string* $cookie, *string* $path )** - использование cookie. Задаются:  
`CURLOPT_COOKIE`  
`CURLOPT_COOKIEJAR`  
`CURLOPT_COOKIEFILE`  
```php
/** @var andy87\curl_requester\Curl $curl */

$resp = $curl->post('www.crm.ru/get-user', ['id' => 123])
    ->useCookie('cookiename=cookievalue', '/tmp/cookies.txt')
    ->response();
```

### Отключение проверки SSL
- **disableSSL()** - отключение проверки SSL. Задаются:  
`CURLOPT_SSL_VERIFYPEER => false`  
`CURLOPT_SSL_VERIFYHOST => false`  
```php
/** @var andy87\curl_requester\Curl $curl */

$resp = $curl->post( 'www.crm.ru/get-user', ['id' => 123])
    ->disableSSL()
    ->response();
```
### Разрешение редиректа
- **enableRedirect()** - разрешение на редирект, если ответ сервера требует редиректа. Задаётся:  
`CURLOPT_FOLLOWLOCATION => true`
```php
/** @var andy87\curl_requester\Curl $curl */

$resp = $curl->post( 'www.vk.com/806034')
    ->enableRedirect()
    ->response();
```

### Получение расширенной информации по запросу. 
- **addCurlInfo( *array* $curl_info )** - Дополняет список информации по запросу которую надо получить
```php
/** @var andy87\curl_requester\Curl $curl */

$query = $curl->post('www.vk.com/806034')
    ->addCurlInfo([CURLINFO_EFFECTIVE_URL]) // Добавление необходимой информации к ответу
    ->run()
    ->getQuery();

$last_url = $query->info[ CURLINFO_EFFECTIVE_URL ]; //Получение информации 
```
### Установка callBack функции
- **setCallback( *callable* $callback )** - callback функция которая будет вызвана сразу после формирования ответа от сервера и до закрытия [curlHandler](https://www.php.net/manual/ru/book.curl.php)
```php
use andy87\curl_requester\entity\Query

/** @var andy87\curl_requester\Curl $curl */

$request = $curl->post('www.vk.com/806034');

$request->setCallback(function ( Query $query, $curlHandler )
{
    if ( $query->httpCode !== Query::OK )
    {
        $errors = curl_error( $curlHandler );
        
        curl_close( $curlHandler );
        
        exit( $errors );
    }
});

$resp = $request->run()->response;
    ...
```

<hr>

### Собственная реализация [cURL](https://www.php.net/manual/ru/book.curl.php) через Request 
```php
use andy87\curl_requester\entity\Request

$ch = Request::createCurlHandler( 'www.vk.com/806034', [
    CURLOPT_RETURNTRANSFER  => true,
    CURLOPT_POST            => 1,
    CURLOPT_HTTPHEADER      => [ 'some headers' ],
    CURLOPT_POSTFIELDS      => [ 'some params' ]
]);

$resp = curl_exec( $ch );
    
curl_close($ch);

// Аналог(кратная запись)
$resp = Request::createCurlHandler( 'www.vk.com/806034', [
    CURLOPT_RETURNTRANSFER  => true,
    CURLOPT_POST            => 1,
    CURLOPT_HTTPHEADER      => [ 'some headers' ],
    CURLOPT_POSTFIELDS      => [ 'some params' ]
], ( $is_return_response = true ) );
```

<hr>

# Установка

## Зависимости
- php ( >= 7.4 )
- ext-curl
- ext-json

## composer.json
Установка с помощью [composer](https://getcomposer.org/download/)  

Добавить в `composer.json`  
<small>require</small>
```
"require": {
    ...
    "andy87/curl-requester" : "1.0.4"
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
            "version"               : "1.0.4",
            "source"                : {
                "type"                  : "git",
                "reference"             : "main",
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
