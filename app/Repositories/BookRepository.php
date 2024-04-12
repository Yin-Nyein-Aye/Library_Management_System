<?php

namespace App\Repositories;

use App\Contracts\AddressInterface;
use App\Contracts\BookInterface;
use App\Contracts\UserInterface;
use App\Db\Core\Crud;
use App\Models\Address;
use App\Models\Book;
use App\Models\User;

class BookRepository implements BookInterface
{
    public function all()
    {
        return Book::paginate(5);
    }

    public function findByID(string $modelName, int $id)
    {
        $model = app("App\\Models\\{$modelName}");
        return $model::find($id);
    }

    public function store(string $modelName, array $data)
    {
        return (new Crud(new Book(), $data, null, false, false))->execute();
    }

    public function update(string $modelName, array $data, int $id)
    {
        return (new Crud(new Book(), $data, $id, true, false))->execute();
    }

    public function delete(string $modelName, int $id)
    {
        return (new Crud(new Book(), null, $id, false, true))->execute();
    }
}
