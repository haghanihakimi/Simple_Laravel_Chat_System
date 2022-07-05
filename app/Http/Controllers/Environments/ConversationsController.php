<?php

namespace App\Http\Controllers\Environments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\IndividualConversation;
use App\Models\IndividualMessage;
use App\Http\Resources\ConversationUserResource;
use Carbon\Carbon;

class ConversationsController extends Controller
{
    protected function getConversations (Request $request) {
        $user = User::find(auth()->user()->id);

        $creators = $user->individualConversationCreator()->paginate(25);
        $hosts = $user->individualConversationHost()->paginate(25);
        

        $conversations = $this->fillConversations($creators, $hosts);

        return response()->json([
            'conversations' => array_reverse($conversations)
        ]);
    }

    protected function deleteConversation (Request $request) {
        try {
            $conversation = IndividualConversation::where('public_id', $request->conversation_id)->firstOrFail();
            $sqlMessages = IndividualMessage::where('individual_conversation_id', $conversation->id)->get();
            
            if ($conversation->creator_id === auth()->user()->id || $conversation->host_id === auth()->user()->id) {
                if (is_null($conversation->deleted_by)) {
                    $this->softDeleteMessages($conversation, $sqlMessages);

                    return response()->json([
                        'code' => 200,
                        'message' => "Conversation deleted!"
                    ]);
                }
                $this->hardDelete($conversation, $sqlMessages);
                
                return response()->json([
                    'code' => 200,
                    'message' => "Conversation deleted."
                ]);
            }
                
            return response()->json([
                'code' => 401,
                'message' => "Unauthorized Access!"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function softDeleteMessages ($conversation, $sqlMessages) {
        $conversation->deleted_by = auth()->user()->id;
        $conversation->save();

        foreach ($sqlMessages as $message) {
            if (is_null($message->deleted_by)) {
                $message->deleted_by = auth()->user()->id;
                $message->save();
            }
        }

        return true;
    }

    private function hardDelete ($conversation, $sqlMessages) {
        if ($conversation->deleted_by !== auth()->user()->id) {
            foreach ($sqlMessages as $message) {
                Redis::del('message:id:'.$message->id);
                $message->delete();
            }
    
            $conversation->delete();
    
            return true;
        }
        return false;
    }

    private function fillConversations ($creators, $hosts) {
        $conversations = [];

        foreach ($creators as $creator) {
            $conversations[] = new ConversationUserResource($creator);
        }
        foreach ($hosts as $host) {
            $conversations[] = new ConversationUserResource($host);
        }

        return $conversations;
    }
}
