<?php

class AuthController extends Controller
{
    public function login()
    {
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::verifyCsrf();
            $email    = isset($_POST['email'])    ? $_POST['email']    : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';

            $userModel = $this->model('User');
            $user = $userModel->findByEmail($email);

            if ($user && $userModel->verifyPassword($user, $password)) {
                Auth::login($user);
                if (Auth::isAdmin()) {
                    $this->redirect('admin');
                } else {
                    $this->redirect('');
                }
            } else {
                $error = 'E-mail ou mot de passe invalide.';
            }
        }
        $this->view('auth/login', array('title' => 'Connexion', 'error' => $error));
    }

    public function register()
    {
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::verifyCsrf();
            $name     = isset($_POST['name'])     ? $_POST['name']     : '';
            $email    = isset($_POST['email'])    ? $_POST['email']    : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $confirm  = isset($_POST['confirm'])  ? $_POST['confirm']  : '';

            // Nom : lettres uniquement.
            if ($name == '' || !preg_match("/^[a-zA-ZÀ-ÿ\s\-]+$/", $name)) {
                $error = 'Le nom doit contenir uniquement des lettres.';
            }
            // Email.
            elseif ($email == '' || !preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
                $error = "L'adresse mail n'est pas valide.";
            }
            // Mot de passe : au moins 6 caractères.
            elseif (strlen($password) < 6) {
                $error = 'Le mot de passe doit contenir au moins 6 caractères.';
            }
            // Confirmation.
            elseif ($password != $confirm) {
                $error = 'Les mots de passe ne correspondent pas.';
            }

            $userModel = $this->model('User');
            if (!$error && $userModel->findByEmail($email)) {
                $error = 'Cet e-mail est déjà enregistré.';
            }

            if (!$error) {
                $id = $userModel->create($name, $email, $password, 'client');
                Auth::login($userModel->find($id));
                $this->redirect('');
            }
        }
        $this->view('auth/register', array('title' => 'Créer un compte', 'error' => $error));
    }

    public function logout()
    {
        Auth::logout();
        $this->redirect('');
    }

    // ---------- GOOGLE OAUTH ----------

    public function google()
    {
        require_once APP_ROOT . '/app/services/OAuth.php';
        if (!OAuth::googleEnabled()) {
            $this->oauthError('Connexion Google non configurée.');
            return;
        }
        header('Location: ' . OAuth::googleAuthUrl());
        exit;
    }

    public function googleCallback()
    {
        require_once APP_ROOT . '/app/services/OAuth.php';
        $code  = isset($_GET['code'])  ? $_GET['code']  : '';
        $state = isset($_GET['state']) ? $_GET['state'] : '';
        if ($code === '' || $state === '') {
            $this->oauthError('Réponse Google invalide.');
            return;
        }
        $profile = OAuth::googleHandleCallback($code, $state);
        if (!$profile) {
            $this->oauthError('Impossible de vous authentifier avec Google.');
            return;
        }
        $this->loginOrCreateOAuthUser('google', $profile);
    }

    // ---------- FACEBOOK OAUTH ----------

    public function facebook()
    {
        require_once APP_ROOT . '/app/services/OAuth.php';
        if (!OAuth::facebookEnabled()) {
            $this->oauthError('Connexion Facebook non configurée.');
            return;
        }
        header('Location: ' . OAuth::facebookAuthUrl());
        exit;
    }

    public function facebookCallback()
    {
        require_once APP_ROOT . '/app/services/OAuth.php';
        $code  = isset($_GET['code'])  ? $_GET['code']  : '';
        $state = isset($_GET['state']) ? $_GET['state'] : '';
        if ($code === '' || $state === '') {
            $this->oauthError('Réponse Facebook invalide.');
            return;
        }
        $profile = OAuth::facebookHandleCallback($code, $state);
        if (!$profile) {
            $this->oauthError('Impossible de vous authentifier avec Facebook.');
            return;
        }
        $this->loginOrCreateOAuthUser('facebook', $profile);
    }

    // ---------- SHARED OAUTH HELPERS ----------

    private function loginOrCreateOAuthUser($provider, $profile)
    {
        $userModel = $this->model('User');

        // 1) Already linked with this provider? log in.
        $user = $userModel->findByProvider($provider, $profile['id']);

        $avatar = null;
        if (isset($profile['avatar'])) {
            $avatar = $profile['avatar'];
        }

        // 2) Otherwise, match by email — link the provider to the existing local account.
        if (!$user && !empty($profile['email'])) {
            $existing = $userModel->findByEmail($profile['email']);
            if ($existing) {
                $userModel->linkProvider((int)$existing['id'], $provider, $profile['id'], $avatar);
                $user = $userModel->find((int)$existing['id']);
            }
        }

        // 3) Otherwise, create a new account.
        if (!$user) {
            $id = $userModel->createOAuth(
                $profile['name'],
                $profile['email'],
                $provider,
                $profile['id'],
                $avatar
            );
            $user = $userModel->find($id);
        }

        Auth::login($user);
        if (Auth::isAdmin()) {
            $this->redirect('admin');
        } else {
            $this->redirect('');
        }
    }

    private function oauthError($message)
    {
        $this->view('auth/login', array('title' => 'Connexion', 'error' => $message));
    }
}
