<?php
class Jasra {

    private $delimiter = '/';
    private $httpStatusCodeActions = array();
    private $registeredEndPoints = array();

    private $request = "";
    private $method = "";


    //###################################################################################
    //###################################################################################

    public function __construct() {
    }

    public function __clone() {
    }

    //###################################################################################
    //###################################################################################

    public function GET($path,$callable) {
        $newEndpoint = array("GET", $path, $callable);
        $this -> registeredEndPoints[] = $newEndpoint;
    }

    public function POST($path,$callable) {
        $newEndpoint = array("POST", $path, $callable);
        $this -> registeredEndPoints[] = $newEndpoint;
    }

    public function PUT($path,$callable) {
        $newEndpoint = array("PUT", $path, $callable);
        $this -> registeredEndPoints[] = $newEndpoint;
    }

    public function DELETE($path,$callable) {
        $newEndpoint = array("DELETE", $path, $callable);
        $this -> registeredEndPoints[] = $newEndpoint;
    }


    public function handleRequest() {
        $this-> request = $this -> filterRequest($_SERVER['REQUEST_URI']);

        $_SERVER['REQUEST_URI'] = ""; 
        $this -> method = $_SERVER['REQUEST_METHOD'];

        switch($this -> method)
        { 
            case "GET": $this->handle_();
                break;
            case "POST": $this->handle_();
                break;
            case "PUT": $this->handle_();
                break;
            case "DELETE": $this->handle_();
                break;
            default: $this -> throwHttpStatusCode("405", null);
        }

    } 

    public function overrideHttpStatusCodeAction($statusCode, $callable) {
        $this -> httpStatusCodeActions[$statusCode] = $callable;
    }

    public function throwHttpStatusCode($statusCode, $object) {
        if($this -> httpStatusCodeActions[$statusCode] !== null) {
            $this -> httpStatusCodeActions[$statusCode]($object);   
        } else {
            http_response_code($statusCode);
        }
    }

    //##################################################################
    //##################################################################

    private function handle_() {

        $endpoint = $this -> getMatchingEndpoint();
        
        if($endpoint !== null) {
            if($endpoint[0] !== $this -> method) { 
                $this -> throwHttpStatusCode("405", null);
                exit(); 
            } else {

                $data = array();
                $data["url_param"] = $this -> getUrlParameter();
                $data["path_param"] = $this -> getPathParameter($endpoint[1]);
                $data["body_param"] = $this -> getBodyParameter();
                $data["header_param"] = $this -> getHeaderInformation();

                $endpoint[2]($data, $this); 
            } 

        }
        else {
            $this -> throwHttpStatusCode("404", null);
            exit();
        }

    } 


    //##################################################################
    //##################################################################

    private function getMatchingEndpoint() {
        $endpoints = $this -> registeredEndPoints;

        $request = $this -> request;
        $endpoint = null;

        $requestUri = explode("?", $request)[0];
        $requestParts = explode($this -> delimiter, $requestUri);

        foreach($endpoints as $e) {

            $deviation = false;
            $e_path = explode($this->delimiter, $e[1]);

            if(count($requestParts) === count($e_path)) {
                for($i=0; $i<count($e_path) ; $i++) {
                    if(strpos($e_path[$i], ":") === false) 
                    { 
                        if($e_path[$i] !== $requestParts[$i]) {
                            $deviation = true;
                        }
                    }
                }
            } else {
                $deviation = true;
            }

            if(!$deviation) {
                $endpoint = $e;
                break;
            }
        }
        return $endpoint;
    }


    private function getUrlParameter() {
        $request = $this -> request;
        $vars = array();

        $parameter = explode("?", $request);
        if(count($parameter === 2)) {
            $splitted = explode("&", $parameter[1]);
            if(count($splitted) < 2) {
                $pair = explode("=", $parameter[1]);
                $vars[$pair[0]] = $pair[1];
            } else {
                foreach($splitted as $keyval) {
                    $pair = explode("=", $keyval);
                    $vars[$pair[0]] = $pair[1];
                }
            }

        }
        return $vars;
    }

    private function getPathParameter($path) {
        $request = $this -> request;
        $vars = array();
        $splitted_request = explode($this->delimiter, $request);
        $splitted_path = explode($this->delimiter, $path);

        for($i=0; $i<count($splitted_path) ; $i++) {
            if(strpos($splitted_path[$i], ":") !== false) 
            {   
                $path = $splitted_path[$i];
                $var = $splitted_request[$i];

                if (strpos($splitted_request[$i], "?")) {
                    $var = explode("?", $var)[0];
                }

                $vars[$path] = $var;
            }
        }

        return $vars;
    }

    private function getBodyParameter() {
        $vars = array();

        while($postData === current($_POST)) {
            $vars[key($_POST)] = $postData; 
            next($postData);
        }

        $data = null;
        $data = file_get_contents("php://input");
        if($data !== null && $data !== "") {
            $vars["bodyData"] = $data; 
        }

        return $vars;
    }



    private function getHeaderInformation() {
        $vars = array();

        foreach (getallheaders() as $name => $value) {
            $vars[$name] = $value;
        }

        return $vars;
    }

    private function filterRequest($request) {
        $filter = RequestFilter::getInstance();
        return $filter -> filterRequest($request);
    }
}


class RequestFilter {
    private static $_instance = null;

    private function __construct() {}

    private function __clone() {}

    private $filterExpression = "/[^.a-zA-Z0-9=&-_\/]/";


    public static function getInstance()
    {
        if (null === self::$_instance)
        {
            self::$_instance = new self;
        }
        return self::$_instance;
    }


    public function filterRequest($request){

        $filtered= preg_replace($this -> filterExpression, "", $request);

        return $filtered;
    }
}
?>
