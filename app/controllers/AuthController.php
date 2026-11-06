<?php
class AuthController extends Controller
{
    public function register(): void
    {
        if (Auth::check()) {
            $this->redirect('/');
        }

        $errors = [];
        $old = [];

        if ($this->isPost()) {
            $old = [
                'name'  => Validator::clean($_POST['name'] ?? ''),
                'email' => Validator::clean($_POST['email'] ?? ''),
                'phone' => Validator::clean($_POST['phone'] ?? ''),
            ];
            $password = $_POST['password'] ?? '';
            $confirm  = $_POST['confirm_password'] ?? '';
            $users = new User();

            if ($old['name'] === '') {
                $errors[] = 'Name is required.';
            }
            if (!Validator::email($old['email'])) {
                $errors[] = 'Enter a valid email.';
            } elseif ($users->findByEmail($old['email'])) {
                $errors[] = 'This email is already registered.';
            }
            if (strlen($password) < 6) {
                $errors[] = 'Password must be at least 6 characters.';
            }
            if ($password !== $confirm) {
                $errors[] = 'Passwords do not match.';
            }

            if (!$errors) {
                $id = $users->create($old['name'], $old['email'], $password, $old['phone']);
                Auth::login($users->find($id));
                $this->redirect('/');
            }
        }

        $this->view('user/auth/register', compact('errors', 'old'));
    }

    public function login(): void
    {
        if (Auth::check()) {
            $this->redirect('/');
        }

        $errors = [];
        $old = [];

        if ($this->isPost()) {
            $email    = Validator::clean($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $user = (new User())->findByEmail($email);

            if ($user && password_verify($password, $user['password_hash'])) {
                Auth::login($user);
                $this->redirect($user['role'] === 'admin' ? '/admin' : '/');
            }
            $errors[] = 'Invalid email or password.';
            $old = ['email' => $email];
        }

        $this->view('user/auth/login', compact('errors', 'old'));
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: ' . BASE_URL . '/');
        exit;
    }
}
