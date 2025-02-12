<?php
require __DIR__ . '/../src/bootstrap.php';

if (php_sapi_name() !== 'cli') {
  exit(1);
}

$fileImport = new FileImport('xml');
$files = $fileImport->scanFolder();
$bookParse = new BookXmlParse();
$book = new Book();
$author = new Author();

foreach ($files as $file) {
  $bookParse->xmlToArray($file);
}

$booklist = $bookParse->getData();
$books = [];
$authors = [];
foreach ($booklist as $list) {
  $authordata = ['name' => $list['author']];
  $authorResult = $author->createOrUpdate($authordata, $authordata);
  $books[] = ['name' => $list['name'], 'author_id' => $authorResult['id']];
}

$book->upsert('name', $books);
