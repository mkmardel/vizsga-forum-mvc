<?php
class Guards
{
    /**
     * Check if the logged in user is authorized to edit the selected User
     * Redirects to hompage if not authorized
     */
    public static function userEditGuard()
    {
        $queries = array();
        parse_str($_SERVER['QUERY_STRING'], $queries);
        if ($queries['controller'] == 'user' && $queries['action'] == 'details') {
            if ($_SESSION['user_role'] != 'admin' && $_SESSION['user_id'] != $queries['id']) {
                header('Location: index.php');
            }
        }
    }

    /**
     * Check if the logged in user is authorized to edit the selected Post
     * Redirects to hompage if not authorized
     * @param PDO $connection PDO connection
     */
    public static function postEditGuard($connection)
    {
        $queries = array();
        parse_str($_SERVER['QUERY_STRING'], $queries);
        if ($queries['controller'] == 'post' && ($queries['action'] == 'details' || $queries['action'] == 'delete')) {
            require_once __DIR__ . '/../models/Post.php';
            $post = new Post($connection);
            $authorId = $post->getById($queries['id'])->user_id;
            if ($_SESSION['user_role'] != 'admin' && $_SESSION['user_id'] != $authorId) {
                header('Location: index.php');
            }
        }
    }
}
