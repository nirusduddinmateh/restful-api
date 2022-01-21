<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Serializers\CustomSerializer;
use App\Transformers\CommentTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PostCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($postId)
    {
        $post = Post::query()->findOrFail($postId);
        return response()->json($this->transform($post->comments));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, $postId)
    {
        $rules = [
            'description' => 'required'
        ];

        $this->validate($request, $rules);

        $post = Post::query()->findOrFail($postId);

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
    public function show($postId, $commentId)
    {
        $post = Post::query()->findOrFail($postId);
        $comment = Comment::query()->findOrFail($commentId);
        $this->check($post, $comment);

        return response()->json($this->transform($comment));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $postId
     * @param $commentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $postId, $commentId)
    {
        $post = Post::query()->findOrFail($postId);
        $comment = Comment::query()->findOrFail($commentId);
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
     * @param $postId
     * @param $commentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($postId, $commentId)
    {
        $post = Post::query()->findOrFail($postId);
        $comment = Comment::query()->findOrFail($commentId);
        $this->check($post, $comment);

        $comment->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    protected function check($post, $comment)
    {
        if ($post->id != $comment->post_id) {
            throw new HttpException(422, 'กรุณาตรวจสอบความสัมพันธ์ของข้อมูล!');
        }
    }

    private function transform($data)
    {
        return fractal($data, new CommentTransformer(), new CustomSerializer())->toArray();
    }
}
