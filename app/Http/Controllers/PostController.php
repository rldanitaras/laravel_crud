<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{

    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(Post::class, 'post');
    }

    public function index()
    {
        $posts = Post::with('user')
            ->latest()
            ->paginate(10);

        //return view('posts.index', compact('posts'));

        return response()->json([
            'success' => true,
            'data' => $posts,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request): JsonResponse
    {
        // Validate input
        $validated =  $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

       $post = Post::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Post created successfully",
            'data' => $post,
        ], 201); 
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
       //
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, Post $post): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        $post->update($validated);

        return response()->json([
            'success' => true,
            'message' => "Post updated successfully",
            'data' => $post,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): JsonResponse
    {
        $post->delete();
        
        //return redirect()->route('posts.index')->with('success', 'Post deleted successfully.');
        return response()->json([
            'success' => true,
            'message' => "Post deleted successfully",
            'data' => $post,
        ], 200);
    }
}
