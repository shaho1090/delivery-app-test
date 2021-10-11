<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'note',
    ];

    public function createNew(array $request) : Note
    {
        $this->attributes['author_id'] = $request['author_id'];
        $this->attributes['title'] = $request['title'];
        $this->attributes['note'] = $request['note'];
        $this->save();

        return $this;
    }
}
