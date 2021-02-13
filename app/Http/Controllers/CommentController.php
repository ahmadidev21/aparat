<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Comment;
use App\Http\Requests\Comment\ListCommentRequest;
use App\Http\Requests\Comment\CreateCommentRequest;

class CommentController extends Controller
{
    public function index(ListCommentRequest $request)
    {
        $comments = Comment::channelCommand(auth('api')->id());
        if($request->has('state')){
            $comments = $comments->where('comments.state', $request->state);
        }

        return $comments->get();
    }

    public function create(CreateCommentRequest $request)
    {
        $user = $request->user();
        $video = Video::findOrFail($request->video_id);

        $comment = $user->comments()->create([
            'video_id'=>$request->video_id,
            'parent_id'=>$request->parent_id,
            'body'=>$request->body,
            'state'=> ($user->id === $video->user_id) ? Comment::STATE_ACCEPTED : Comment::STATE_PENDING
        ]);

        return $comment;
    }

//    public function changeState()
//    {
//
//    }
}
