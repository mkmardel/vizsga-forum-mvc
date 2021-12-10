<?php

class PostController extends GeneralController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . '/../models/Category.php';
        require_once __DIR__ . '/../models/Post.php';
    }

    /**
     * @throws ErrorException If no action
     */
    public function run($action)
    {
        switch ($action) {
            case 'new':
                $this->new();
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
                throw new ErrorException('No such page');
        }
    }

    private function new()
    {
        echo $this->twig->render('newPost.twig', array(
            'category' => filter_input(INPUT_GET, 'category'),
            'title' => 'Új hozzászólás'
        ));
    }

    private function create()
    {
        $post = new Post($this->connection);
        $this->setProperties('post', $post);
        if (!empty($post->getErrors())) {
            echo $this->twig->render('newPost.twig', array(
                "category" => $post->getCategoryId(),
                "errors" => $post->getErrors(),
                'title' => 'Új hozzászólás'
            ));
        } else {
            $post->create();
            header('Location:index.php?controller=category&action=posts&id=' . $post->getCategoryId());
        }
    }

    /**
     * @param string $method 'post' / 'get'
     * @param Post $postObject selected post to set properties
     */
    private function setProperties(string $method, Post $postObject)
    {
        $input = ($method == 'post' ? INPUT_POST : INPUT_GET);
        if (filter_input($input, 'id')) {
            $postObject->setId(filter_input($input, 'id', FILTER_SANITIZE_NUMBER_INT));
        }
        if (filter_input($input, 'content')) {
            $postObject->setContent(filter_input($input, 'content', FILTER_SANITIZE_STRING));
        }
        if (filter_input($input, 'category')) {
            $postObject->setCategoryId(filter_input($input, 'category', FILTER_SANITIZE_STRING));
        }
    }

    private function details()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $post = new Post($this->connection);
        $result = $post->getById($id);
        echo $this->twig->render('postDetails.twig', array(
            "post" => $result,
            "category" => filter_input(INPUT_GET, 'category'),
            "title" => "Hozzászólás részletei"
        ));
    }

    private function update()
    {
        $post = new Post($this->connection);
        $this->setProperties('post', $post);
        $post->update();
        if (!empty($post->getErrors())) {
            $this->redirectWithErrors($post, 'update');
        } else {
            header("Location:index.php?controller=category&action=posts&id=" . $post->getCategoryId());
        }
    }

    private function delete()
    {
        $post = new Post($this->connection);
        $this->setProperties('get', $post);
        $post->delete();
        if (!empty($post->getErrors())) {
            $this->redirectWithErrors($post, 'delete');
        } else {
            header("Location:index.php?controller=category&action=posts&id=" . $post->getCategoryId());
        }
    }

    /**
     * Redirects with error message
     * @param Post $post
     * @param string $action 'update' / 'delete'
     */
    private function redirectWithErrors(Post $post, $action)
    {
        $posts = $post->getAllWithUsername($post->getCategoryId());
        $category = new Category($this->connection);
        $category = $category->getById($post->getCategoryId());
        if ($action == 'delete') {
            echo $this->twig->render('categoryPosts.twig', array(
                "errors" => $post->getErrors(),
                "posts" => $posts,
                "category" => $category,
                "title" => "Hozzászólás részletei"
            ));
        }
        if ($action == 'update') {
            echo $this->twig->render('postDetails.twig', array(
                "errors" => $post->getErrors(),
                "post" => $post,
                "category" => $post->getCategoryId(),
                "title" => "Hozzászólás részletei"
            ));
        }
    }

    public function view($view)
    {
        require_once __DIR__ . '/../view/' . $view . '.php';
    }
}
