<?php


namespace App\Services;

use App\Models\Post;

class PostService
{
    public function createPost($data)
    {
        $post = new Post();
        $post->title = $data['title'];
        $post->content = $data['content'];
        $post->tags = json_encode($data['tags'] ?? []);
        $post->image_url = $data['image'] ?? null;
        $post->date = now();
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

    public function updatePost($id, $data)
    {
        $post = Post::findOrFail($id);
        $post->title = $data['title'];
        $post->content = $data['content'];
        $post->tags = $data['tags'];
        $post->image_url = $data['image'] ?? $post->image_url;
        $post->save();
        return $post;
    }

    public function deletePost($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();
    }
}
