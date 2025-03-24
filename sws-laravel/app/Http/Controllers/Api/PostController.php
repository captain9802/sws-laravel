<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Services\PostService;
use App\Services\FileService;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    protected $postService;
    protected $fileService;

    public function __construct(PostService $postService, FileService  $fileService)
    {
        $this->postService = $postService;
        $this->fileService = $fileService;
    }

    public function create(PostRequest $request)
    {
        $validated = $request->validated();

        if (isset($validated['content'])) {
            $validated['content'] = $this->processImagesInContent($validated['content']);
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $this->fileService->uploadFile($request->file('image'));
        }
        $post = $this->postService->createPost($validated);

        return response()->json([
            'status' => 'success',
            'message' => '새 블로그 글이 성공적으로 작성되었습니다.',
            'data' => $post
        ], 201);
    }

    public function processImagesInContent($content)
    {
        preg_match_all('/<img.*?src=["\'](data:image\/(.*?);base64,([^"\']*?))["\'].*?>/', $content, $matches);

        foreach ($matches[3] as $imageBase64) {
            $imageData = base64_decode($imageBase64);
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_buffer($finfo, $imageData);
            finfo_close($finfo);

            $extension = 'jpg';
            if ($mimeType === 'image/png') {
                $extension = 'png';
            } elseif ($mimeType === 'image/jpeg') {
                $extension = 'jpg';
            } elseif ($mimeType === 'image/gif') {
                $extension = 'gif';
            }

            $uploadedImageUrl = $this->fileService->uploadBase64Image($imageBase64, $extension);

            $content = str_replace(
                $matches[0][array_search($imageBase64, $matches[3])],
                '<img src="' . $uploadedImageUrl . '">',
                $content
            );
        }

        return $content;
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

        try {
            $post = $this->postService->getPostById($id);

            $existingImages = $this->extractImageUrls($post->content);
            $updatedImages = $this->extractImageUrls($validated['content'] ?? '');

            $imagesToDelete = array_diff($existingImages, $updatedImages);
            foreach ($imagesToDelete as $imageUrl) {
                $this->fileService->deleteFile($imageUrl);
            }

            if (isset($validated['content'])) {
                $validated['content'] = $this->processImagesInContent($validated['content'], $existingImages);
            }

            if ($request->hasFile('image')) {
                $this->fileService->deleteFile($post->image);
                $validated['image'] = $this->fileService->uploadFile($request->file('image'));
            }

            $updatedPost = $this->postService->updatePost($id, $validated);

            return response()->json([
                'status' => 'success',
                'message' => '블로그 글이 성공적으로 업데이트되었습니다.',
                'data' => $updatedPost
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => '블로그 글 업데이트에 실패했습니다.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $post = $this->postService->getPostById($id);

            if (!$post) {
                return response()->json([
                    'status' => 'error',
                    'message' => '해당 블로그 글을 찾을 수 없습니다.'
                ], 404);
            }

            $existingImages = $this->extractImageUrls($post->content);

            foreach ($existingImages as $imageUrl) {
                $this->fileService->deleteFile($imageUrl);
            }
            $this->fileService->deleteFile($post->image);
            $deleted = $this->postService->deletePost($id);
            if (!$deleted) {
                return response()->json([
                    'status' => 'error',
                    'message' => '블로그 글 삭제에 실패했습니다.'
                ], 500);
            }

            return response()->json([
                'status' => 'success',
                'message' => '블로그 글이 성공적으로 삭제되었습니다.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => '블로그 글 삭제 중 오류가 발생했습니다.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function extractImageUrls($content)
    {
        preg_match_all('/<img[^>]+src="(https:\/\/[^"]+\.(jpg|png|jpeg|gif))"/', $content, $matches);
        preg_match_all('/https:\/\/.*\.(jpg|png|jpeg|gif)/', $content, $matches2);
        return array_merge($matches[1], $matches2[0]);
    }
}
