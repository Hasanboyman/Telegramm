<!DOCTYPE html>
<html lang="en">
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <title>Chat Application</title>
    </head>
    <body id="reload">

    <!-- MAIN APP -->
    <section class="mainApp">
        <div class="leftPanel">
            <header>
                <button class="trigger" id="toggleMenu">
                    <span class="bars_settings" id="menuIcon"><i class="fas fa-bars"></i></span>
                    <span class="closeIcon" id="closeSearchModal" style="display: none;"><i class="fa-solid fa-arrow-left-long" style="color: #ffffff;"></i></span>
                </button>
                <input class="searchChats" type="search" placeholder="Search..." id="searchInput" />
            </header>

            <!-- Modal for Search Results -->
            <div id="searchModal" class="search-modal">
                <div class="modal-content">
                    <div id="searchResults">
                        <!-- Search results container -->
                        <form action="/chats" method="POST">
                            @csrf
                            <input type="hidden" name="user_one" value="{{ auth()->user()->id }}">

                            <!-- Example input for the other user to start chat with -->
                            <!-- Assuming you pass a user ID from search results to open chat with -->
                            <select name="user_two" required>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>

                            <button type="submit">Start Chat</button>
                        </form>
                    </div> <!-- Container for search results -->
                </div>
            </div>



            <div class="chats" id="chatsContainer">
                @foreach ($chats as $chat)
                    @if ($chat->userOne->id == auth()->id() || $chat->userTwo->id == auth()->id())
                        <a href="/chats/{{ $chat->id }}">
                            <div class="chatButton {{  $chat->id ? 'active' : '' }}">
                                <div class="chatInfo">
                                    @php
                                        $otherUser = $chat->user_one == auth()->id() ? $chat->userTwo : $chat->userOne;
                                        $profilePictureUrl = $otherUser->profile_picture ? asset('storage/' . $otherUser->profile_picture) : 'https://thispersondoesnotexist.com/';
                                    @endphp

                                    <div class="image" style="background: url('{{ $profilePictureUrl }}') no-repeat center;background-size: cover;">

                                    </div>
                                    <div class="chatInfo_p">
                                        <p class="name">{{ $chat->name }}</p>
                                        <p class="message">
                                            @if ($chat->messages->isNotEmpty())
                                                @if($chat->messages->last()->image_url)
                                                    picture 🖼️
                                                @else
                                                    {{( Str::limit($chat->messages->last()->message, 10, '...')) }}
                                                @endif
                                            @else
                                                No messages
                                            @endif
                                        </p>
                                    </div>

                                    <div class="status {{ $chat->messages->isNotEmpty() ? 'onTop' : 'normal' }}">
                                        <p class="date">
                                            @if ($chat->messages->isNotEmpty())
                                                {{ $chat->messages->last()->created_at->format('H:i') }}
                                            @else
                                                No date
                                            @endif
                                        </p>
                                        @if ($chat->messages->where('sender_id', '!=', auth()->id())->count())
                                            <p class="count">
                                                {{ $chat->messages->where('sender_id', '!=', auth()->id())->count() }}
                                            </p>
                                        @endif
                                        @if ($chat->messages->isNotEmpty())
                                            <i class="fas fa-check-double read"></i>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>

        <span class="rightPanel">
            <b>Select a chat to start messaging</b>
            @if (Auth::check())
                <p>Logged in as {{ Auth::user()->name }}</p>
            @endif
        </span>
    </section>

    @include("Others.Menu")
    </body>
    </html>

