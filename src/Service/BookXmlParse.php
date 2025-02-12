<?php

class BookXmlParse
{
    protected $books = [];

    public function xmlToArray($file)
    {
        $booksList = simplexml_load_file($file);
        if ($booksList === false) {
            echo "Failed to load XML\n";
            foreach (libxml_get_errors() as $error) {
                echo $error->message;
            }
            exit;
        }
        foreach ($booksList as $book) {
            $this->books[] = (array) $book;
        }

        return $this;
    }

    public function getData()
    {
        return $this->books;
    }
}
