<?php

namespace Haider\Comments\Events;

use Illuminate\Queue\SerializesModels;
use Haider\Comments\Comment;

class CommentCreated
{
    use SerializesModels;

    public $comment;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }
}
