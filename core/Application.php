<?php

namespace app\core;

class Application{

        public static string $ROOT_DIR;
        public Router $router;
        public Request $request;
        public Response $response;
        public static Application $app;
        public Controller $controller;
        public Database $db;
        public Session $session;
        public function getController(){
            return $this->controller;
        }
        public function setController(Controller $controller){
            $this->controller = $controller;
        }

        public function __construct($rootpath, array $config){
            self::$ROOT_DIR = $rootpath;
            self::$app = $this; 
            $this->request = new Request();
            $this->response = new Response();
            $this->session = new Session();
            $this->router = new Router($this->request, $this->response);

            $this->db = new Database($config['db']);

        }

        public function run(){
            $this->router->resolve();
        }
    }