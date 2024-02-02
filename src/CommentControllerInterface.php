<?php

namespace Haider\Comments;

use Illuminate\Http\Request;

interface CommentControllerInterface
{
    /**
     * Creates a new comment for given model.
     */
    public function store(Request $request);

    /**
     * Updates the message of the comment.
     */
    public function update(Request $request, Comment $comment);

    /**
     * Updates the status (approve/reject) of the comment.
     */
    public function status(Request $request, Comment $comment);

    /**
     * Deletes a comment.
     */
    public function destroy(Comment $comment);

    /**
     * Creates a reply "comment" to a comment.
     */
    public function reply(Request $request, Comment $comment);
}