<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{

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
            'id'    => $model->id,
            'name'  => $model->name,
            'email' => $model->email
        ];
    }
}
