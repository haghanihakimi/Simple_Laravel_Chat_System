<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

// Broadcast::channel('newContactRequest', function ($user, $id) {
//     return true;
// });


Broadcast::channel('newContactRequest.{id}', function ($user, $id) {
    return $user->uid === $id;
});

Broadcast::channel('ContactRequestCancellation.{id}', function ($user, $id) {
    return $user->uid === $id;
});

Broadcast::channel('ContactRequestReject.{id}', function ($user, $id) {
    return $user->uid === $id;
});

Broadcast::channel('ContactRequestAcceptance.{id}', function ($user, $id) {
    return $user->uid === $id;
});

Broadcast::channel('contactRemoval.{id}', function ($user, $id) {
    return $user->uid === $id;
});

Broadcast::channel('contactBlocked.{id}', function ($user, $id) {
    return $user->uid === $id;
});

Broadcast::channel('contactSpamMark.{id}', function ($user, $id) {
    return $user->uid === $id;
});

Broadcast::channel('contactUnmarkSpam.{id}', function ($user, $id) {
    return $user->uid === $id;
});

Broadcast::channel('individualMessageChannel.{id}', function ($user, $id) {
    return $user->uid === $id;
});

Broadcast::channel('ReceivedContactRequestCounter.{id}', function ($user, $id) {
    return $user->uid === $id;
});