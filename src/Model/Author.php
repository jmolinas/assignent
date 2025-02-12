<?php

require_once __DIR__ . '/../../src/Core/Model.php';

class Author extends Model
{
    public $table = 'authors';

    public function books()
    {
        return $this->hasMany(Book::class, 'author_id');
    }

    public function search($query)
    {
        return $this->where('LIKE', ['name' => "%{$query}%"]);
    }
}
