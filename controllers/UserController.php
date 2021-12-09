<?php

class UserController extends GeneralController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . '/../models/User.php';
    }

    public function run($action)
    {
        switch ($action) {
            case 'registration':
                $this->registration();
                break;
            case 'login':
                $this->login();
                break;
            case 'logout':
                $this->logout();
                break;
            case 'validate':
                $this->validate();
                break;
            case 'create':
                $this->create();
                break;
            case 'details':
                $this->details();
                break;
            case 'update':
                $this->update();
                break;
            case 'delete':
                $this->delete();
                break;
            default:
                $this->index();
                break;
        }
    }

    private function index()
    {
        $user = new User($this->connection);
        $users = $user->getAll();
        echo $this->twig->render('users.twig', array('users' => $users, 'title' => 'Felhasználók'));
    }

    private function registration()
    {
        echo $this->twig->render('registration.twig', array('title' => 'Regisztráció'));
    }

    private function login()
    {
        echo $this->twig->render('login.twig', array('title' => 'Bejelentkezés'));
    }

    private function logout()
    {
        $_SESSION['logged_in'] = false;
        session_destroy();
        header('Location: index.php');
    }

    private function validate()
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        $user = new User($this->connection);
        $user->setEmail($email);
        $loggedInUser = $user->validate($password);
        if (!empty($loggedInUser)) {
            header('Location: index.php');
        } else {
            echo $this->twig->render('login.twig', array('errors' => array('Helytelen felhasználónév vagy jelszó!'), 'title' => 'Bejelentkezés'));
        }
    }

    private function create()
    {
        $user = new User($this->connection);
        $this->setProperties('post', $user);
        $user->create();
        if (!empty($user->getErrors())) {
            echo $this->twig->render('registration.twig', array('errors' => $user->getErrors(), 'title' => 'Regisztráció'));
        } else {
            echo $this->twig->render('login.twig', array('success' => 'Sikeres regisztráció.', 'title' => 'Bejelentkezés'));
        }
    }

    private function details()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $user = new User($this->connection);
        $user = $user->getById($id);
        echo $this->twig->render('userDetails.twig', array(
            'user' => $user,
            'title' => 'Felhasználó adatai'
        ));
    }

    private function update()
    {
        $user = new User($this->connection);
        $this->setProperties('post', $user);
        $existingUsername = $user->getById($user->getId())->name;
        if (!empty($user->getErrors())) {
            $user->setName($existingUsername);
            echo $this->twig->render('userDetails.twig', array('errors' => $user->getErrors(), 'user' => $user, 'title' => 'Felhasználó adatai'));
        } else {
            $user->update();
            echo $this->twig->render('userDetails.twig', array('success' => 'Sikeres módosítás.', 'user' => $user, 'title' => 'Felhasználó adatai'));
        }
    }

    /**
     * @param string $method 'post' / 'get'
     * @param User $postObject selected post to set properties
     */
    private function setProperties(string $method, User $userObject)
    {
        $input = ($method == 'post' ? INPUT_POST : INPUT_GET);
        if (filter_input($input, 'id')) {
            $userObject->setId(filter_input($input, 'id', FILTER_SANITIZE_NUMBER_INT));
        }
        if (filter_input($input, 'name')) {
            $userObject->setName(filter_input($input, 'name', FILTER_SANITIZE_STRING));
        }
        if (filter_input($input, 'email')) {
            $userObject->setEmail(filter_input($input, 'email', FILTER_SANITIZE_EMAIL));
        }
        if (filter_input($input, 'password1')) {
            $userObject->setPassword(
                filter_input($input, 'password1', FILTER_SANITIZE_STRING),
                filter_input($input, 'password2', FILTER_SANITIZE_STRING),
            );
        }
    }

    private function delete()
    {
        $user = new User($this->connection);
        $user->setId(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT));
        $user->delete();
        $this->run('index');
    }

    public function view($view)
    {
        require_once __DIR__ . '/../view/' . $view . '.php';
    }
}
