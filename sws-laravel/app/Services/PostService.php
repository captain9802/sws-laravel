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
        $post->image_url = $data['image'] ?? null;
        $post->date = now();
        $post->save();
        return $post;
    }

    public function getAllPosts()
    {
        return Post::all();
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
