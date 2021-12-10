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
        require_once __DIR__ . '/../core/Guards.php';

        // SESSION
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // CONNECTION
        $this->connect = new Connect();
        $this->connection = $this->connect->connecting();

        // GUARDS
        Guards::userEditGuard();
        Guards::postEditGuard($this->connection);

        // INIT TWIG
        $loader = new Twig_Loader_Filesystem('view');
        $this->twig = new Twig_Environment($loader, array('debug' => true));
        $this->twig->addGlobal('session', $_SESSION);
    }
}
