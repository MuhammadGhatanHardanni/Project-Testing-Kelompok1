<?php
// core/Controller.php

abstract class Controller
{
    /**
     * Render a view file with data.
     *
     * @param string $view   Dot-notation path relative to VIEW_PATH  e.g. 'product.index'
     * @param array  $data   Variables to extract into view scope
     * @param string $layout Layout file name (without .php)
     */
    protected function view(string $view, array $data = [], string $layout = 'main'): void
    {
        // Convert dot notation to path
        $viewFile = VIEW_PATH . '/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewFile)) {
            throw new RuntimeException("View not found: {$viewFile}");
        }

        // Make data available in view scope
        extract($data);

        // Capture view content
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Render inside layout
        $layoutFile = VIEW_PATH . '/shared/' . $layout . '.php';
        if ($layout && file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    /**
     * Redirect to a URL.
     */
    protected function redirect(string $url): void
    {
        // Prepend APP_URL if relative path
        if (strpos($url, 'http') !== 0) {
            $url = APP_URL . $url;
        }
        header("Location: {$url}");
        exit;
    }

    /**
     * Return JSON response.
     */
    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Get sanitized POST input.
     */
    protected function input(string $key, $default = null): mixed
    {
        return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
    }

    /**
     * Get sanitized GET input.
     */
    protected function query(string $key, $default = null): mixed
    {
        return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
    }

    /**
     * Require authenticated session. Redirects to login if not.
     */
    protected function requireAuth(): void
    {
        if (empty($_SESSION['user_id'])) {
            $_SESSION['flash_error'] = 'Silakan login terlebih dahulu.';
            $this->redirect('/auth/login');
        }
    }

    /**
     * Require admin role.
     */
    protected function requireAdmin(): void
    {
        $this->requireAuth();
        if (($_SESSION['user_role'] ?? '') !== 'admin') {
            http_response_code(403);
            $this->redirect('/');
        }
    }

    /**
     * Set flash message in session.
     */
    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash_' . $type] = $message;
    }
}
