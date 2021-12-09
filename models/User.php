<?php
require_once __DIR__ . '/General.php';
require_once __DIR__ . '/../core/Bcrypt.php';

class User extends General
{
    private $name;
    private $email;
    private $password;
    private $role;
    private $errors = array();

    public function __construct($connection)
    {
        parent::__construct($connection);
        $this->table = TABLE_USERS;
        $this->role = 'user';
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        if (mb_strlen($name) > 0 && mb_strlen($name) <= 30) {
            $this->name = filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS);
        } else {
            $this->errors[] = 'A név nem lehet több 30 karakternél.';
        }
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->email = filter_var($email, FILTER_SANITIZE_EMAIL);
        } else {
            $this->errors[] = 'Az email cím valós kell, hogy legyen.';
        }
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password1
     * @param string $password2
     */
    public function setPassword(string $password1, string $password2): void
    {
        if ($password1 === $password2) {
            if (mb_strlen($password1) >= 4) {
                $this->password = filter_var($password1, FILTER_SANITIZE_SPECIAL_CHARS);
            } else {
                $this->errors[] = 'A jelszó hossza minimum 3 karakter.';
            }
        } else {
            $this->errors[] = 'A két jelszó nem egyezik.';
        }
    }

    /**
     * @throws Exception
     */
    public function create()
    {
        if ($this->emailIsUnique()) {
            $hashPassword = Bcrypt::hashPassword($this->password);
            $query = $this->connection->prepare('INSERT INTO ' . $this->table . ' (name, email, password, role) VALUES (:name, :email, :password, :role)');
            $result = $query->execute(array(
                'name' => $this->name,
                'email' => $this->email,
                'password' => $hashPassword,
                'role' => $this->role,
            ));
            $this->connection = null;
            return $result;
        } else {
            $this->errors[] = 'Az megadott email cím már regisztrálva van.';
            return false;
        }
    }

    public function update()
    {
        $query = $this->connection->prepare(
            'UPDATE ' . $this->table .
                ' SET
                name = :name
             WHERE id = :id'
        );

        $result = $query->execute(array(
            'name' => $this->name,
            'id' => $this->id
        ));
        $this->connection = null;
        return $result;
    }

    public function delete()
    {
        $this->deleteComments();
        $this->deleteById($this->id);
    }

    public function deleteComments()
    {
        try {
            $query = $this->connection->prepare("DELETE FROM Posts WHERE user_id = :id");
            $query->execute(array('id' => $this->id));
            //$this->connection = null;
            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * @throws Exception
     */
    public function validate($password)
    {
        $user = $this->getByColumn('email', $this->email);
        if (!empty($user)) {
            $compare = Bcrypt::checkPassword(
                $password,
                $user[0]['password']
            );
            if ($compare) {
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $user[0]['id'];
                $_SESSION['user_role'] = $user[0]['role'];
                return $user[0];
            }
        }
        return false;
    }

    public function emailIsUnique()
    {
        $result = $this->getByColumn('email', $this->email);
        if (empty($result)) {
            return true;
        }
        return false;
    }
}
