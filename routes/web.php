<?php

require __DIR__ . '/../src/bootstrap.php';
$templatePath = __DIR__ . '/../src/View';
$view = new View($templatePath);

$http = new Http();
$router = new Router($http);
$router->get('/searchjkj', function () {
    echo 'hahaha';
});
$router->post('/search', function () use ($view) {
    $fileImport = new FileImport('xml');
    $files = $fileImport->scanFolder();
    $books = [];
    foreach ($files as $file) {
        $booksList = simplexml_load_file($file);
        if ($booksList === false) {
            echo "Failed to load XML\n";
            foreach (libxml_get_errors() as $error) {
                echo $error->message;
            }
            exit;
        }
        foreach ($booksList as $key => $book) {
            $books[] = ['name' => (string) $book->name, 'author' => (string) $book->author];
        }
    }
    $view->render('search.php', ['title' => 'Search', 'name' => 'joseph', 'files' => $files]);
});
$router->execute();
