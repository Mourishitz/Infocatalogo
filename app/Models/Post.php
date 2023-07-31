<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    /**
     * Table associated with the model.
     *
     * @var string
     */
    protected $table = 'posts';

    protected $fillable = [
        'title',
        'type'
    ];

    /**
     * Get post author.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
