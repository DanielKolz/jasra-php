# jasra-php v 1.0
This is just another simple RESTful Api in PHP for using CRUD


Jasra is a simple REST-API written in PHP. You can use the default CRUD methods for 

__Create__  POST

__Read__    GET

__Update__  PUT

__Delete__  DELETE

your serverside data. It could be used to provide a quick access to some ressources. You can also read different body data send from the client. Additionally you can handle different path variables and also url-parameters. Use this api for your home made projects and ideas! 

## You can start very easily: 
```php
require("Jasra.php");

$api = new Jasra();

$api -> GET("/:id", function ($data, $api) {
      echo $data["path_param"][":id"];
      
      //To get URL parameter
      //$data["url_param"]["name"]
      
      //To get post parameter         
      //$data["body_param"]["name"]
      
      //To get client headers
      //$data["header_param"]
});

$api -> handleRequest();
```

Feel free to use or modify this api :) 

## Just throw any status code you want from code... 

```php
$api -> throwHttpStatusCode("404");
// like -> http_response_code(404);
```

## ... or set a specific action to run 

```php
$api -> overrideHttpStatusCodeAction("404", function () {
  //do some action on 404
});
```
