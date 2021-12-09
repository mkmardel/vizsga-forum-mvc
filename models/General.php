<?php
class General
{
    protected $table;
    protected $connection;
    protected $id;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getAll()
    {
        try {
            $query = $this->connection->prepare('SELECT * FROM ' . $this->table);
            $query->execute();
            $results = $query->fetchAll();
            $this->connection = null;
            return $results;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function getById($id)
    {
        try {
            $query = $this->connection->prepare('SELECT * FROM ' . $this->table . " WHERE id = :id");
            $query->execute(array('id' => $id));
            //$this->connection = null;
            return $query->fetchObject();
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function getByColumn($column, $value)
    {
        try {
            $query = $this->connection->prepare('SELECT * FROM ' . $this->table . " WHERE " . $column . " = :value");
            $query->execute(array(
                'value' => $value
            ));

            //$this->connection = null;
            return $query->fetchAll();
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function deleteById($id)
    {
        try {
            $query = $this->connection->prepare('DELETE FROM ' . $this->table . " WHERE id = :id");
            $query->execute(array('id' => $id));
            $this->connection = null;
            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function deleteByColumn($column, $value)
    {
        try {
            $query = $this->connection->prepare('DELETE FROM ' . $this->table . " WHERE " . $column . " = :value");
            $query->execute(array(
                'value' => $value
            ));
            $this->connection = null;
            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }
}
