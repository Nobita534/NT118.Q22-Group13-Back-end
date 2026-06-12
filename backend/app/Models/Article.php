<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $table = 'Article';

    protected $primaryKey = 'Article_ID';

    public $timestamps = false;

    protected $fillable = [
        'Title', 'Slug', 'ThumbnailURL', 'Original_URL', 'URL_Hash', 'PublishDate', 'ViewCount', 'Source_ID'
    ];
}
