<?php 

require("Jasra.php");

$api = new Jasra();


$api -> GET("/persons", function ($data, $api) {
       //do something
});

$api -> GET("/persons/:id", function ($data, $api) {
       //do something
});

$api -> POST("/persons/:id", function ($data, $api) {
       //do something
});

$api -> DELETE("/persons/:id", function ($data, $api) {
       //do something
});

$api -> PUT("/persons/:id/name", function ($data, $api) {
       //do something
});


$api -> handleRequest();

?>
