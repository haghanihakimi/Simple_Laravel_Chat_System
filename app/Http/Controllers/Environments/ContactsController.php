<?php
namespace App\Http\Controllers\Environments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
Use App\Jobs\ContactRequestJob;
use App\Models\User;
use App\Models\Block;
use App\Models\Contact;
use App\Models\Notification as NotificationModel;
use App\Events\ContactRequests AS ContactRequestEvent;
use App\Events\CancelContactRequestEvent;
use App\Events\RejectContactRequestEvent;
use App\Events\AcceptContactRequestEvent;
use App\Events\ContactRemoveEvent;
use App\Events\ContactBlockEvent;
use App\Events\ContactMarkAsSpamEvent;
use App\Events\ContactSpamUnmarkEvent;
use App\Events\CountPendingRequestsEvent;
use App\Http\Resources\ContactsResource;
use App\Http\Resources\ContactsRelationshipResource;

class ContactsController extends Controller
{
    public function __construct (){
        $this->middleware(['auth', 'verified']);
    }
    
    /**
     * Fetches and lists all current user's contacts
     *
     * @return array
     */
    protected function listContacts (Request $request) {
        $user = User::find(auth()->user()->id);

        $inputs = $user->contacts();

        $contacts = [];

        if (count($inputs) > 0) {
            foreach ($inputs as $contact){
                $contacts[] = new ContactsResource($contact);
            }
        }
        
        return response()->json([
            "code" => 200,
            "contacts" => $contacts,
        ]);
    }
    
    /**
     * Fetches and lists all current user's received contact requests
     *
     * @return array
     */
    protected function receivedPendingContacts () {
        $user = User::find(auth()->user()->id);
        
        $inputs = $user->receivedPendingRequests();
        $pendings = [];

        if (count($inputs) > 0) {
            foreach ($inputs as $pending) {
                $pendings[] = new ContactsResource($pending);
            }
        }
        
        return response()->json([
            "code" => 200,
            'pendings' => $pendings
        ]);
    }
    
    /**
     * Fetches and lists all contact requests which current user has sent
     *
     * @return array
     */
    protected function sentPendingContacts () {
        $user = User::find(auth()->user()->id);
        
        $inputs = $user->sentPendingRequests();
        $pendings = [];

        if (count($inputs) > 0) {
            foreach ($inputs as $pending) {
                $pendings[] = new ContactsResource($pending);
            }
        }
        
        return response()->json([
            "code" => 200,
            'pendings' => $pendings
        ]);
    }
    
    /**
     * Handles sending contact requests to the target user
     *
     * @return void,array
     */
    protected function sendRequest ($id, Request $request) {
        session()->regenerate(); //Renew session ID and CSRF-Token

        $this->validation($request); //Check received user id for any possible vulnerability
        $user = User::where('public_uid', $request->id)->firstOrFail();
        
        if ( auth()->user()->checkAddable($user) && 
            !Redis::exists('contact.request.user.'.auth()->user()->id.'.target.'.$user->id)
        ) {
            $sendRequest = $this->storeRequest($user);
            
            if ($sendRequest) {
                Redis::set('contact.request.user.'.auth()->user()->id.'.target.'.$user->id, true); 
                
                $requests = count(Contact::where('second_user', $user->id)->where('status', 'pending')->limit(100)->get());

                event( new CountPendingRequestsEvent( $user, $requests ) );
                event( new ContactRequestEvent( $user, auth()->user()) );

                ContactRequestJob::dispatch ($user, auth()->user())->delay(now()->addMinutes(15));

            }
        }
        return response()->json([
            'code' => 200, 
            'interact' => (new ContactsRelationshipResource(auth()->user()))->target($user),
            'token' => csrf_token()
        ]);
    }
    
