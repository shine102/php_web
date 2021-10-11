<?php

namespace app\core;
require_once __DIR__.'/../controllers/SiteController.php';

class Router{
        protected array $routes = [];
        public Request $request;
        public Response $response;

        public function __construct(Request $request, Response $response)
        {
            $this->response = $response;
            $this->request = $request;
        }

        public function get($path, $callback){
            $this->routes['get'][$path] = $callback;
        }

        public function post($path, $callback){
            $this->routes['post'][$path] = $callback;
        }

        public function resolve(){
            $path = $this->request->getPath();
            $method = $this->request->getMethod();
            $callback = $this->routes[$method][$path] ?? false;
            if ($callback === false){
                $this->response->statusCode(404);
                return $this->renderView("_404");
            }
            if (is_string($callback)){
                return $this->renderView($callback);
            }
            if (is_array($callback)){
                Application::$app->controller = new $callback[0]();
                $callback[0] = Application::$app->controller;
            }

            return call_user_func($callback, $this->request);
        }

        public function renderView($view, $params=[])
        {
            $layoutContent = $this->layoutContent();
            $viewContent = $this->renderOnlyView($view, $params);
            print_r(str_replace('{{content}}', $viewContent, $layoutContent));
            
        }

        protected function layoutContent(){
            ob_start();
            $layout = Application::$app->controller->layout;
            include_once Application::$ROOT_DIR."/views/layouts/$layout.php";
            return ob_get_clean();
        }

        protected function renderOnlyView($view, $params){
            foreach ($params as $key=>$value){
                $$key = $value;
            }
            ob_start();
            include_once Application::$ROOT_DIR."/views/$view.php";
            return ob_get_clean();
        }
    }