<?php
require_once __DIR__ . '/General.php';

class Category extends General
{
    private $name;
    private $errors = array();

    public function __construct($connection)
    {
        parent::__construct($connection);
        $this->table = TABLE_CATEGORIES;
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
            $this->errors[] = 'A kategória neve nem lehet több 30 karakternél.';
        }
    }

    public function create()
    {
        if ($this->categoryIsUnique()) {
            if (empty($this->errors)) {
                $query = $this->connection->prepare('INSERT INTO ' . $this->table . ' (name) VALUES (:name)');
                $result = $query->execute(array(
                    'name' => $this->name,
                ));
                $this->connection = null;
                return $result;
            }
        } else {
            $this->errors[] = 'Ez a kategória már létezik.';
            return false;
        }
    }

    public function categoryIsUnique()
    {
        $result = $this->getByColumn('name', $this->name);
        if (empty($result)) {
            return true;
        }
        return false;
    }
}
