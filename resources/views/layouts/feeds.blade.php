@extends('pages.feeds')

@section('feeds')
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
                    <sign-out></sign-out>
                </div>
            </div>

            {{-- Center pane. In this section, when user clicks on any icon, a specific pane opens --}}
            <div class="messages-panel">
                
                {{-- In this section, panes to related icons (in the left) will be displayed --}}
                <div @click.stop="$store.state.generals.leftActivePanels = true" class="left-panel-viewer" :class="[$store.state.generals.leftActivePanels ? 'leftActive_panels' : 'leftInactive_panels']">
                    <preferences-layout v-if="$store.state.generals.preferencesShow"></preferences-layout>
                    <searchbox-layout v-if="$store.state.generals.searchShow"></searchbox-layout>
                    <contacts-list v-if="$store.state.generals.contactsShow"></contacts-list>
                    <conversations-view v-if="$store.state.generals.conversationsListShow"></conversations-view>
                </div>

                {{-- In this section POST contents will be displaed --}}
                <div class="middle-messages-viewer">
                    @isset($replacedCode)
                        <p class="usedRecoverCode">
                            {!! $replacedCode !!}
                        </p>
                    @endisset
                </div>

                {{-- In this section, panes to related icons (in the RIGHT) will be displayed --}}
                <div @click.stop="$store.dispatch('toggleProfilePreview', true, false)" class="right-panel-viewer" :class="[$store.state.generals.rightActivePanels ? 'rightActive_panels' : 'rightInactive_panels']">
                    <profile-overview v-if="$store.state.generals.profilePreview"></profile-overview>
                </div>
            
            </div>

            {{-- All available icons on right bar, right side of screen! --}}
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