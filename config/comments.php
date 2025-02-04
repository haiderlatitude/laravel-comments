<?php

return [

    /**
     * To extend the base Comment model one just needs to create a new
     * CustomComment model extending the Comment model shipped with the
     * package and change this configuration option to their extended model.
     */
    'model' => \Haider\Comments\Comment::class,

    /**
     * You can customize the behaviour of these permissions by
     * creating your own and pointing to it here.
     */
    'permissions' => [
        'create-comment' => 'Haider\Comments\CommentPolicy@create',
        'delete-comment' => 'Haider\Comments\CommentPolicy@delete',
        'edit-comment' => 'Haider\Comments\CommentPolicy@update',
        'reply-to-comment' => 'Haider\Comments\CommentPolicy@reply',
    ],

    /**
     * The Comment Controller.
     * Change this to your own implementation of the CommentController.
     * You can use the \Haider\Comments\CommentControllerInterface
     * or extend the \Haider\Comments\CommentController.
     */
    'controller' => '\Haider\Comments\WebCommentController',

    /**
     * Disable/enable the package routes.
     * If you want to completely take over the way this package handles
     * routes and controller logic, set this to false and provide your
     * own routes and controller for comments.
     */
    'routes' => true,

    /**
     * By default comments posted are marked as approved. If you want
     * to change this, set this option to true. Then, all comments
     * will need to be approved by setting the `approved` column to
     * `true` for each comment.
     *
     * To see only approved comments use this code in your view:
     *
     *     @comments([
     *         'model' => $book,
     *         'approved' => true
     *     ])
     *
     */
    'approval_required' => true,

    /**
     * Set this option to `true` to enable guest commenting.
     *
     * Visitors will be asked to provide their name and email
     * address in order to post a comment.
     */
    'guest_commenting' => true,

    /**
     * Set this option to `true` to enable soft deleting of comments.
     *
     * Comments will be soft deleted using laravels "softDeletes" trait.
     */
    'soft_deletes' => false,

    /**
     * Enable/disable the package provider to load migrations.
     * This option might be useful if you use multiple database connections.
     */
    'load_migrations' => true,

    /**
     * Enable/disable calling Paginator::useBootstrap() in the boot method
     * to prevent breaking non bootstrap based Site.
     */
    'paginator_use_bootstrap' => false,

    /**
     * Enable/disable commenting functionality on a model.
     */
    'can_reply' => false,

    /**
     * Enable/disable comment editing functionality.
     */
    'can_edit' => false,

    /**
     * Enable/disable comment deletion functionality.
     */
    'can_delete' => false,

];
