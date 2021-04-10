<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Serializers\CustomSerializer;
use App\Transformers\CommentTransformer;
use App\Transformers\PostTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PostCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($aId)
    {
        $post = Post::query()->findOrFail($aId);
        return response()->json($this->transform($post->comments));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $aId)
    {
        $rules = [
            'description' => 'required'
        ];

        $this->validate($request, $rules);

        $post = Post::query()->findOrFail($aId);

        $data = $request->all();
        $data['author_id'] = Auth::user()->id;

        $comment = $post->comments()->create($data);

        return response()->json($this->transform($comment), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($aId, $bId)
    {
        $post = Post::query()->findOrFail($aId);
        $comment = Comment::query()->findOrFail($bId);
        $this->check($post, $comment);

        return response()->json($this->transform($comment));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $aId, $bId)
    {
        $post = Post::query()->findOrFail($aId);
        $comment = Comment::query()->findOrFail($bId);
        $this->check($post, $comment);

        if ($request->has('description')) {
            $comment->description = $request->description;
            $comment->save();
        }

        return response()->json($this->transform($comment));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($aId, $bId)
    {
        $post = Post::query()->findOrFail($aId);
        $comment = Comment::query()->findOrFail($bId);
        $this->check($post, $comment);

        $comment->delete();

        return response()->json($this->transform($comment));
    }

    protected function check($post, $comment)
    {
        if ($post->id != $comment->post_id) {
            throw new HttpException(422, 'ความสัมพันธ์ของข้อมูลที่ไม่ถูกต้อง');
        }
    }

    private function transform($data)
    {
        return fractal($data, new CommentTransformer(), new CustomSerializer())->toArray();
    }
}