<style>



    * {
        margin: 0;
        padding: 0;
    }
    /* Dark Theme */
    .dark-theme body {
        background-color: #121212;
        color: #e0e0e0;
    }
    .dark-theme .chatButton {
        background-color: #333333;
        color: #e0e0e0;
    }

    .dark-theme .leftPanel{
        background-color: #333333;

    }
    .dark-theme .chatButton:hover, .dark-theme .moreMenu .option:hover {
        background-color: rgb(122 122 122 / 14%);
        color: #e0e0e0;
    }
    .dark-theme .msg {
        background-color: #1c1c1c;
        color: #e0e0e0;
    }
    .dark-theme .status .count {
        color: #FFFFFF;
    }
    .dark-theme div[class~="onTop"]>.count {
        background: #FFF;
        color: #419fd9;
    }
    .dark-theme .replyBar {
        background-color: #333333;
    }
    .dark-theme .replyBar>.replyMessage {
        border: none;
    }
    .dark-theme .otherTools>.toolButtons:hover {
        color: #FFFFFF;
        background: transparent;
    }
    .dark-theme .tbButton:hover,.otherTools .toolButtons:hover {
        color: #FFFFFF;
        background: transparent;
    }
    .dark-theme .go-back {
        background: #419fd9;
        color: #FFFFFF;
    }
    .dark-theme .moreMenu,.dark-theme .moreMenu>button {
        color: #FFFFFF;
        background: #333333;
    }
    .dark-theme .emojiList .pick:hover {
        background-color: #333333;
    }
    .dark-theme .chats {
        background: #333333;
    }
    .dark-theme .rightPanel>.topBar {
        background: #333333;
        color: #FFFFFF;
    }
    .dark-theme .menu {
        background: #333333;
    }
    .dark-theme .rightPanel>.rightSide>button {
        background: black;
    !important;
    }
    .dark-theme .leftSide>.chatName {
        color: #FFFFFF;
    }
    .dark-theme *>button:hover {
        color: #FFFFFF;
        background: #a5a0a033;
    }
    .dark-theme *>button {
        color: #888;
        background: #333333;
        border: 0;
    }
    .dark-theme .otherTools>.toolButtons {
        font-size: 18px;
        background: #333333;
        border: 0;
    }
    .dark-theme button,input[type="search"],input[type="text"] {
        border-top-width: 0.5px;
        border-top-style: solid;
        border-top-color: #474646;
        border-right-width: 0.5px;
        border-right-style: solid;
        border-right-color: #555252;
        border-left-width: 0.1px;
        border-left-style: solid;
        border-left-color: rgba(22, 30, 22, 0.8);
        border-bottom-width: 0.1px;
        border-bottom-style: solid;
        border-bottom-color: gainsboro;
    }
    .dark-theme .replyBar>.attach {
        background: #333333;
    !important;
        border: 0;
    }
    .dark-theme .searchChats {
        border-top-width: 0.5px;
        border-top-style: solid;
        border-top-color: #474646;
        border-right-width: 0.5px;
        border-right-style: solid;
        border-right-color: #555252;
        border-left-width: 0.1px;
        border-left-style: solid;
        border-left-color: rgba(22, 30, 22, 0.8);
        border-bottom-width: 0.1px;
        border-bottom-style: solid;
        border-bottom-color: gainsboro;
    }
    .dark-theme *>input {
        color: #FFFFFF;
        background: #333333;
        border: 1px solid #333333;
    }
    .dark-theme *>input:focus {
        color: #FFFFFF;
        background: #333333;
        border: 1px solid #090909;
        box-shadow: rgba(0, 0, 0, 0.17) 0 -23px 25px 0 inset, rgba(0, 0, 0, 0.15) 0 -36px 30px 0 inset, rgba(0, 0, 0, 0.1) 0 -79px 40px 0 inset, rgba(0, 0, 0, 0.06) 0 2px 1px, rgba(0, 0, 0, 0.09) 0 4px 2px, rgba(0, 0, 0, 0.09) 0 8px 4px, rgba(0, 0, 0, 0.09) 0 16px 8px, rgba(0, 0, 0, 0.09) 0 32px 16px;
    }
    .dark-theme header {
        color: #FFFFFF;
        background: #333333;
    }
    body {
        overflow: hidden;
        background: #419fd9 url('https://i.postimg.cc/N08p3mGb/dark.png');
        font-family: 'Open Sans', 'Roboto', 'Ubuntu', sans-serif;
        font-size: 16px;
    }
    a {
        text-decoration: none;
    }
    button {
        cursor: pointer;
    }
    button,input[type="search"],input[type="text"] {
        border: none;
        outline: none;
    }
    input[type="checkbox"] {
        display: none;
        margin: 7px 15px 7px 7px;
    }
    .alerts {
        position: absolute;
        bottom: 10px;
        left: 10px;
        z-index: 9999;
        padding: 10px;
        color: #666;
        border-radius: 4px;
        background: #FFF;
        box-shadow: 0 0 7px 0 rgba(0, 0, 0, 0.4);
        display: none;
    }
    /* small conversation menu */
    .moreMenu {
        position: absolute;
        top: 70px;
        right: 0;
        z-index: 10;
        padding: 10px 0 10px 0;
        color: #666;
        border-radius: 0 0 0 4px;
        background: #FFF;
        display: none;
        border-top: 1px solid #DDD;
    }
    .moreMenu .option {
        width: 150px;
        height: 50px;
        display: block;
        background: #FFF;
        font-size: 14px;
        text-align: left;
        border-radius: 4px;
        padding-left: 10px;
    }
    .moreMenu .option:hover {
        background: #DDD;
    }
    .moreMenu .option:nth-last-child(1) {
        margin-top: 3px;
    }
    /* switch to mobile version screen */
    .switchMobile {
        position: absolute;
        width: 65%;
        height: auto;
        padding: 10px;
        background: #FFF;
        top: 75px;
        left: 0;
        right: 0;
        margin: auto;
        border-radius: 4px;
        box-shadow: 0 0 7px 0 rgba(0, 0, 0, 0.1);
        display: none;
    }
    .switchMobile .title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
    }
    .switchMobile .desc {
        font-size: 14px;
        font-weight: 300;
        margin-bottom: 25px;
    }
    .switchMobile .okay {
        float: right;
        width: 80px;
        font-size: 16px;
        font-weight: 600;
        background: #419fd9;
        color: #fff;
        border-radius: 4px;
        padding: 10px;
    }
    /* side menu */
    .menuWrap {
        top: 0;
        position: absolute;
        width: 30%;
        min-width: 240px;
        max-width: 320px;
        height: 100%;
        z-index: 300;
        display: none;
    }
    .menu {
        position: relative;
        left: -320px;
        width: 100%;
        height: 100vh;
        float: left;
        background: #FFF;
        box-shadow: 0 0 7px 0 rgba(0, 0, 0, 0.4);
        opacity: 0;
    }
    .me {
        position: relative;
        width: calc(100% - 50px);
        height: 140px;
        background: #419fd9;
        padding: 15px 25px;
        margin-bottom: 15px;
    }
    .me .image {
        width: 70px;
        height: 70px;
        background: #FFF url({{ asset('storage/' . Auth::user()->profile_picture) }}) no-repeat center;
        background-size: cover;
        border-radius: 100%;
        cursor: pointer;
    }
    .me .settings {
        position: absolute;
        right: 20px;
        bottom: 65px;
        width: 40px;
        height: 40px;
        padding-top: 2px;
        color: #FFF;
        border-radius: 100%;
        background: rgba(0, 0, 0, 0.15);
    }
    .me .settings:hover {
        background: rgba(0, 0, 0, 0.35);
    }
    .me .cloud {
        display: none;
        position: absolute;
        right: 20px;
        bottom: 15px;
        width: 40px;
        height: 40px;
        color: #FFF;
        border-radius: 100%;
        background: rgba(0, 0, 0, 0.09);
    }

    .chat-container {
        height: 500px; /* Adjust to your needs */
        overflow-y: auto; /* Enable vertical scrolling */
    }

    .messages {
        display: flex;
        flex-direction: column;
    }

    .me .cloud:hover {
        background: rgba(0, 0, 0, 0.35);
    }
    .me .myinfo {
        position: absolute;
        bottom: 15px;
        font-size: 14px;
        color: #FFF;
    }
    .myinfo .name {
        font-weight: 600;
        margin-bottom: 5px;
    }
    .myinfo .phone {
        font-weight: 300;
    }
    nav button {
        display: flex;
        align-items: center;
        width: 100%;
        height: 45px;
        background: #FFF;
        text-align: left;
        padding-left: 20px;
        color: #666;
    }
    nav {
        float: left;
        width: 100%;
        height: auto;
        max-height: 350px;
        overflow-x: hidden;
        overflow-y: auto;
    }
    nav button:hover {
        background: #EEE;
    }
    nav button>i {
        color: #999;
        float: left;
    }
    nav button>span {
        display: inline-block;
        margin-top: 5px;
        margin-left: 20px;
        font-weight: 600;
        font-size: 14px;
    }
    .info {
        position: absolute;
        left: 20px;
        bottom: 15px;
        font-size: 12px;
        color: #666;
    }
    /* configuration screen */
    .config {
        position: absolute;
        width: 40%;
        height: 100vh;
        left: 0px;
        right: -200vw;
        top: 0px;
        margin: auto;
        background: #DDD;
        overflow-x: hidden;
        overflow-y: auto;
        display: block;
        z-index: 520;
        opacity: 0;
    }
    .confTitle {
        font-size: 24px;
        font-weight: 600;
        color: #444;
        margin: 10px 0px 15px 0px;
    }
    .configSect {
        float: left;
        width: calc(100% - 60px);
        padding: 15px 30px;
        margin-bottom: 10px;
        background: #FFF;
    }
    .profile .image {
        width: 140px;
        height: 140px;
        background: #FFF url({{ asset('storage/' . Auth::user()->profile_picture) }}) no-repeat center;
        background-size: cover;
        border-radius: 100%;
        float: left;
        margin-right: 30px;
    }
    .side {
        flex-direction: column;
        justify-content: center;
        display: flex;
        width: auto;
        height: 110px;
        float: none;
        position: relative;
    }
    .side .name {
        font-size: 26px;
        font-weight: 600;
    }

    .side>p{
        padding-bottom: 10px;
        font-weight: 600;
    }

    .side>p:nth-child(3){
        opacity: 0.5;
    }

    .side .pStatus {
        margin-top: 5px;
        font-size: 14px;
        font-weight: 300;
    }

    .profile .changePic, .profile .edit {
        rotate: 180deg;
        position: absolute;
        top: 20%;
        transform: translate(50%, 29%);
        height: 70px;
        width: 140px;
        border-radius: 150px 150px 0 0;
        background: #00000045;
    }

    .image:hover .changePic {
        animation: fadeIn 0.5s ease-in-out forwards;
    }

    .image:not(:hover) .changePic {
        animation: fadeOut 0.5s ease-in-out forwards;
    }

    @keyframes fadeIn {
        0% {
            transform: translateY(15px);
            opacity: 0;
        }
        100% {
            transform: translateY(20px);
            opacity: 1;
        }
    }

    @keyframes fadeOut {
        0% {
            transform: translateY(20px);
            opacity: 1;
        }
        100% {
            transform: translateY(15px);
            opacity: 0;
        }
    }






    .profile .edit {
        margin-left: 10px;
        background: #FFF;
        color: #999;
    }
    .profile .edit:hover {
        color: #419fd9;
    }
    .second ul {
        float: none;
        margin-left: -7px;
        list-style-type: none;
    }
    .second ul li {
        margin: 7px;
    }
    .second .blue {
        color: #419fd9
    }
    .second label {
        display: block;
        clear: both;
    }
    .second .information {
        margin-bottom: 30px;
    }
    .check {
        position: relative;
        float: left;
        display: block;
        width: 38px;
        height: 14px;
        background: #BBB;
        cursor: pointer;
        border-radius: 15px;
        transition: all 0.2s ease-in-out;
    }
    .check>.tracer {
        width: 16px;
        height: 16px;
        background: #FFF;
        border: 2px solid #BBB;
        border-radius: 100%;
        float: left;
        margin-top: -3px;
        transition: all 0.2s ease-in-out;
    }
    .toggleTracer:checked~.check {
        background: #419fd9;
    }
    .toggleTracer:checked~.check>.tracer {
        border-color: #419fd9;
        margin-left: 18px;
    }
    .optionWrapper {
        display: block;
        width: 100%;
        height: 32px;
    }
    .optionWrapper p {
        float: left;
        margin-top: 3px;
        margin-left: 15px;
        font-size: 14px;
        color: #444;
    }

    .overlay {
        position: absolute;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 2;
        display: none;
    }
    /* ----------------- */
    /* MAIN APP ELEMENTS */
    /* ----------------- */


    .leftPanel {
        position: relative;
        display: inline-block;
        left: 0;
        width: 320px;
        height: 100vh;
        float: left;
        background: #FFF;
        border-right: 1px solid #DDD;
    }
    header {
        width: 100%;
        height: 70px;
        background: #FFF;
    }
    .trigger {
        float: left;
        width: 32px;
        height: 32px;
        margin: 20px 15px 0;
        color: #BBB;
        cursor: pointer;
        opacity: 0.6;
        background: none;
    }
    .trigger:hover {
        opacity: 1;
    }
    .trigger>svg {
        width: 24px;
        height: 24px;
        fill: #888;
    }
    .searchChats {
        width: calc(100% - 85px);
        height: 40px;
        background: #EEE;
        float: left;
        padding: 8px;
        margin-top: 14px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        border: 1px solid black;
    }
    .searchChats:focus {
        color: #444;
        border: 2px solid #419fd9;
        background: rgb(243, 243, 243);
    }
    .chats {
        width: 100%;
        height: calc(100vh - 70px);
        float: left;
        overflow: hidden;
        overflow-y: scroll; /* Add the ability to scroll */
    }

    .chats::-webkit-scrollbar {
        display: none;
    }
    .chatButton {
        float: left;
        width: 100%;
        height: 80px;
        background: #FFF;
        color: #555;
        cursor: pointer;
        overflow: hidden;
    }
    .chatButton:hover {
        color: #555;
        background: #EEE;
    }
    .active,.active:hover {
        color: #FFF;
        background: #419fd9;
    }
    .chatInfo {
        display: flex;
        justify-content: space-between;
    }
    .chatInfo_p {
        padding-top: 20px;
        padding-right: 10px ;
    }
    .chatInfo .image {
        width: 55px;
        height: 55px;
        background: #DDD url(https://thispersondoesnotexist.com/) no-repeat center;
        background-size: cover;
        border-radius: 100%;
        margin: 13px 13px;
    }
    .chatInfo .my-image {
        background-image: url(https://avatars1.githubusercontent.com/u/21313332?s=460&v=4);
    }
    .chatInfo .name {
        float: left;
        font-weight: 600;
    }
    .chatInfo .message {
        color: #9B9B9B;
        clear: left;
        margin-top: 7px;
    }
    .status {
        width: 175px;
        position: relative;
        float: right;
    }
    .status .fixed {
        position: absolute;
        right: 10px;
        bottom: 10px;
        display: none;
    }
    div[class~="normal"]>.fixed {
        display: none;
    }
    div[class~="normal"]>.count {
        right: 10px;
        bottom: 7px;
    }
    div[class~="onTop"]>.fixed {
        display: block;
        width: 24px;
        height: 24px;
        fill: #FFF;
    }
    div[class~="onTop"]>.count {
        right: 49px;
        bottom: 7px;
        background: #FFF;
        color: #419fd9;
    }
    .status .count {
        display: flex;
        justify-content: center;
        align-items: center;
        position: absolute;
        right: 49px;
        bottom: 7px;
        width: 10px;
        height: 12px;
        padding: 7px;
        border-radius: 20px;
        color: #222;
        background: rgba(0, 0, 0, 0.12);
    }
    .status .date {
        position: absolute;
        right: 12px;
        top: 12px;
        font-size: 12px;
    }
    .status .read {
        position: absolute;
        right: 20px;
        top: 54px;
    }
    .rightPanel {
        font-weight: 500;
        padding: 5px;
        left: 55%;
        position: absolute;
        top: 50%;
        border-radius:20px ;
        background: rgba(153, 153, 153, 0.37);
        color: #FFFFFF;
    }
    .rightPanel .topBar {
        float: left;
        width: 100%;
        height: 70px;
        background: #FFF;
    }
    .go-back {
        float: left;
        width: 44px;
        height: 44px;
        margin-left: 12px;
        margin-top: 12px;
        background: #419fd9;
        border: none;
        border-radius: 100%;
    }
    .go-back>svg {
        float: left;
        width: 24px;
        height: 24px;
        margin-left: calc(50% - 13px);
        fill: #fff;
    }
    .leftSide {
        display: inline-block;
        clear: none;
        float: left;
    }
    .leftSide .chatName {
        float: left;
        width: 320px;
        margin-left: 16px;
        margin-top: 13px;
        color: #444;
        font-weight: 600;
        cursor: default;
    }
    .chatName>span {
        font-size: 12px;
        font-weight: 500;
        color: #BBB;
        margin-left: 12px;
    }
    .leftSide .chatStatus {
        float: left;
        clear: left;
        margin-left: 16px;
        margin-top: 2px;
        font-size: 12px;
        color: #419fd9;
        font-weight: 300;
        cursor: default;
    }
    .chatStatusoffline {
        float: left;
        clear: left;
        margin-left: 16px;
        margin-top: 2px;
        font-size: 12px;
        color: #ffffff;
        font-weight: 300;
        cursor: default;
    }
    .rightSide {
        display: inline-block;
        clear: none;
        float: right;
        margin-right: 20px;
    }
    .tbButton,.otherTools .toolButtons {
        width: 50px;
        height: 50px;
        margin-top: 10px;
        background: #FFF;
        color: #888;
        border-radius: 100%;
    }
    .tbButton:hover,.otherTools .toolButtons:hover {
        color: #555;
        background: #DDD;
    }
    /* THE CONVERSATION HISTORY CSS */
    .convHistory {
        float: left;
        position: relative;
        width: 100%;
        height: calc(100vh - 140px);
        background: #333;
        overflow-x: hidden;
        overflow-y: auto;
    }
    .userBg {
        opacity: 0.89;
        background: url("https://i.postimg.cc/N08p3mGb/dark.png") 100% center;
    }
    .msg {
        position: relative;
        width: auto;
        min-width: 100px;
        max-width: 45%;
        height: auto;
        padding: 25px 25px 35px;
        margin: 20px 15px 0;
        background: #FFF;
        border-radius: 7px;
        clear: both;
    }
    .msg:nth-last-child(1) {
        margin-bottom: 20px;
    }
    .msg .timestamp {
        display: block;
        position: absolute;
        right: 10px;
        bottom: 6px;
        color: #AAA;
        user-select: none;
    }
    .messageReceived {
        float: left;
        background: #FFF;
    }
    .messageSent {
        float: right;
        background: #effdde;
    }
    .messageSent>.timestamp,.messageSent>.readStatus {
        bottom: 10px;
        right: 40px;
        color: darkgreen;
        user-select: none;
    }
    .messageSent>.readStatus {
        position: absolute;
        bottom: 10px;
        right: 12px;
    }
    /* THE REPLY BAR CSS */
    .replyBar {
        width: 100%;
        height: 70px;
        float: left;
        background: #fff;
    }
    .replyBar .attach {
        width: 70px;
        height: 70px;
        color: #777;
        background: #FFF;
        float: left;
    }
    .replyBar .attach:hover {
        color: #555;
        background: #DDD;
    }
    .replyBar .d45 {
        transform: rotate(45deg);
        font-size: 32px;
    }
    .replyBar .replyMessage {
        width: calc(100% - 220px);
        float: left;
        height: 70px;
        padding: 0 8px;
        font-size: 16px;
    }
    .replyBar .otherTools {
        float: right;
        width: 120px;
        height: 70px;
    }

    .settings-modal {
        display: none; /* Ensure it's hidden by default */
    }

    .settings-modal {
        position: fixed; /* or absolute */
        top: 0;
        left: 50%;
    }
    .overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .emojiBar {
        display: none;
        position: absolute;
        width: 325px;
        height: 200px;
        padding: 10px;
        right: 30px;
        bottom: 80px;
        border: 2px solid #DDD;
        border-radius: 3px;
        background: #FFF;
    }
    .emoticonType {
        width: 100%;
        height: 50px;
    }
    .emoticonType button {
        width: 105px;
        height: 36px;
        font-weight: 600;
        color: #555;
        background: none;
    }
    .emoticonType button:hover {
        color: #FFF;
        background: #419fd9;
    }
    .emojiList,.stickerList {
        width: 100%;
        height: 150px;
        overflow-x: hidden;
        overflow-y: auto;
    }
    .emojiList .pick {
        width: 50px;
        height: 50px;
        background: transparent;
        background-repeat: no-repeat;
        background-position: center;
        background-size: 70%;
        transition: all 0.15s ease-in-out;
    }
    .emojiList .pick:hover {
        background: #DDD;
        background-repeat: no-repeat;
        background-position: center;
        background-size: 70%;
    }
    .stickerList .pick {
        width: 80px;
        height: 80px;
        background: transparent;
        background-repeat: no-repeat;
        background-position: center;
        background-size: 65%;
        transition: all 0.15s ease-in-out;
    }
    .stickerList .pick:hover {
        background: #DDD;
        background-repeat: no-repeat;
        background-position: center;
        background-size: 65%;
    }

    /* Add your custom styles here */
    .mainApp {
        display: flex;
    }

    .search-modal {
        display: none; /* Hidden by default */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    }

    .close-btn {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }
    .close-btn:hover,
    .close-btn:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    #searchResults {
        height: 100%;
        background-color: #333;
    }
    .user-item {
        padding: 10px;
        cursor: pointer;
        border-bottom: 1px solid #ddd;
    }
    .user-item:hover {
        background-color: #ccc;
    }
    /* SOLVING RESPONSIVE DESIGN ISSUES */
    @media screen and (max-width: 1180px) {
        .config {
            width: 41%;
        }
    }
    @media screen and (max-width: 980px) {
        .config {
            width: 90%;
        }
    }
    @media screen and (max-width: 940px) {
        body {
            font-size: 14px;
        }
        .msg {
            width: 90%;
        }
        .leftSide {
            width: 120px;
            margin-top: 4px;
        }
        .chatName>span {
            display: none;
        }
    }
    @media screen and (max-width: 720px) {
        .leftPanel {
            width: 80px;
        }
        .trigger {
            margin-left: 22px;
        }
        .rightPanel {
            width: calc(100% - 81px);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .msg {
            width: auto;
            max-width: 60%;
        }
        .profile .changePic,.profile .edit {
            font-size: 14px;
            font-weight: 500;
        }
        .profile .edit {
            margin-left: 5px;
        }
        .searchChats,.search,.chatButton>.name,.chatButton>.message,.chatButton>.status {
            display: none;
        }
    }
    @media screen and (max-width: 480px) {
        .go-back {
            display: block;
        }
        .msg {
            padding: 12px 12px 36px;
            border-radius: 7px;
            clear: both;
        }
    }
    @media screen and (max-width: 360px) {
    }
</style>
<script>


    document.addEventListener("DOMContentLoaded", function () {
        // Check local storage to apply the saved theme
        const themeToggleCheckbox = document.getElementById('checkNight');
        const bodyElement = document.body;

        // Apply the saved theme
        const currentTheme = localStorage.getItem('theme') || 'light';
        if (currentTheme === 'dark') {
            bodyElement.classList.add('dark-theme');
            themeToggleCheckbox.checked = true;
        } else {
            themeToggleCheckbox.checked = false;
        }

        // Listen for changes to the checkbox
        themeToggleCheckbox.addEventListener('change', () => {
            if (themeToggleCheckbox.checked) {
                bodyElement.classList.add('dark-theme');
                localStorage.setItem('theme', 'dark');
            } else {
                bodyElement.classList.remove('dark-theme');
                localStorage.setItem('theme', 'light');
            }
        });


        document.querySelector(".settings").addEventListener("click", function() {
            document.querySelector(".config").style.opacity = "1";
            document.querySelector(".config").style.right = "0px";
            document.querySelector(".menuWrap").style.display = "none";
            document.querySelector(".menu").style.opacity = "0";
            document.querySelector(".menu").style.left = "-320px";
        });

        document.querySelector(".deskNotif").addEventListener("click", function() {
            const elements = [".showSName", ".showPreview", ".playSounds"];
            elements.forEach(function (selector) {
                const elem = document.querySelector(selector);
                elem.style.display = (elem.style.display === "none" || !elem.style.display) ? "block" : "none";
            });
        });




        document.querySelector(".otherOptions").addEventListener("click", function() {
            const moreMenu = document.querySelector(".moreMenu");
            moreMenu.style.display = (moreMenu.style.display === "none" || !moreMenu.style.display) ? "block" : "none";
        });

        document.querySelector(".search").addEventListener("click", function() {
            document.querySelector(".searchChats").focus();
        });

        document.querySelector(".emoji").addEventListener("click", function() {
            const emojiBar = document.querySelector(".emojiBar");
            emojiBar.style.display = (emojiBar.style.display === "none" || !emojiBar.style.display) ? "block" : "none";
        });

        document.querySelectorAll(".convHistory, .replyMessage").forEach(function(element) {
            element.addEventListener("click", function() {
                document.querySelector(".emojiBar").style.display = "none";
            });
        });
    });

    const searchInput = document.getElementById('searchInput');
    const searchModal = document.getElementById('searchModal');
    const closeSearchModal = document.getElementById('closeSearchModal');
    const resultsContainer = document.getElementById('searchResults');
    const chatsContainer = document.getElementById('chatsContainer');
    const menuIcon = document.getElementById('menuIcon');
    const closeIcon = document.getElementById('closeSearchModal');

    // Show or hide icons based on input focus
    searchInput.addEventListener('focus', function () {
        chatsContainer.style.display = 'none'; // Hide chat list when searching
        menuIcon.style.display = 'none'; // Hide menu icon
        closeIcon.style.display = 'block'; // Show close icon
    });

    searchInput.addEventListener('blur', function () {
        if (this.value === '') {
            chatsContainer.style.display = 'block'; // Show chat list if search input is empty
            menuIcon.style.display = 'block'; // Show menu icon
            closeIcon.style.display = 'none'; // Hide close icon
        }
    });

    // Close modal on close button click
    closeSearchModal.onclick = function() {
        searchModal.style.display = "none"; // Hide modal
        chatsContainer.style.display = 'block'; // Show chat list again
        menuIcon.style.display = 'block'; // Show menu icon
        closeIcon.style.display = 'none'; // Hide close icon
        document.getElementById('searchInput').value = "";
    }

    // Close modal when clicking outside of the modal
    window.onclick = function(event) {
        if (event.target === searchModal) {
            searchModal.style.display = "none"; // Hide modal
            chatsContainer.style.display = 'block'; // Show chat list again
            menuIcon.style.display = 'block'; // Show menu icon
            closeIcon.style.display = 'none'; // Hide close icon
        }
    };


    const currentUserId = @json(auth()->id());

    // Search users
    searchInput.addEventListener('input', function () {
        const query = this.value;

        if (query.length > 0) {
            fetch(`/search/users?query=${query}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
                .then(response => response.json())
                .then(data => {
                    resultsContainer.innerHTML = '';

                    // Check if any users were found
                    if (data.length > 0) {
                        data.forEach(user => {
                            const userElement = document.createElement('div');
                            userElement.classList.add('user-item'); // Apply the CSS class
                            userElement.textContent = user.name;

                            // Add click event to check or create a chat for the clicked user
                            userElement.addEventListener('click', function() {
                                fetch('http://127.0.0.1:8000/chats', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                                    },
                                    body: JSON.stringify({
                                        user_one: currentUserId,
                                        user_two: user.id,
                                        name: '{{ Auth()->user()->name }}',
                                        image_url: 'http://example.com/image.jpg'
                                    })
                                })
                                    .then(response => {
                                        if (!response.ok) {
                                            throw new Error('Error checking chat');
                                        }
                                        return response.json();
                                    })
                                    .then(result => {
                                        if (result.status === 'existing' || result.status === 'created') {
                                            window.location.href = `/chats/${result.chat.id}`;
                                        } else {
                                            console.error('Unexpected response status:', result.status);
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error occurred while checking or creating chat:', error);
                                    });
                            });


                            resultsContainer.appendChild(userElement);
                        });
                    } else {
                        resultsContainer.innerHTML = '<p>No users found.</p>';
                    }
                    searchModal.style.display = "block"; // Show modal
                })
                .catch(error => {
                    console.error('Error during user search:', error); // Log the error
                });
        } else {
            resultsContainer.innerHTML = '';
            searchModal.style.display = "none";
        }
    });




</script>

