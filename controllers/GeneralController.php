<?php

class GeneralController
{
    protected $connect;
    protected $connection;
    protected $twig;

    /**
     * @throws Exception if connection failed
     */
    public function __construct()
    {
        require_once __DIR__ . '/../core/Connect.php';
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->connect = new Connect();
        $this->connection = $this->connect->connecting();
        $loader = new Twig_Loader_Filesystem('view');
        $this->twig = new Twig_Environment($loader, array('debug' => true));
        $this->twig->addGlobal('session', $_SESSION);
    }
}
