<?php

namespace App\Models;

use App\DB\Core\IntegerField;
use App\DB\Core\StringField;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function saveableFields(): array
    {
        return [
            'title' => StringField::new(),
            'author_id'=>IntegerField::new(),
            'genre_id'=>IntegerField::new(),
            'status_id'=>IntegerField::new()
        ];
    }
}
