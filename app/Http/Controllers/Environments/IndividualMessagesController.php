<?php

namespace App\Http\Controllers\Environments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\User;
use App\Http\Resources\UsersResource;
use App\Http\Resources\MessagesResource;
use App\Http\Resources\ContactsRelationshipResource;
use App\Models\IndividualMessage;
use App\Models\IndividualConversation;
use App\Models\Notification as Notifications;
use App\Events\IndividualMessagesEvent;
use Carbon\Carbon;

class IndividualMessagesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    
    /**
     * Fetches & displays view of the page
     *
     * @return mixed
     */
    protected function index ($id, Request $request) {
        $user = User::find(auth()->user()->id);
        
        $this->validateGet($request);
        $target = User::where('username', $request->id)->firstOrFail();

        $settings = json_decode(Redis::get('profiles:username:'.$user->uid));
        $username = $target->username;
        $beingBlocked = $user->checkBeingBlocked($target);
        $hasBlocked = $user->checkHasBlocked($target);

        $target = $this->fetchReceiverInfo($target);

        return view('layouts.messages', compact(
            'user',
            'username',
            'beingBlocked',
            'hasBlocked',
            'target',
            'settings'
        ));
    }
    
    /**
     * Fetches all send/received messages. It fetches up to 50 rows
     *
     * @return mixed
     */
    public function fetchMessages ($user, Request $request) {
        $this->validateGet($request); // Validate for received request paramater which is Username

        $target = User::where('username', $request->user)->firstOrFail();

        $messages = IndividualMessage::select('*')->Message($target)->limit(50)->orderBy('created_at', 'DESC')->get();
        
        $chats = [];
        $sent = false;

        foreach ($messages as $message) {
            if ($message->deleted_by !== auth()->user()->id) {
                $temp = json_decode(Redis::get('message:id:'.$message->id));
                $sent = ($message->sender_id === auth()->user()->id) ? true : false;
                
                $chats[] = [
                    "sent" => $sent,
                    "message_id" => $temp->message_id,
                    "message" => Crypt::decryptString($temp->message),
                    "timestamp" => Carbon::parse($temp->timestamp)->format("D, d M 'y - h:m A"),
                ];
            }
        }

        return response()->json([
            "user" => new UsersResource($target),
            "messageable" => auth()->user()->checkMessageable($target),
            "interacts" => (new ContactsRelationshipResource(auth()->user()))->target($target),
            "destination" => $target->public_uid,
            'messages' => $chats,
        ]);
    }
    
    /**
     * Fetches current sent messages which is last row
     *
     * @return mixed
     */
    public function fetchRecentMessage ($user, Request $request) {
        $this->validateGet($request); // Validate for received request paramater which is Username

        $target = User::where('username', $request->user)->firstOrFail();

        $messages = IndividualMessage::select('*')->Message($target)->orderBy('created_at', 'DESC')->first();
        $chats = [];

        $temp = json_decode(Redis::get('message:id:'.$messages->id));
        $sent = ($messages->sender_id === auth()->user()->id) ? true : false;
        
        $chats = [
            "sent" => $sent,
            "message_id" => $temp->message_id,
            "message" => Crypt::decryptString($temp->message),
            "timestamp" => Carbon::parse($temp->timestamp)->format("D, d M 'y - h:m A"),
        ];

        return response()->json([
            "user" => new UsersResource($target),
            "messageable" => auth()->user()->checkMessageable($target),
            "blockStatus" => auth()->user()->checkBlockable($target),
            "addable" => auth()->user()->checkAddable($target),
            "cancellableReq" => auth()->user()->checkCancellable($target),
            "remove" => auth()->user()->checkContactRemovable($target),
            "destination" => $target->public_uid,
            'messages' => $chats,
        ]);
    }
    
    /**
     * Handles sending messages. Stores all messages to database and redis
     *
     * @return void
     */
    protected function sendMessage ($id, Request $request) {
        $this->validateMessage($request);

        $user = User::find(auth()->user()->id);
        $target = User::where('public_uid', $request->user)->firstOrFail();

        if ($user->checkMessageable($target)) {
            $conversation = [];
            if ($user->individualConversationsCheck($target)) {
                $conversation = $this->fetchConversation($user, $target);
                $conversation->deleted_by = null;
                $conversation->save();
            } else {
                $conversation = $this->createConversation($user, $target);
            }

            $message = $this->storeMessageMySql($user, $target, $conversation);
            
            $this->storeMessageRedis ($message, $conversation, $request);
            
            event(new IndividualMessagesEvent( $target, auth()->user(), $message) );
            return response()->json($this->storeOutputMessage ($target, $request, $message));
        }
        
        return response()->json(["code" => 500, "failed", "token" => csrf_token()]);
    }
    
    /**
     * Validate received inputs from client side
     *
     * @return void
     */
    private function validateMessage ($request) {
        return $this->validate($request, [
            'user' => ['nullable', 'string', "regex:/^[a-zA-Z0-9_]+$/u"],
            'message' => ['nullable', 'string', 'min:1'],
        ]);
    }
    
    /**
     * Validates received parameter from REQUEST
     *
     * @return void
     */
    private function validateGet ($request) {
        return $this->validate($request, [
            'id' => ['nullable', 'string', "regex:/^[a-zA-Z0-9_]+$/u"],
            'user' => ['nullable', 'string', "regex:/^[a-zA-Z0-9_]+$/u"],
        ]);
    }
    
    /**
     * Fetches and prepares receiver user's information
     *
     * @return void
     */
    private function fetchReceiverInfo ($target) {
        return (object) collect([
            "id" => $target->public_uid,
            "first_name" => $target->fname,
            "surname" => $target->sname,
            "privacy" => $target->is_locked,
            "gender" => $target->gender
        ])->all();
    }
    
    /**
     * If there is any related conversation; it fetches the conversation
     *
     * @return void
     */
    private function fetchConversation ($user, $target) {
        return IndividualConversation::where('creator_id', $user->id)
        ->where('host_id', $target->id)
        ->orWhere('host_id', $user->id)
        ->where('creator_id', $target->id)->latest('created_at')->first();
    }
    
    /**
     * If there is NOT related conversation; it creates new conversation
     *
     * @return void
     */
    private function createConversation ($user, $target) {
        return IndividualConversation::firstOrCreate([
            'public_id' => sha1(Str::uuid()->toString()),
            'creator_id' => $user->id,
            'host_id' => $target->id,
            'updated_at' => now(),
            'created_at' => now()
        ]);
    }
    
    /**
     * Stores data for current message in SQL database
     *
     * @return void
     */
    private function storeMessageMySql ($user, $target, $conversation) {
        return IndividualMessage::create([
            'public_id' => sha1(Str::uuid()->toString()),
            'individual_conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'receiver_id' => $target->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
    
    /**
     * Stores current message data such as message contents in Redis db (database number 0)
     *
     * @return void
     */
    private function storeMessageRedis ($message, $conversation, $request) {
        return Redis::set('message:id:'.$message->id, 
            json_encode([
                "conversation_id" => $conversation->id,
                "message_id" => $message->public_id,
                "message" => Crypt::encryptString($request->message),
                "timestamp" => $message->created_at,
            ])
        );
    }
    
    /**
     * Returns array of all related ouputs (sent message, user data)
     *
     * @return array
     */
    private function storeOutputMessage ($target, $request, $message) {
        return [
            "code" => 200,
            "user" => new UsersResource($target),
            "messages" => [
                "sent" => true,
                "message_id" => $message->public_id,
                "message" => $request->message,
                "timestamp" => Carbon::parse($message->created_at)->format("D, d M 'y - h:m A"),
            ]
        ];
    }
}
