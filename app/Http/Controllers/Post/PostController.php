<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Serializers\CustomSerializer;
use App\Transformers\PostTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $posts = Post::query()->with('author')->orderBy('id', 'desc')->paginate();
        return response()->json($this->transform($posts));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $rules = [
            'title'  => 'required',
            'description' => 'required',
            'img' => 'required|image',
        ];

        $this->validate($request, $rules);

        $data = $request->all();
        $data['img'] = $request->img->store('public/posts');
        $data['author_id'] = Auth::user()->id;

        $post = Post::query()->create($data);

        return response()->json($this->transform($post), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Post $post)
    {
        return response()->json($this->transform($post));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Post $post)
    {
        $rules = [
            'img' => 'image',
        ];
        $this->validate($request, $rules);

        $post->fill($request->only([
            'title',
            'description'
        ]));

        if ($request->hasFile('img')) {
            Storage::delete($post->img);
            $post->img = $request->img->store('public/posts');
        }

        if ($post->isClean()) {
            return response()->json([
                'error' => 'คุณจำเป็นต้องระบุค่าที่แตกต่างเพื่อการปรับปรุงข้อมูล!',
                'code'  => 422
            ], 422);
        }

        $post->save();

        return response()->json($this->transform($post));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Post $post)
    {
        $post->delete();
        Storage::delete($post->img);

        return response()->json($this->transform($post));
    }

    private function transform($data)
    {
        return fractal($data, new PostTransformer(), new CustomSerializer())->toArray();
    }
}
