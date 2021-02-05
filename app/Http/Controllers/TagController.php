<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Requests\Tag\CreateTagRequest;

class TagController extends Controller
{
    public function index()
    {
        return Tag::all();
    }

    public function create(CreateTagRequest $request)
    {
        return Tag::create($request->validated());
    }
}
