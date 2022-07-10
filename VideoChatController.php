<?php

namespace App\Domains\VideoChat\Controllers;

use App\Domains\VideoChat\Filters\VideoChatsFilter;
use App\Domains\VideoChat\Requests\ChatsFilterRequest;
use App\Domains\VideoChat\Requests\VideoChatsFilterRequest;
use App\Domains\VideoChat\Requests\VideoChatsRequest;
use App\Domains\VideoChat\Services\VideoChatServices;
use App\Domains\VideoChat\Transformers\VideoChatTransformer;
use App\Http\Controllers\Controller;
use Dingo\Api\Http\Response;
use Illuminate\Support\Facades\Auth;

class VideoChatController extends Controller
{
    /**
     * @api            {GET} /api/VideoChats 1. Get Chats
     * @apiName        GetVideoChats
     * @apiGroup       Chat
     * @apiDescription Get video Chats
     * @apiPermission  User
     *
     * @apiHeader {String} Content-Type=application/json
     *
     * @apiUse         ChatsFilterRequest
     * @apiUse         ChatTransformer
     *
     * @apiUse         Authorization401
     */
    public function index(
        VideoChatsFilterRequest $request,
        VideoChatsFilter $filter,
        VideoChatServices $videoChatServices
    ): Response {

        $chats = $videoChatServices->paginate($filter, $request->validated());

        return $this->response->paginator($chats, new VideoChatTransformer());
    }

    /**
     * @api            {POST} /api/VideoChats 2. Create Video Chat
     * @apiName        VideoChatCreate
     * @apiGroup       Chat
     * @apiDescription Create new video chat
     * @apiPermission  User
     *
     * @apiHeader {String} Content-Type=application/json
     * @apiHeader {String} Authorization  Bearer{token}
     *
     * @apiUse         DirectChatRequest
     * @apiUse         DirectChatTransformer
     *
     * @apiUse         Authorization401
     * @apiUse         BadRequest400
     * @apiUse         Forbidden403
     */
    public function create(
        VideoChatsRequest $request,
        VideoChatServices $videoChatServices
    ) {
        $event_id = $request->event_id;
        $event = Event::find($event_id);
        $videoChat = $videoChatServices->createVideoChat(
            $event,
            Auth::user(),
            $request->validated()
        );

        return $this->response->item($videoChat, new VideoChatTransformer());
    }

}
