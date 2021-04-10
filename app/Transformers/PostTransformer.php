<?php

namespace App\Transformers;

use App\Models\Post;
use League\Fractal\TransformerAbstract;

class PostTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'author',
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'author',
        'comments'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Post $model)
    {
        return [
            'id'    => $model->id,
            'author_id' => $model->author_id,
            'title' => $model->title,
            'description' => $model->description,
            'img' => $model->img,
            'created_date' => $model->created_at,
            'updated_date' => $model->updated_at,
        ];
    }

    /**
     * Include Author
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeAuthor(Post $model)
    {
        $author = $model->author;

        return $this->item($author, new UserTransformer(), false);
    }

    /**
     * Include Comments
     *
     * @param Post $model
     * @return \League\Fractal\Resource\Collection
     */
    public function includeComments(Post $model)
    {
        $comments = $model->comments;
        return $this->collection($comments, new CommentTransformer, false);
    }
}
