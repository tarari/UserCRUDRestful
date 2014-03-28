<?php
/**
 * CRUD framework front controller.
 * 
 * @package CRUD-Restful    
 * @author  Toni Jimenez <t.jimenez@escolesnuria.cat>
 */

/**
 * Generic class autoloader.
 * 
 * @param string $class_name
 */
function autoload_class($class_name) {
    $directories = array(
        'classes/',
        'classes/controllers/',
        'classes/models/'
    );
    foreach ($directories as $directory) {
        $filename = $directory . $class_name . '.php';
        if (is_file($filename)) {
            require($filename);
            break;
        }
    }
}

/**
 * Register autoloader functions.
 */
spl_autoload_register('autoload_class');

/**
 * Parse the incoming request.
 */
$request = new Request();
if (isset($_SERVER['PATH_INFO'])) {
    $request->url_elements = explode('/', trim($_SERVER['PATH_INFO'], '/'));
}
$request->method = strtoupper($_SERVER['REQUEST_METHOD']);

switch ($request->method) {
    case 'GET':
       $request->parameters = $_GET;
        break;
    case 'POST':
        parse_str(file_get_contents('php://input'), $request->parameters);
        break;
    case 'PUT':
    case 'DELETE':
        parse_str(file_get_contents('php://input'), $request->parameters);
        break;
}

/**
 * Route the request.
 */
if (!empty($request->url_elements)) {
    $controller_name = ucfirst(strtolower(array_shift($request->url_elements))) . 'Controller';
    if (class_exists($controller_name)) {

        $controller = new $controller_name;
        //var_dump($controller);
        $action_name = array_shift($request->url_elements);
        $action_name=($action_name) ? $action_name :'index';
        if (count($request->url_elements)>0){
                    $response_str = call_user_func_array(array($controller, $action_name), $request);
        }else{
         
            $response_str=call_user_func(array($controller, $action_name),$request);
        }
    }
    else {
        header('HTTP/1.1 404 Not Found');
        $response_str = 'Unknown request: ' . $request->url_elements[0];
    }
}
else {
    $response_str = 'Unknown request';
}

/**
 * Send the response to the client.
 */
$response_obj = Response::create($response_str, $_SERVER['HTTP_ACCEPT']);
ob_clean();
echo $response_obj->render();