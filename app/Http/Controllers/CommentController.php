<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Video;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Comment\ListCommentRequest;
use App\Http\Requests\Comment\CreateCommentRequest;
use App\Http\Requests\Comment\DeleteCommentRequest;
use App\Http\Requests\Comment\ChangeStateCommentRequest;

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

    public function changeState(ChangeStateCommentRequest $request)
    {
        $comment = $request->comment;
        $comment->state = $request->state;
        $comment->save();

        return response(['message'=>'وضعیت با موفقیت تغییر یافت.'], Response::HTTP_ACCEPTED);
    }

    public function delete(DeleteCommentRequest $request)
    {
        try {
            DB::transaction();
            $request->comment->delete();
            DB::commit();

            return response(['message'=>'حذف دیدگاه با موفقیت انجام شد.'], Response::HTTP_OK);
        }catch (Exception $exception){
            DB::rollBack();
            Log::error($exception);

            return response(['message'=>'حذف دیدگاه با شکست مواجه شد.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
