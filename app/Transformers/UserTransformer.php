<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'posts'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $model)
    {
        return [
            'id' => $model->id,
            'email' => $model->email,
            'name' => $model->name,
            'role' => $model->role,
            'created_date' => $model->created_at,
        ];
    }

    /**
     * Include Posts
     *
     * @param User $model
     * @return \League\Fractal\Resource\Collection
     */
    public function includePosts(User $model)
    {
        return $this->collection($model->posts, new PostTransformer(), false);
    }
}
