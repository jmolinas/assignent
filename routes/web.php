<?php

require __DIR__ . '/../src/bootstrap.php';
$templatePath = __DIR__ . '/../src/View';
$view = new View($templatePath);

$http = new Http();
$router = new Router($http);
$authors = new Author();
$router->get('/search', function () use ($view) {
    $view->render('search.php', ['title' => 'Search', 'name' => 'joseph',]);
});
$router->post('/api/search', function () use ($http, $authors) {
    $query = $http->input('query');
    $author = $authors->with('books')->search($query);
    $author = $author === false ? [] : $author;
    $books = [];
    if (count($author)) {
        foreach ($author as $value) {
            foreach ($value['books'] as $book) {
                $book['author_name'] = $value['name'];
                $books[] = $book;
            }
        }
    }
    echo $http->json($books);
});
$router->execute();
