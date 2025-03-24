<?php


namespace App\Services;
use Illuminate\Support\Facades\DB;
use App\Models\Post;

class PostService
{
    public function createPost($data)
    {
        $post = new Post();
        $post->title = $data['title'];
        $post->content = $data['content'];
        $post->tags = $data['tags'] ?? '';
        $post->image = $data['image'] ?? null;
        $post->date = now()->toDateString();
        $post->save();
        return $post;
    }

    public function getAllPosts($page, $limit, $search = '', $tags = '')
    {
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

        return $query->paginate($limit, ['*'], 'page', $page);
    }

    public function getPostById($id)
    {
        return Post::findOrFail($id);
    }

    public function updatePost($id, $validatedData)
    {
        $post = Post::find($id);
        if (!$post) {
            throw new \Exception("블로그 글을 찾을 수 없습니다.");
        }
        $post->update($validatedData);
        return $post;
    }

    public function deletePost($id)
        {
            DB::beginTransaction();
            try {
                $post = Post::find($id);

                if (!$post) {
                    return false;
                }
                $post->delete();
                DB::commit();
                return true;
            } catch (\Exception $e) {
                DB::rollBack();
                throw new \Exception("게시글 삭제 중 오류 발생: " . $e->getMessage());
            }
        }
}
