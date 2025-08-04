<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Auth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $router = service('router');
        $controller = basename($router->controllerName());

        if (!authCheck()) {
            if (strpos($controller, 'HomeController') !== false || strpos($controller, 'ProfileController') !== false || strpos($controller, 'EarningsController') !== false) {
                redirectToUrl(langBaseUrl());
            } else {
                redirectToUrl(adminUrl('login'));
            }
            exit();
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}