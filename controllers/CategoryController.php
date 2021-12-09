<?php
class CategoryController extends GeneralController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . '/../models/Category.php';
        require_once __DIR__ . '/../models/Post.php';
    }

    public function run($action)
    {
        switch ($action) {
            case 'new':
                $this->new();
                break;
            case 'create':
                $this->create();
                break;
            case 'posts':
                $this->posts();
                break;
            default:
                $this->index();
                break;
        }
    }

    private function index()
    {
        $category = new Category($this->connection);
        $categories = $category->getAll();
        echo $this->twig->render('index.twig', array('categories' => $categories, 'title' => 'Kategóriák'));
    }

    private function new()
    {
        echo $this->twig->render('newCategory.twig', array('title' => 'Új kategória'));
    }

    private function create()
    {

        $category = new Category($this->connection);
        $this->setProperties($category);
        $category->create();
        if (!empty($category->getErrors())) {
            echo $this->twig->render('newCategory.twig', array('errors' => $category->getErrors(), 'title' => 'Új kategória'));
        } else {
            $this->run('index');
        }
    }

    private function posts()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $category = new Category($this->connection);
        $category = $category->getById($id);
        $post = new Post($this->connection);
        $posts = $post->getAllWithUsername($id);
        echo $this->twig->render('categoryPosts.twig', array(
            'category' => $category,
            'posts' => $posts,
            'title' => 'Hozzászólások: ' . $category->name
        ));
    }

    private function setProperties(Category $categoryObject)
    {
        $categoryObject->setName(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    }

    public function view($view)
    {
        require_once __DIR__ . '/../view/' . $view . '.php';
    }
}