    /**
     * Handles cancellation of contact request is sent to the target user
     *
     * @return void,array
     */
    protected function cancelRequest ($id, Request $request) {
        $this->validation($request);
        $target = User::where('public_uid', $request->id)->firstOrFail();

        try {
            if (
                Redis::exists('contact.request.user.'.auth()->user()->id.'.target.'.$target->id) &&
                auth()->user()->checkCancellable($target)
            ) {
                $this->cancelContactRequest(auth()->user(), $target);

                Redis::del('contact.request.user.'.auth()->user()->id.'.target.'.$target->id);
                
                $requests = count(Contact::where('second_user', $target->id)->where('status', 'pending')->limit(100)->get());

                event( new CountPendingRequestsEvent( $target, $requests ) );
                event( new CancelContactRequestEvent($target, auth()->user()) );
            }
            return response()->json([
                'code' => 200, 
                'interact' => (new ContactsRelationshipResource(auth()->user()))->target($target),
            ]); //Return successful message
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500, 
                'message' => $e->getMessage(), 
            ]); //Return error message
        }
    }
    
    /**
     * Handles cancellation of contact request is sent to the target user
     *
     * @return void,array
     */
    protected function rejectRequest ($id, Request $request) {
        $this->validation($request);
        $target = User::where('public_uid', $request->id)->firstOrFail();
        
        try {
            if (
                Redis::exists('contact.request.user.'.$target->id.'.target.'.auth()->user()->id) &&
                auth()->user()->checkRejectable($target)
            ) {
                $this->rejectContactRequest(auth()->user(), $target);

                Redis::del('contact.request.user.'.$target->id.'.target.'.auth()->user()->id);

                $requests = count(Contact::where('second_user', $target->id)->where('status', 'pending')->limit(100)->get());

                event( new CountPendingRequestsEvent( $target, $requests ) );
                event( new RejectContactRequestEvent($target, auth()->user()) );
                
            }
            return response()->json([
                'code' => 200, 
                'message' => '', 
                'interact' => (new ContactsRelationshipResource(auth()->user()))->target($target),
            ]); //Return message
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500, 
                'message' => $e->getMessage(), 
                'interact' => (new ContactsRelationshipResource(auth()->user()))->target($target),
            ]); //Return error message
        }
    }
    
    /**
     * Handles acceptation of contact request is received from other users
     *
     * @return void,array
     */
    protected function acceptRequest ($id, Request $request) {
        $this->validation($request);
        $target = User::where('public_uid', $request->id)->firstOrFail();

        try {
            if (
                Redis::exists('contact.request.user.'.$target->id.'.target.'.auth()->user()->id) &&
                auth()->user()->checkRejectable($target)
            ) {
                $this->acceptContactRequest(auth()->user(), $target);
                Redis::del('contact.request.user.'.$target->id.'.target.'.auth()->user()->id);

                $requests = count(Contact::where('second_user', $target->id)->where('status', 'pending')->limit(100)->get());

                event( new CountPendingRequestsEvent( $target, $requests ) );
                event( new AcceptContactRequestEvent($target, auth()->user()) );
            }
            return response()->json([
                'code' => 200, 
                'interact' => (new ContactsRelationshipResource(auth()->user()))->target($target),
            ]); //Return unsuccessful message
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500, 
                'message' => $e->getMessage(), 
                'interact' => (new ContactsRelationshipResource(auth()->user()))->target($target),
            ]); //Return error message
        }
    }
    
    /**
     * Handles removing process of contact who is in current user's contacts list
     *
     * @return void,array
     */
    protected function removeContact ($id, Request $request) {
        $this->validation($request);
        $target = User::where('public_uid', $request->id)->firstOrFail();

        try {
            if ( auth()->user()->checkContactRemovable($target) ) {
                $this->delMyContact(auth()->user(), $target);

                event( new ContactRemoveEvent($target, auth()->user()) );
            }
            return response()->json([
                'code' => 200, 
                'interact' => (new ContactsRelationshipResource(auth()->user()))->target($target),
            ]); //Return unsuccessful message
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500, 
                'message' => $e->getMessage(), 
                'interact' => (new ContactsRelationshipResource(auth()->user()))->target($target),
            ]); //Return error message
        }
    }
    
    /**
     * Runs process of Blocking any user who is targeted by the current user.
     *
     * @return void,array
     */
    protected function blockContact ($id, Request $request) {
        $this->validation($request);
        $target = User::where('public_uid', $request->id)->firstOrFail();

        try {
            if ( auth()->user()->checkBlockable($target) ) {
                $block = $this->storeBlockContact(auth()->user(), $target);

                if ($block) {
                    if (auth()->user()->checkAcceptable($target)) {
                        $this->rejectContactRequest(auth()->user(), $target);
                    }
                    if (auth()->user()->checkContactRemovable($target)) {
                        $this->delMyContact(auth()->user(), $target);
                    }
                    if (auth()->user()->checkCancellable($target)) {
                        $this->cancelContactRequest(auth()->user(), $target);
                    }

                    Redis::del('contact.request.user.'.$target->id.'.target.'.auth()->user()->id);
                    Redis::del('contact.request.user.'.auth()->user()->id.'.target.'.$target->id);

                    $requests = count(Contact::where('second_user', $target->id)->where('status', 'pending')->limit(100)->get());

                    event( new CountPendingRequestsEvent( $target, $requests ) );    
                    event( new ContactBlockEvent($target, auth()->user()) );
                }
            }

            return response()->json([
                'code' => 200, 
                'interact' => (new ContactsRelationshipResource(auth()->user()))->target($target),                
            ]); //Return message
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500, 
                'message' => $e->getMessage(), 
                'interact' => (new ContactsRelationshipResource(auth()->user()))->target($target),
            ]); //Return error message
        }
    }
    
    /**
     * Runs process of Unblocking any user who currently in current user's block list.
     *
     * @return void,array
     */
    protected function unBlockContact ($id, Request $request) {
        $this->validation($request);
        $target = User::where('public_uid', $request->id)->firstOrFail();

        try {
            if ( !auth()->user()->checkBlockable($target) ) {
                $block = $this->delUnblockContact(auth()->user(), $target);

                if ($block) {
                    Redis::del('contact.block.target.'.$target->uid.'.user.'.auth()->user()->uid);
                    
                    event( new ContactBlockEvent($target, auth()->user()) );
                }
                
            }

            return response()->json([
                'code' => 200, 
                'message' => 'Contact is not blocked.',
                'interact' => (new ContactsRelationshipResource(auth()->user()))->target($target),
            ]); //Return unsuccessful message
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500, 
                'message' => $e->getMessage(), 
                'interact' => (new ContactsRelationshipResource(auth()->user()))->target($target),
            ]); //Return error message
        }
    }
    
    /**
     * Marks targeted user as spam. 
     * This feature is available for current user.
     * This feature is available when current user has received contact request from target user (other users)
     * This feature doesn't block target user.
     * This feature cancels received contact request and stops target user from sending any more contact requests!
     *
     * @return void,array
     */
    protected function markedContactAsSpam ($id, Request $request) {
        $this->validation($request);
        $target = User::where('public_uid', $request->id)->firstOrFail();

        try {
            if ( auth()->user()->checkSpamMarkable($target) ) {
                $this->markAsSpam(auth()->user(), $target);

                Redis::del('contact.request.user.'.$target->id.'.target.'.auth()->user()->id);

                $requests = count(Contact::where('second_user', $target->id)->where('status', 'pending')->limit(100)->get());

                event( new CountPendingRequestsEvent( $target, $requests ) );                
                event( new ContactMarkAsSpamEvent($target, auth()->user()) );
                
            }

            return response()->json([
                'code' => 200, 
                'message' => '',
                'interact' => (new ContactsRelationshipResource(auth()->user()))->target($target),
            ]); //Return unsuccessful message
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500, 
                'message' => $e->getMessage(), 
                'interact' => (new ContactsRelationshipResource(auth()->user()))->target($target),
            ]); //Return error message
        }
    }
    
    /**
     * It Unmarks spammed user as NOT SPAMMED.
     * It gives back "send contact request" ability to UNMARKED SPAM user
     *
     * @return void,array
     */
    protected function contactUnmarkedSpam ($id, Request $request) {
        $this->validation($request);
        $target = User::where('public_uid', $request->id)->firstOrFail();

        try {
            if ( auth()->user()->checkHasSpammed($target) ) {
                $this->unSpamContact(auth()->user(), $target);
                
                event( new ContactSpamUnmarkEvent($target, auth()->user()) );
                
                return response()->json([
                    'code' => 200, 
                    'interact' => (new ContactsRelationshipResource(auth()->user()))->target($target),
                ]); //Return message
                
            }

            return response()->json([
                'code' => 200, 
                'interact' => (new ContactsRelationshipResource(auth()->user()))->target($target),
            ]); //Return message
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500, 
                'message' => $e->getMessage(), 
                'interact' => (new ContactsRelationshipResource(auth()->user()))->target($target),
                'userAddable' => auth()->user()->checkAddable($target),
                'contactUnmarkableSpam' => auth()->user()->checkHasSpammed($target),
                'contactMarkable' => auth()->user()->checkSpamMarkable($target),
            ]); //Return error message
        }
    }
    
    /**
     * Processes and validate all received inputs from client side
     *
     * @return void,array
     */
    private function validation ($input) {
        return $this->validate($input, [
            'id' => ['number', 'min:1', 'max:11', 'regex:/(^([0-9]+)(\d+)?$)/u'],
        ]);
    }
    
    /**
     * Adds new row to Contacts database to add new contact request
     * If a request is previously sent it just updates status to "Pending" 
     *
     * @return void,array
     */
    private function storeRequest ($target) {
        return Contact::updateOrCreate(
            ['first_user' => auth()->user()->id, 'second_user' => $target->id],
            [
                'public_id' => sha1(Str::uuid()->toString()),
                'status' => 'pending',
                'updated_at' => now()
            ]
        );
    }
    
    /**
     * Cancels received contact request and updates contact request "Pending" column to "Cancelled" in Contacts database
     *
     * @return void,array
     */
    private function cancelContactRequest ($user, $target) {
        $cancellableRequest = Contact::where('first_user', $user->id)
        ->where('second_user', $target->id)
        ->firstOrFail();
        $cancellableRequest->status = 3;

        return $cancellableRequest->save();
    }
    
    /**
     * Rejects received contact request by cancelling it, and updates contact request "Pending" column to "Rejected" in Contacts database
     *
     * @return void,array
     */
    private function rejectContactRequest ($user, $target) {
        $rejectableRequest = Contact::where('first_user', $target->id)
        ->where('second_user', $user->id)
        ->firstOrFail();
        $rejectableRequest->status = 4;

        return $rejectableRequest->save();
    }
    
    /**
     * Cancels received contact request and updates contact request "Pending" column to "Cancelled" in Contacts database
     *
     * @return void,array
     */
    private function acceptContactRequest ($user, $target) {
        $rejectableRequest = Contact::where('first_user', $target->id)
        ->where('second_user', $user->id)
        ->firstOrFail();
        $rejectableRequest->status = 2;
        
        return $rejectableRequest->save();
    }
    
    /**
     * Removes current contact from contacts list and updates "Status" column of Contacts DB to "Removed"
     *
     * @return void,array
     */
    private function delMyContact ($user, $target) {
        $removeContact = Contact::where('first_user', $target->id)
        ->where('second_user', $user->id)
        ->where('status', 'accepted')
        ->orWhere('first_user', $user->id)
        ->where('second_user', $target->id)
        ->where('status', 'accepted')
        ->firstOrFail();
        $removeContact->status = 5;
        
        return $removeContact->save();
    }
    
    /**
     * Block any "targeted" user by adding new column to Contacts DB
     *
     * @return void,array
     */
    private function storeBlockContact ($user, $target) {
        return Block::firstOrCreate([
            'public_id' => sha1(Str::uuid()->toString()),
            'blocker_user_id' => $user->id,
            'blocked_user_id' => $target->id,
            'updated_at' => now(),
            'created_at' => now()
        ]);
    }
    
    /**
     * Unblocks "blocked" user by removing previously recorded row from Contacts DB
     *
     * @return void,array
     */
    private function delUnblockContact ($user, $target) {
        return Block::where('blocker_user_id', $user->id)
        ->where('blocked_user_id', $target->id)
        ->firstOrFail()
        ->delete();
    }
    
    /**
     * Marks targeted user as spam by updating "IS_SPAM" column of Contacts DB to TRUE
     * It cancels received contact request
     * It does NOT block targeted user
     * Targeted user is able to send messages but not any more contact requests.
     *
     * @return void,array
     */
    private function markAsSpam ($user, $target) {
        $spam = Contact::where('first_user', $target->id)
        ->where('second_user', $user->id)
        ->where('status', 'pending')
        ->firstOrFail();
        $spam->is_spam = true;
        $spam->status = 4;
        
        return $spam->save();
    }
    
    /**
     * Unmarks spammed user and updates "IS_SPAM" column of Contacts DB to FALSE
     * Unspammed user can now send another contact requests!
     *
     * @return void,array
     */
    private function unSpamContact ($user, $target) {
        $spam = Contact::where('first_user', $target->id)
        ->where('second_user', $user->id)
        ->firstOrFail();
        $spam->is_spam = false;
        
        return $spam->save();
    }
}
