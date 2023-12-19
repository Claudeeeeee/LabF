<?php
// src/Model/Comment.php

namespace App\Model;

use App\Service\Config;

class Comment
{
    private ?int $id = null;
    private ?int $postId = null;
    private ?string $content = null;

    // Konstruktory, gettery, settery

    public static function fromArray($array): Comment
    {
        $comment = new self();
        $comment->fill($array);

        return $comment;
    }

    public function fill($array): Comment
    {
        if (isset($array['id']) && ! $this->getId()) {
            $this->setId($array['id']);
        }
        if (isset($array['post_id'])) {
            $this->setPostId($array['post_id']);
        }
        if (isset($array['content'])) {
            $this->setContent($array['content']);
        }

        return $this;
    }

    public static function findAll($postId): array
    {
        $pdo = new \PDO(Config::get('db_dsn'), Config::get('db_user'), Config::get('db_pass'));
        $sql = 'SELECT * FROM comment WHERE post_id = :postId';
        $statement = $pdo->prepare($sql);
        $statement->execute(['postId' => $postId]);

        $comments = [];
        $commentsArray = $statement->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($commentsArray as $commentArray) {
            $comments[] = self::fromArray($commentArray);
        }

        return $comments;
    }

    public static function find($id): ?Comment
    {
        $pdo = new \PDO(Config::get('db_dsn'), Config::get('db_user'), Config::get('db_pass'));
        $sql = 'SELECT * FROM comment WHERE id = :id';
        $statement = $pdo->prepare($sql);
        $statement->execute(['id' => $id]);

        $commentArray = $statement->fetch(\PDO::FETCH_ASSOC);
        if (! $commentArray) {
            return null;
        }
        $comment = Comment::fromArray($commentArray);

        return $comment;
    }

    public function save(): void
    {
        $pdo = new \PDO(Config::get('db_dsn'), Config::get('db_user'), Config::get('db_pass'));
        if (! $this->getId()) {
            $sql = "INSERT INTO comment (post_id, content) VALUES (:postId, :content)";
            $statement = $pdo->prepare($sql);
            $statement->execute([
                'postId' => $this->getPostId(),
                'content' => $this->getContent(),
            ]);

            $this->setId($pdo->lastInsertId());
        } else {
            $sql = "UPDATE comment SET post_id = :postId, content = :content WHERE id = :id";
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':postId' => $this->getPostId(),
                ':content' => $this->getContent(),
                ':id' => $this->getId(),
            ]);
        }
    }

    public function delete(): void
    {
        $pdo = new \PDO(Config::get('db_dsn'), Config::get('db_user'), Config::get('db_pass'));
        $sql = "DELETE FROM comment WHERE id = :id";
        $statement = $pdo->prepare($sql);
        $statement->execute([
            ':id' => $this->getId(),
        ]);

        $this->setId(null);
        $this->setPostId(null);
        $this->setContent(null);
    }
}
