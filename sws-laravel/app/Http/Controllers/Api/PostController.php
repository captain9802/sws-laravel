<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Services\PostService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function create(PostRequest $request)
    {
        $validated = $request->validated();
        $post = $this->postService->createPost($validated);
        return response()->json([
            'status' => 'success',
            'message' => '새 블로그 글이 성공적으로 작성되었습니다.',
            'data' => $post
        ], 201);
    }

    public function index()
    {
        $posts = $this->postService->getAllPosts();
        return response()->json($posts);
    }

    public function show($id)
    {
        $post = $this->postService->getPostById($id);
        return response()->json($post);
    }

    public function update(PostRequest $request, $id)
    {
        $validated = $request->validated();
        $post = $this->postService->updatePost($id, $validated);
        return response()->json([
            'status' => 'success',
            'message' => '블로그 글이 성공적으로 업데이트되었습니다.',
            'data' => $post
        ]);
    }

    public function destroy($id)
    {
        $this->postService->deletePost($id);
        return response()->json([
            'status' => 'success',
            'message' => '블로그 글이 성공적으로 삭제되었습니다.'
        ]);
    }
}
