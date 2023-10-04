<?php

namespace App\Models;

use App\Enums\PostTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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
        'type',
        'content',
    ];

    protected $casts = [
        'type' => PostTypeEnum::class,
    ];

    /**
     * Get post author.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get comments from the post.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Get likes from the post
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}
