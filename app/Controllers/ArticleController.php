<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Collections\ArticleCollection;
use App\Models\Article;
use App\RedirectResponse;
use App\Response;
use App\ViewResponse;
use Carbon\Carbon;

class ArticleController extends BaseController
{
    public function index():Response
    {
        $articles = $this->database->createQueryBuilder()
            ->select('*')
            ->from('articles')
            ->fetchAllAssociative();
        $articleCollection = new ArticleCollection();
        foreach ($articles as $article)
        {
            $articleCollection->add(new Article(
                $article['title'],
                $article ['description'],
                $article ['picture'],
                $article ['created_at'],
                (int)$article ['id'],
                $article ['updated_at']
            ));
        }
        return new ViewResponse('articles/index',[
            'articles' => $articleCollection
        ]);
    }
    public function show(int $id):Response
    {
        $data = $this->database->createQueryBuilder()
            ->select('*')
            ->from('articles')
            ->where('id=:id')
            ->setParameter('id',$id)
            ->fetchAssociative();

        $updatedAt = $data['updated_at'] ?? '';

        $article = new Article(
            $data['title'],
            $data['description'],
            $data['picture'],
            $data['created_at'],
            (int)$data['id'],
            $updatedAt
        );
        return new ViewResponse('articles/show', [
            'article' => $article, ['id'=>$id]
        ]);
    }
    public function create():Response
    {
        return new ViewResponse('articles/create');
    }
    public function store() :Response
    {
        $this->database->createQueryBuilder()
            ->insert('articles')
            ->values(
                [
                    'title'=> ':title',
                    'description' => ':description',
                    'picture'=> ':picture',
                    'created_at' => ':created_at'
                ]
            )->setParameters([
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'picture'=>'https://via.assests.so/img.jpg?w=500&h=500',
                'created_at'=>Carbon::now()
            ])->executeQuery();

        return new RedirectResponse('/articles');
    }
    public function edit(int $id):Response
    {
        $data = $this->database->createQueryBuilder()
            ->select('*')
            ->from('articles')
            ->where('id=:id')
            ->setParameter('id', $id)
            ->fetchAssociative();

        if ($data === false) {
            return new RedirectResponse('/articles');
        }

        $title = $data['title'] ?? '';
        $description = $data['description'] ?? '';
        $picture = $data['picture'] ?? '';
        $createdAt = $data['created_at'] ?? '';
        $id = (int)($data['id'] ?? 0);
        $updatedAt = $data['updated_at'] ?? '';

        $article = new Article(
            $title,
            $description,
            $picture,
            $createdAt,
            $id,
            $updatedAt
        );

        return new ViewResponse('articles/edit', [
            'article' => $article
        ]);
    }
    public function update(int $id): Response
    {
        $this->database->createQueryBuilder()
            ->update('articles')
            ->set('title',':title')
            ->set('description',':description')
            ->where('id', ':id')
            ->setParameters([
                'id'=> $id,
                'title' => $_POST['title'],
                'description' => $_POST['description'],
            ])->executeQuery();

        return new RedirectResponse('/articles/'.$id);
    }
    public function delete(int $id):Response
    {
         $this->database->createQueryBuilder()
            ->delete('articles')
            ->where('id=:id')
            ->setParameter('id', $id)
            ->executeQuery();

        return new RedirectResponse('/articles');
    }
}