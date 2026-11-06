<?php
class App
{
    private string $controller = 'HomeController';
    private string $method = 'index';

    public function __construct()
    {
        $url = $this->parseUrl();
        $folder = '';
        $prefix = '';

        if (($url[0] ?? '') === 'admin') {
            array_shift($url);
            $folder = 'admin/';
            $prefix = 'Admin';
            $this->controller = 'AdminDashboardController';
        }

        if (!empty($url[0])) {
            $this->checkName($url[0]);
            $this->controller = $prefix . ucfirst($url[0]) . 'Controller';
            array_shift($url);
        }

        $file = __DIR__ . '/../controllers/' . $folder . $this->controller . '.php';
        if (!file_exists($file)) {
            $this->notFound();
        }
        require_once $file;
        $object = new $this->controller();

        if (!empty($url[0])) {
            $this->checkName($url[0]);
            $this->method = $url[0];
            array_shift($url);
        }
        if (!is_callable([$object, $this->method])) {
            $this->notFound();
        }

        call_user_func_array([$object, $this->method], $url);
    }

    private function parseUrl(): array
    {
        $url = trim($_GET['url'] ?? '', '/');
        return $url === '' ? [] : explode('/', $url);
    }

    private function checkName(string $name): void
    {
        if (!preg_match('/^[a-zA-Z]+$/', $name)) {
            $this->notFound();
        }
    }

    private function notFound(): void
    {
        http_response_code(404);
        echo '404 Not Found';
        exit;
    }
}