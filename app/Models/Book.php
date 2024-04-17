<?php

namespace App\Models;

use App\DB\Core\DateTimeField;
use App\DB\Core\IntegerField;
use App\DB\Core\StringField;
use Faker\Provider\ar_EG\Internet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Config;
use Ramsey\Uuid\Type\Integer;

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
            'status_id'=>IntegerField::new(),
            'user_id' => IntegerField::new(),
            'borrow_return_date' => DateTimeField::new()
        ];
    }

    public function author():BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function genre():BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    public function status():BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeBorrowBook(Builder $query):Builder
    {
        return $query->where("status_id",Config::get('variables.BORROW'));
    }

    public function scopeReturnBook(Builder $query):Builder
    {
        return $query->where("status_id",Config::get('variables.RETURN'));
    }

    public function scopeSearchingBook(Builder $query, $filters)
    {
        $query->when($filters['author'] ?? false, function ($query, $author) {
            $query->whereHas('author', function ($query) use($author)
            {
                $query->where('name','LIKE', '%'.  $author . '%');
            });
        });

        $query->when($filters['genre'] ?? false, function ($query, $genre_id) {
            $query->whereHas('genre', function ($query) use ($genre_id) {
                $query->where('id', $genre_id);
            });
        });

        $query->when($filters['title'] ?? false, function ($query, $title) {
            $query->where('title', 'LIKE', '%'. $title . '%');
        });
    }
}
