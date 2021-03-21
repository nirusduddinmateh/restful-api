<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Traits\APIResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{

    use APIResponse;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $posts = Post::query();

        $posts = $this->filterAndSort($posts);

        return response()->json([
            'data' => $posts->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $rules = [
            'author_id' => 'required',
            'title' => 'required',
            'description' => 'required'
        ];

        $this->validate($request, $rules);

        $post = Post::query()->create($request->all());

        return response()->json([
            'data' => $post
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Post $post)
    {
        return response()->json([
            'data' => $post
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Post $post)
    {
        $post->fill($request->all());

        if (!$post->isDirty()) {
            return response()->json([
                'error' => 'คุณจำเป็นต้องระบุค่าที่แตกต่างเพื่อการปรับปรุงข้อมูล!',
                'code' => 422
            ], 422);
        }

        $post->save();

        return response()->json([
            'data' => $post
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return response()->json([
            'deleted' => true,
            'data' => $post
        ]);
    }
}
