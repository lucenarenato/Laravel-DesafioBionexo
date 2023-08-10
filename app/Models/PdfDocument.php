<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Laravel\Scout\Attributes\SearchUsingFullText;

class PdfDocument extends Model
{
    use Searchable;

    protected $table = "pdf_documents";

    protected $primaryKey = 'id';

    protected $fillable = ['title', 'content'];

    protected $dates = ['created_at', 'updated_at'];

    //#[SearchUsingFullText(["title", "content"])]
    public function toSearchableArray()
    {
        return [
            "title" => $this->title,
            "content" => $this->content,
        ];
    }
}
