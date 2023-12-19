<?php
// src/Controller/CommentController.php

namespace App\Controller;

use App\Model\Comment;
use App\Service\Templating;

class CommentController
{
    public function indexAction(Templating $templating, $postId)
    {
        $comments = Comment::findAll($postId);
        return $templating->render('comment/index.html.twig', ['comments' => $comments]);
    }

    public function showAction(Templating $templating, $id)
    {
        $comment = Comment::find($id);
        return $templating->render('comment/show.html.twig', ['comment' => $comment]);
    }

    public function createAction($postId, $content)
    {
        $comment = new Comment(null, $postId, $content);
        $comment->save();
        // Obsługa sukcesu i przekierowanie
    }

    public function editAction($id, $content)
    {
        $comment = Comment::find($id);
        $comment->setContent($content);
        $comment->save();
        // Obsługa sukcesu i przekierowanie
    }

    public function deleteAction($id)
    {
        $comment = Comment::find($id);
        $comment->delete();
        // Obsługa sukcesu i przekierowanie
    }
}
