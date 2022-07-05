@extends('pages.messages')

@section('messages')
    <div class="m_Container">
        <div class="m_box">
            {{-- Left side icons bar --}}
            <div class="left-panel">
                <div @click.stop="$store.dispatch('togglePreferences', true, false)" class="preferences-left-panel" title="Preferences">
                    <span class="material-symbols-rounded">discover_tune</span>
                </div>
                <div class="middle-controls-box">
                    <div class="newFeeds-left-panel left-panel-innerbox">
                        <div class="icon-holder">
                            <a href="{{route('feeds')}}" target="_self">
                                <span class="material-symbols-rounded">home</span>
                            </a>
                        </div>
                    </div>
                    <div @click.stop="$store.dispatch('toggleSearchBox', true, false)" class="search-left-panel left-panel-innerbox">
                        <div class="icon-holder">
                            <span class="material-symbols-rounded">search</span>
                        </div>
                    </div>
                    <div @click.stop="$store.dispatch('toggleConversationsListShow', true, false)" class="messages-left-panel left-panel-innerbox">
                        <div class="icon-holder">
                            <span class="material-symbols-rounded">chat</span>
                            <notifications-counter></notifications-counter>
                        </div>
                    </div>
                    <div class="contacts-left-panel left-panel-innerbox" @click.stop="$store.dispatch('toggleContactShow', true, false)">
                        <div class="icon-holder" style="width: 30px;height:30px;">
                            <span class="material-symbols-rounded">group</span>
                            <contact-requests-counter :user-id="{{json_encode(auth()->user()->uid)}}"></contact-requests-counter>
                        </div>
                    </div>
                </div>
                <div class="bottom-controls-box">
                    <div class="settings-left-panel left-panel-innerbox">
                        <div class="icon-holder">
                            <a href="{{route('security.settings')}}" target="_self">
                                <span class="material-symbols-rounded">settings</span>
                            </a>
                        </div>
                    </div>
                    {{-- <div class="logout-left-panel left-panel-innerbox">
                        <form action="{{route('signout')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <button type="submit" role="button">
                                <span class="material-symbols-rounded">logout</span>
                            </button>
                        </form>
                    </div> --}}
                    <sign-out></sign-out>
                </div>
            </div>

            <div class="messages-panel">
                
                <div @click.stop="$store.state.generals.leftActivePanels = true" class="left-panel-viewer" :class="[$store.state.generals.leftActivePanels ? 'leftActive_panels' : 'leftInactive_panels']">
                    <preferences-layout v-if="$store.state.generals.preferencesShow"></preferences-layout>
                    <searchbox-layout v-if="$store.state.generals.searchShow"></searchbox-layout>
                    <contacts-list></contacts-list>
                </div>
                <div class="{{($beingBlocked || $hasBlocked) ? 'middle-messages-viewer unavailable-page' : 'middle-messages-viewer'}}">
                    {{-- Main Messaging middle section --}}
                    @if ($beingBlocked)
                        <h2 class="profile-unavailability">This page is unavailable at the moment.</h2>
                    @elseif($hasBlocked)
                        <h2 class="profile-unavailability">You blocked <strong>{{$target->first_name}} {{$target->surname}}</strong>. <br />You cannot contact {{($target->gender === 'female') ? 'her' : 'him'}} at this moment!</h2>
                    @else
                        <messages-screen :user-name="{{json_encode($username)}}" :user-id="{{json_encode(auth()->user()->uid)}}"></messages-screen>
                    @endif
                </div>
                <div @click.stop="$store.dispatch('toggleProfilePreview', true, false)" class="right-panel-viewer" :class="[$store.state.generals.rightActivePanels ? 'rightActive_panels' : 'rightInactive_panels']">
                    <profile-overview v-if="$store.state.generals.profilePreview"></profile-overview>
                </div>
            
            </div>

            <div class="right-panel">
                <div class="panel-innerbox">
                    <div class="profile-pic" @click.stop="$store.dispatch('toggleProfilePreview', true, false)">
                        <img src="{{ asset('/storage/locals/'.$user->avatar) }}" alt="{{ $user->fname.' '.$user->sname }} tuba profile picture/image">
                    </div>
                    <span class="horizontal-line"><hr></span>
                </div>
            </div>
        </div>
    </div>
@endsection