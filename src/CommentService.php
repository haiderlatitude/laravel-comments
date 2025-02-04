<?php

namespace Haider\Comments;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class CommentService
{
    /**
     * Handles creating a new comment for given model.
     * @return mixed the configured comment-model
     */
    public function store(Request $request)
    {

        // If guest commenting is turned off, authorize this action.
        if (Config::get('comments.guest_commenting') == false) {
            Gate::authorize('create-comment', Comment::class);
        }
        $model = $request->commentable_type::findOrFail($request->commentable_id);

        if (!$model->commentable()) {
            abort(403, 'This model does not support comments.');
        }

        // Define guest rules if user is not logged in.
        if (!Auth::check()) {
            $guest_rules = [
                'guest_name' => 'required|string|max:255',
                'guest_email' => 'required|string|email|max:255',
            ];
        }

        // Merge guest rules, if any, with normal validation rules.
        Validator::make($request->all(), array_merge($guest_rules ?? [], [
            'commentable_type' => 'required|string',
            'commentable_id' => 'required|string|min:1',
            'message' => 'required|string'
        ]))->validate();


        $commentClass = Config::get('comments.model');
        $comment = new $commentClass;

        if (!Auth::check()) {
            $comment->guest_name = $request->guest_name;
            $comment->guest_email = $request->guest_email;
        } else {
            $comment->commenter()->associate(Auth::user());
        }

        $comment->commentable()->associate($model);
        $comment->comment = $request->message;
        $comment->approved = !Config::get('comments.approval_required');
        $comment->save();

        if($comment->approved) {
            session()->flash('success', 'Comment added successfully.');
        } else {
            session()->flash('success', 'Comment submitted for approval successfully.');
        }

        return $comment;
    }

    public function status(Request $request, Comment $comment): Comment
    {
        Validator::make($request->all(), [
            'approved' => 'required|boolean'
        ]);

        $comment->update([
            'approved' => $request->get('approved')
        ]);

        session()->flash('success', 'Comment status updated successfully.');

        return $comment;
    }

    /**
     * Handles updating the message of the comment.
     * @return mixed the configured comment-model
     */
    public function update(Request $request, Comment $comment)
    {
        Gate::authorize('edit-comment', $comment);

        Validator::make($request->all(), [
            'message' => 'required|string'
        ])->validate();

        $comment->update([
            'comment' => $request->message
        ]);

        session()->flash('success', 'Comment updated successfully.');

        return $comment;
    }

    /**
     * Handles deleting a comment.
     * @return mixed the configured comment-model
     */
    public function destroy(Comment $comment): void
    {
        Gate::authorize('delete-comment', $comment);

        if (Config::get('comments.soft_deletes') == true) {
            $comment->delete();
        } else {
            $comment->forceDelete();
        }

        session()->flash('success', 'Comment deleted successfully.');
    }

    /**
     * Handles creating a reply "comment" to a comment.
     * @return mixed the configured comment-model
     */
    public function reply(Request $request, Comment $comment)
    {
        Gate::authorize('reply-to-comment', $comment);

        if (!$comment->commentable->commentable()) {
            abort(403, 'Cannot reply to this comment.');
        }

        Validator::make($request->all(), [
            'message' => 'required|string'
        ])->validate();

        $commentClass = Config::get('comments.model');
        $reply = new $commentClass;
        $reply->commenter()->associate(Auth::user());
        $reply->commentable()->associate($comment->commentable);
        $reply->parent()->associate($comment);
        $reply->comment = $request->message;
        $reply->approved = !Config::get('comments.approval_required');
        $reply->save();

        if($reply->approved) {
            session()->flash('success', 'Reply added successfully.');
        } else {
            session()->flash('success', 'Reply submitted for approval successfully.');
        }

        return $reply;
    }
}
