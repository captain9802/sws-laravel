<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Services\PostService;
use Illuminate\Http\Request;
use App\Models\Post;

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

    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 12);
        $search = $request->input('search', '');
        $tags = $request->input('tags', '');

        $query = Post::query();

        if (!empty($search)) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        if (!empty($tags)) {
            $tagsArray = explode(',', $tags);

            foreach ($tagsArray as $tag) {
                $query->whereJsonContains('tags', $tag);
            }
        }

        $posts = $query->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'blogs' => $posts->items(),
            'totalCount' => $posts->total()
        ]);
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
