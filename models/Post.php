<?php
require_once __DIR__ . '/General.php';

class Post extends General
{
    private $content;
    private $time;
    private $categoryId;
    private $userId;
    private $errors = array();

    public function __construct($connection)
    {
        parent::__construct($connection);
        $this->table = TABLE_POSTS;
        $this->userId = $_SESSION['user_id'];
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
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        if (mb_strlen($content) > 0 && mb_strlen($content) <= 100) {
            $this->content = filter_var($content, FILTER_SANITIZE_SPECIAL_CHARS);
        } else {
            $this->errors[] = 'Content can be up to 100 characters.';
        }
    }

    /**
     * @return string
     */
    public function getTime(): string
    {
        return $this->time;
    }

    /**
     * @return string
     */
    public function getCategoryId(): string
    {
        return $this->categoryId;
    }

    /**
     * @param string $categoryId
     */
    public function setCategoryId($categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     */
    public function setUserId($userId): void
    {
        $this->userId = $userId;
    }

    public function getAllByCategory($id)
    {
        return $this->getByColumn('category_id', $id);
    }

    public function getAllWithUsername($categoryId)
    {
        try {
            $query = $this->connection->prepare(
                'SELECT
                Posts.id,
                Posts.content,
                Posts.time,
                Posts.user_id,
                Users.name
              FROM
                Posts
                LEFT JOIN Users
                ON Posts.user_id = Users.id 
                WHERE Posts.category_id = ' . $categoryId . ' 
                ORDER BY Posts.id DESC'

            );
            $query->execute();
            $results = $query->fetchAll();
            $this->connection = null;
            return $results;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function create()
    {
        try {
            $query = $this->connection->prepare('INSERT INTO ' . $this->table . ' (content, time, category_id, user_id) VALUES (:content, :time, :category_id, :user_id)');
            $result = $query->execute(array(
                'content' => $this->content,
                'time' => time(),
                'category_id' => $this->categoryId,
                'user_id' => $this->userId
            ));
            $this->connection = null;
            return $result;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function update()
    {
        if ($this->userCanEdit()) {
            $query = $this->connection->prepare(
                'UPDATE ' . $this->table .
                    ' SET
                    content = :content 
                    WHERE id = :id'
            );

            $result = $query->execute(array(
                'content' => $this->content,
                'id' => $this->id
            ));
            $this->connection = null;
            return $result;
        } else {
            $this->errors[] = 'A hozzászólás 5 perc után nem módosítható.';
        }
    }

    public function delete()
    {
        if ($this->userCanEdit()) {
            $this->deleteById($this->id);
        } else {
            $this->errors[] = 'A hozzászólás 5 perc után nem törölhető.';
        }
    }

    private function userCanEdit()
    {
        if ($_SESSION['user_role'] == 'admin') {
            return true;
        }
        $post = $this->getById($this->id);
        if ((int)($post->time + (5 * 60)) > time()) {
            return true;
        }
        return false;
    }
}
