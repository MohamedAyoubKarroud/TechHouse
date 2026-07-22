<?php
// Base controller. View rendering + model loading + helpers.

class Controller
{
    protected function model($name)
    {
        $file = APP_ROOT . '/app/models/' . $name . '.php';
        if (!file_exists($file)) {
            throw new RuntimeException("Model not found: $name");
        }
        require_once $file;
        return new $name();
    }

    protected function view($view, $data = array())
    {
        $file = APP_ROOT . '/app/views/' . $view . '.php';
        if (!file_exists($file)) {
            throw new RuntimeException("View not found: $view");
        }
        extract($data, EXTR_SKIP);
        require APP_ROOT . '/app/views/layouts/header.php';
        require $file;
        require APP_ROOT . '/app/views/layouts/footer.php';
    }

    protected function viewBare($view, $data = array())
    {
        $file = APP_ROOT . '/app/views/' . $view . '.php';
        extract($data, EXTR_SKIP);
        require $file;
    }

    protected function redirect($path)
    {
        header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
        exit;
    }

    protected function requireAuth()
    {
        if (!Auth::check()) {
            $_SESSION['flash'] = 'Veuillez vous connecter pour continuer.';
            $this->redirect('auth/login');
        }
    }

    protected function requireAdmin()
    {
        $this->requireAuth();
        if (!Auth::isAdmin()) {
            http_response_code(403);
            echo 'Accès refusé — droits administrateur requis.';
            exit;
        }
    }

    protected function json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
