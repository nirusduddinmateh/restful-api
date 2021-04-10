<?php

namespace App\Transformers;

use App\Models\Comment;
use League\Fractal\TransformerAbstract;

class CommentTransformer extends TransformerAbstract
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
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Comment $model)
    {
        return [
            'id'    => $model->id,
            'description'  => $model->description
        ];
    }

    /**
     * Include Author
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeAuthor(Comment $model)
    {
        if ($author = $model->author) {
            return $this->item($author, new UserTransformer(), false);
        }

        return null;
    }
}
