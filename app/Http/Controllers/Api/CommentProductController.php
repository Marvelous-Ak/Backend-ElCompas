<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\CommentProduct;

class CommentProductController extends Controller
{
    public function index()
    {
        $comments = CommentProduct::all();
        return view('comments.index', compact('comments'));
    }

    public function show($id)
    {
        $comment = CommentProduct::find($id);
        return view('comments.show', compact('comment'));
    }
}