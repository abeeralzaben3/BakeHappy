<?php
abstract class Controller
{
    // Load a view file and give it variables: $this->view('user/shop/index', ['products' => $p]);
    protected function view(string $path, array $data = []): void
    {
        $file = __DIR__ . '/../views/' . $path . '.php';
        if (!file_exists($file)) {
            echo "View not found: $path";
            return;
        }
        extract($data);
        require $file;
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . BASE_URL . '/' . ltrim($url, '/'));
        exit;
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function notFound(): void
    {
        http_response_code(404);
        $this->view('user/errors/404');
        exit;
    }
}
