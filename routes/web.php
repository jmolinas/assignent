<?php

require __DIR__.'/../src/bootstrap.php';
$templatePath = __DIR__.'/../src/View';
$view = new View($templatePath);

$http = new Http();
$router = new Router($http);

$router->route('/search', function() use ($view) {
    $view->render('search.php', ['title' => 'Search', 'name' => 'joseph']);
});
$router->execute();