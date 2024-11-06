<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Title</title>
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

        <div id="searchModal" class="search-modal">
            <div class="modal-content">
{{--                Container for search--}}
                <div id="searchResults"></div>
            </div>
        </div>

        <div class="chats" id="chatsContainer">
            @foreach ($chats as $chat)
                @if ($chat->userOne->id == auth()->id() || $chat->userTwo->id == auth()->id())
                    <a href="/chats/{{ $chat->id }}">
                        <div class="chatButton {{ $chat->id ? 'active' : '' }}">
                            <div class="chatInfo">
                                @php
                                    $otherUser = $chat->userOne->id == auth()->id() ? $chat->userTwo : $chat->userOne;
                                    $profilePictureUrl = $otherUser->profile_picture ? asset('storage/' . $otherUser->profile_picture) : 'https://thispersondoesnotexist.com/';
                                @endphp

                                <div class="image" style="background: url('{{ $profilePictureUrl }}') no-repeat center; background-size: cover;"></div>

                                <div class="chatInfo_p">
                                    <p class="name">{{ $chat->name }}</p>
                                    <p class="message">
                                        @if ($chat->messages->isNotEmpty())
                                            @php
                                                $lastMessage = $chat->messages->last(); // Get the latest message
                                            @endphp
                                            @if ($lastMessage->image_url)
                                                Picture 🖼️
                                            @else
                                                {{ Str::limit($lastMessage->message, 10, '...') }}
                                            @endif
                                        @else
                                            No messages
                                        @endif
                                    </p>
                                </div>

                                <div class="status {{ $chat->messages->isNotEmpty() ? 'onTop' : 'normal' }}">
                                    <p class="date">
                                        @if ($chat->messages->isNotEmpty())
                                            {{ $chat->messages->last()->created_at->format('H:i') }} <!-- Adjusted to get the last message's created_at -->
                                        @else
                                            No date
                                        @endif
                                    </p>
                                    @php
                                        $unreadCount = $chat->messages->where('sender_id', '!=', auth()->id())->where('seen', false)->count();
                                        $lastMessage = $chat->messages->where('sender_id', '!=', auth()->id())->last();
                                    @endphp

                                    @if ($unreadCount)
                                        <p class="count" id="unread-count-{{ $chat->id }}">{{ $unreadCount }}</p>
                                    @endif

                                    @if ($lastMessage && !$lastMessage->seen)
                                        <i class="fas fa-check read" id="seen-status-{{ $chat->id }}"></i> <!-- Single check for unread -->
                                    @elseif ($lastMessage && $lastMessage->seen)
                                        <i class="fas fa-check-double read" id="seen-status-{{ $chat->id }}"></i> <!-- Double check for read -->
                                    @endif


                                </div>
                            </div>
                        </div>
                    </a>
                @endif
            @endforeach
        </div>
    </div>


{{--    Messages --}}
    <div class="rightPanel">
        <div class="topBar">
            <div class="rightSide">
                <button class="tbButton search">
                    <i class="fas fa-search"></i>
                </button>
                <button class="tbButton otherOptions">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>

            <button class="go-back">
                <i class="fas fa-arrow-left"></i>
            </button>

            <div class="leftSide">
                <p class="chatName name">
                    @if ($selectedChat)
                        @php
                            $otherUser = $selectedChat->userOne->id === auth()->id() ? $selectedChat->userTwo : $selectedChat->userOne;
                        @endphp
                        <span>{{ $otherUser->name }}</span>
                    @endif
                </p>


                <p class="chatStatus {{ $otherUser->active ? '' : 'chatStatusoffline' }}">
                    {{ $otherUser->active ? 'online' : 'offline' }}
                </p>
            </div>

        </div>

        <div class="convHistory userBg" id="messageContainer">
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            let isUserAtBottom = true;

            function loadMessages(chatId) {
                $.ajax({
                    url: `/chats/${chatId}/messages`,
                    type: 'GET',
                    success: function(data) {
                        $('#messageContainer').empty(); // Clear the message container

                        data.messages.forEach(function(message) {
                            const msgClass = message.sender_id == {{ auth()->id() }} ? 'messageSent' : 'messageReceived';
                            const messageContent = message.image_url
                                ? `<img src="../storage/${message.image_url}" alt="Image" class="message-image">`
                                : `<span>${message.message}</span>`;

                            const messageHtml = `
                    <div class="msg ${msgClass}">
                        ${messageContent}
                        <span class="timestamp">${new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
                    </div>
                `;
                            $('#messageContainer').append(messageHtml); // Append new message
                        });

                        // Check if user is at the bottom before scrolling
                        if (isUserAtBottom) {
                            $('#messageContainer').scrollTop($('#messageContainer')[0].scrollHeight); // Scroll to the bottom
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            }

            // Check for new messages periodically
            function checkForNewMessages(chatId) {
                $.ajax({
                    url: `/chats/${chatId}/messages/unread`,
                    type: 'GET',
                    success: function(data) {
                        if (data.messages.length > 0) {
                            loadMessages(chatId);
                            markMessagesAsSeen(data.messages);
                        }
                        updateUnreadCount(chatId);
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            }

            // Event listener for scrolling
            $('#messageContainer').on('scroll', function() {
                isUserAtBottom = $(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight; // Update bottom status
            });


            function markMessagesAsSeen(messages) {
                messages.forEach(function(message) {
                    $.ajax({
                        url: `/chats/messages/${message.id}/seen`,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        success: function() {
                            // Successfully marked as seen
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                        }
                    });
                });
            }

            function updateUnreadCount(chatId) {
               const tick = false;
                $.ajax({
                    url: `/chats/${chatId}/messages/unread/count`,
                    type: 'GET',
                    success: function(data) {
                        const unreadCount = data.count;
                        const unreadCountElement = $('#unread-count-' + chatId);
                        const tick = true;
                        if (unreadCount > 0) {
                            unreadCountElement.text(unreadCount).show();
                        } else {
                            unreadCountElement.hide();
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });

                function markChatAsRead(chatId) {
                    $.post(`/chats/${chatId}/markAsRead`, {
                        _token: '{{ csrf_token() }}'
                    }).done(function(response) {
                        $('#unread-count-' + chatId).remove(); // Remove unread count display
                    });
                }



            }
            <?php
            $servername = "localhost";
            $username = "root";
            $password = "Mymodlyu0";
            $dbname = "telegramm";

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $sql = "SELECT id FROM chats";
            $result = $conn->query($sql);

            $chatIds = []; // Initialize an array to hold chat IDs

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $chatIds[] = $row['id']; // Add each chat ID to the array
                }
            }
            $conn->close();
            ?>

            const chatIds = <?php echo json_encode($chatIds); ?>; // This will create a JavaScript array of chat IDs

            $(document).ready(function() {
                const chatId = {{ $selectedChat->id }};
                loadMessages(chatId);



                setInterval(() => {
                    checkForNewMessages(chatId);
                    @foreach($chatIds as $chatId)
                     updateUnreadCount({{$chatId}})
                    @endforeach
                }, 5000);

                $('#messageContainer').on('scroll', function() {
                    isUserAtBottom = $(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight;

                    if ($(this).scrollTop() === 0 && !loading) {
                        loading = true;
                        $.ajax({
                            url: `/chats/${chatId}/messages/more`,
                            type: 'GET',
                            success: function(data) {
                                if (data.messages.length > 0) {
                                    data.messages.forEach(function(message) {
                                        const msgClass = message.sender_id == {{ auth()->id() }} ? 'messageSent' : 'messageReceived';
                                        const messageContent = message.image_url
                                            ? `<img src="../storage/${message.image_url}" alt="Image" class="message-image">`
                                            : `<span>${message.message}</span>`;

                                        const messageHtml = `
                                    <div class="msg ${msgClass}">
                                        ${messageContent}
                                        <span class="timestamp">${new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
                                    </div>
                                `;
                                        $('#messageContainer').prepend(messageHtml); // Prepend new messages to the top
                                    });
                                    loading = false; // Reset loading

                                    // Adjust scroll position after loading more messages
                                    if (!isUserAtBottom) {
                                        const scrollHeight = $('#messageContainer')[0].scrollHeight;
                                        $('#messageContainer').scrollTop(scrollHeight - $(this).innerHeight());
                                    }
                                } else {
                                    loading = false; // Reset loading if no new messages
                                }
                            },
                            error: function(xhr) {
                                console.error(xhr.responseText);
                                loading = false; // Reset loading on error
                            }
                        });
                    }
                });
            });
        </script>





        <style>.message-image {
                max-width: 100%;
                height: auto;
                border-radius: 8px;
                margin-top: 5px;
            }
        </style>



        {{--      Replay bar      --}}

        @include("Others.ReplayBar")

    </div>
</section>


{{-- MENU --}}
@include("Others.Menu")

</body>
</html>

<style>



    * {
        margin: 0;
        padding: 0;
    }


    .dark-theme body {
        background-color: #121212;
        color: #e0e0e0;
    }

    .dark-theme .chatButton {
        background-color: #333333;
        color: #e0e0e0;
    }

    .dark-theme .chatButton:hover, .dark-theme .moreMenu .option:hover {
        background-color: rgb(122 122 122 / 14%);
        color: #e0e0e0;
    }

    .dark-theme .messageReceived {
        background-color: #1c1c1c;
        color: #e0e0e0;
    }

    .dark-theme .status .count {
        color: #FFFFFF;
    }

    .dark-theme div[class~="onTop"] > .count {
        background: #FFF;
        color: #419fd9;
    }





    .dark-theme .replyBar,.dark-theme .leftPanel {
        background-color: #333333;
    }

    .dark-theme .replyBar > .replyMessage {
        border: none;
    }

    .dark-theme .otherTools > .toolButtons:hover {

        color: #FFFFFF;
        background: transparent;
    }

    .dark-theme .tbButton:hover, .otherTools .toolButtons:hover {
        color: #FFFFFF;
        background: transparent;
    }

    .dark-theme .go-back {
        background: #419fd9;
        color: #FFFFFF;
    }

    .dark-theme .moreMenu, .dark-theme .moreMenu > button {
        color: #FFFFFF;
        background: #333333;
    }

    .dark-theme .emojiList .pick:hover {
        background-color: #333333;
    }

    .dark-theme .chats {
        background: #333333;
    }

    .dark-theme .rightPanel > .topBar , .dark-theme .replyBar > form>label:hover{
        background: #333333;
        color: #FFFFFF;
    }

    .dark-theme .rightPanel > .rightSide > button {
        background: black;
    !important;
    }

    .dark-theme .menu {
        background: #333333;
    }

    .dark-theme .leftSide > .chatName {
        color: #FFFFFF;
        font-weight: 500;
    }

    .dark-theme * > button:hover {
        color: #FFFFFF;
        background: #a5a0a033;
    }

    .dark-theme * > button {
        color: #888;
        background: #333333;
        border: 0;
    }

    .dark-theme .otherTools > .toolButtons {
        font-size: 18px;
        background: #333333;
        border: 0;
    }


    .dark-theme button, input[type="search"], input[type="text"] {
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

    .dark-theme .replyBar > .attach {
        background: #333333;
    !important;
        border: 0;
    }

    .dark-theme * > input {
        color: #FFFFFF;
        background: #333333;
        border: 1px solid #333333;
    }

    .dark-theme header {
        color: #FFFFFF;
        background: #333333;
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

    .dark-theme * > input:focus {
        color: #FFFFFF;
        background: #333333;
        border: 1px solid #090909;
        box-shadow: rgba(0, 0, 0, 0.17) 0px -23px 25px 0px inset, rgba(0, 0, 0, 0.15) 0px -36px 30px 0px inset, rgba(0, 0, 0, 0.1) 0px -79px 40px 0px inset, rgba(0, 0, 0, 0.06) 0px 2px 1px, rgba(0, 0, 0, 0.09) 0px 4px 2px, rgba(0, 0, 0, 0.09) 0px 8px 4px, rgba(0, 0, 0, 0.09) 0px 16px 8px, rgba(0, 0, 0, 0.09) 0px 32px 16px;
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

    button, input[type="search"], input[type="text"] {
        border: none;
        outline: none;
    }

    input[type="checkbox"] {
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

    .switchMobile {
        position: absolute;
        width: 65%;
        height: auto;
        padding: 10px;
        background: #FFF;
        top: 75px;
        left: 0px;
        right: 0px;
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

    .menuWrap {
        position: absolute;
        width: 30%;
        min-width: 240px;
        max-width: 320px;
        height: 100%;
        z-index: 3;
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
        background: #FFF url({{ asset('storage/' .  Auth::user()->profile_picture) ?  : 'asd'  }}) no-repeat center;
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
        height: 500px;
        overflow-y: auto;
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

    nav button > i {
        color: #999;
        float: left;
    }

    nav button > span {
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

    .check > .tracer {
        width: 16px;
        height: 16px;
        background: #FFF;
        border: 2px solid #BBB;
        border-radius: 100%;
        float: left;
        margin-top: -3px;
        transition: all 0.2s ease-in-out;
    }

    #checkNight, #deskNotif, #showSName, #showPreview, #playSounds {
        display: none;
    }

    .toggleTracer:checked ~ .check {
        background: #419fd9;
    }

    .toggleTracer:checked ~ .check > .tracer {
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



    .leftPanel {
        position: relative;
        display: inline-block;
        left: 0px;
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
        margin: 20px 15px;
        margin-bottom: 0px;
        color: #BBB;
        cursor: pointer;
        opacity: 0.6;
        background: none;
    }

    .trigger:hover {
        opacity: 1;
    }

    .trigger > svg {
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
        overflow-y: scroll;
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

    .active, .active:hover {
        color: #FFF;
        background: #419fd9;
    }

    .chatInfo {
        display: flex;
    }
    .chatInfo_p {
        padding-top: 20px;
        padding-right: 10px ;
    }

    .chatInfo .image {
        width: 55px;
        height: 55px;
        background-size: cover;
        border-radius: 100%;
        margin: 13px 13px;
    }

    .chatInfo .my-image {
        background-image: url({{ asset('storage/' . Auth::user()->profile_picture) }});
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

    div[class~="normal"] > .fixed {
        display: none;
    }

    div[class~="normal"] > .count {
        right: 10px;
        bottom: 7px;
    }

    div[class~="onTop"] > .fixed {
        display: block;
        width: 24px;
        height: 24px;
        fill: #FFF;
    }

    div[class~="onTop"] > .count {
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
        float: left;
        width: calc(100% - 321px);
        height: 100vh;
        background: #999;
    }

    .rightPanel .topBar {
        position: relative;
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

    .go-back > svg {
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
        color: #FFFFFF;
        font-weight: 600;
        cursor: default;
    }

    .chatName > span {
        font-weight: 600;
        font-size: 12px;
        color: #FFFFFF;
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

    .tbButton, .otherTools .toolButtons {
        width: 50px;
        height: 50px;
        margin-top: 10px;
        background: #FFF;
        color: #888;
        border-radius: 100%;
    }

    .tbButton:hover, .otherTools .toolButtons:hover {
        color: #555;
        background: #DDD;
    }



    .convHistory::-webkit-scrollbar {
        display: none;
    }

    .convHistory {
        float: left;
        position: relative;
        width: 100%;
        height: calc(100vh - 140px);
        background: #333;
        overflow-x: hidden;
        overflow-y: auto;
    }

    .msg {
        overflow-wrap: break-word;
        word-wrap: break-word;
        white-space: normal;
        margin-bottom: 10px;
        padding: 10px;
        border-radius: 10px;
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
        padding: 15px;
        padding-bottom: 25px;
        margin: 20px 15px;
        margin-bottom: 0px;
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
        background: #8774E1;
    }

    .messageSent > .timestamp, .messageSent > .readStatus {
        bottom: 3px;
        color: darkgreen;
        user-select: none;
    }

    .messageSent > .readStatus {
        position: absolute;
        bottom: 10px;
        right: 12px;
    }



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

   .dark-theme .attach{
        background: #333;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .dark-theme .replyBar>form>label:hover{
        background: #333;
        display: flex;
        justify-content: center;
        align-items: center;
    }



    .replyBar .fa-paperclip {
        font-size: 32px;
    }

    .replyBar .replyMessage {
        width: calc(100% - 220px);
        float: left;
        height: 70px;
        padding: 0px 8px;
        font-size: 16px;
    }

    .replyBar .otherTools {
        float: right;
        width: 120px;
        height: 70px;
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

    .emojiList, .stickerList {
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


    .stickerList {
        display: none;
    }


    .search-modal {
        display: none;
        background-color: rgba(0,0,0,0.4);
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

    @media screen and (max-width: 1180px) {
        .config {
            width: 60%;
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

        .chatName > span {
            display: none;
            font-weight: 600;
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
        }

        .msg {
            width: auto;
            max-width: 60%;
        }



        .profile .edit {
            margin-left: 5px;
        }

        .searchChats,
        .search,
        .chatButton > .name,
        .chatButton > .message,
        .chatButton > .status {
            display: none;
        }
    }

    @media screen and (max-width: 480px) {
        .go-back {
            display: block;
        }

        .msg {
            padding: 12px;
            padding-bottom: 36px;
            border-radius: 7px;
            clear: both;
        }
    }

    @media screen and (max-width: 1497px) {
        .msg {
            width: 42%;
            max-width: 60%;
        }
    }

    @keyframes profile_upload {

    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const themeToggleCheckbox = document.getElementById('checkNight');
        const bodyElement = document.body;

        const currentTheme = localStorage.getItem('theme') || 'light';
        if (currentTheme === 'dark') {
            bodyElement.classList.add('dark-theme');
            themeToggleCheckbox.checked = true;
        } else {
            themeToggleCheckbox.checked = false;
        }

        themeToggleCheckbox.addEventListener('change', () => {
            if (themeToggleCheckbox.checked) {
                bodyElement.classList.add('dark-theme');
                localStorage.setItem('theme', 'dark');
            } else {
                bodyElement.classList.remove('dark-theme');
                localStorage.setItem('theme', 'light');
            }
        });



        document.querySelector(".settings").addEventListener("click", function () {
            document.querySelector(".config").style.opacity = "1";
            document.querySelector(".config").style.right = "0px";
            document.querySelector(".menuWrap").style.display = "none";
            document.querySelector(".menu").style.opacity = "0";
            document.querySelector(".menu").style.left = "-320px";
        });

        document.querySelector(".deskNotif").addEventListener("click", function () {
            const elements = [".showSName", ".showPreview", ".playSounds"];
            elements.forEach(function (selector) {
                const elem = document.querySelector(selector);
                elem.style.display = (elem.style.display === "none" || !elem.style.display) ? "block" : "none";
            });
        });



        document.querySelector(".otherOptions").addEventListener("click", function () {
            const moreMenu = document.querySelector(".moreMenu");
            moreMenu.style.display = (moreMenu.style.display === "none" || !moreMenu.style.display) ? "block" : "none";
        });

        document.querySelector(".search").addEventListener("click", function () {
            document.querySelector(".searchChats").focus();
        });

        document.querySelector(".emoji").addEventListener("click", function () {
            const emojiBar = document.querySelector(".emojiBar");
            emojiBar.style.display = (emojiBar.style.display === "none" || !emojiBar.style.display) ? "block" : "none";
        });

        document.querySelectorAll(".convHistory, .replyMessage").forEach(function (element) {
            element.addEventListener("click", function () {
                document.querySelector(".emojiBar").style.display = "none";
            });
        });
    });


    document.addEventListener('DOMContentLoaded', function () {
        const convHistory = document.querySelector('.convHistory');

        function scrollToBottom() {
            convHistory.scrollTop = convHistory.scrollHeight;
        }

        scrollToBottom();

        function addMessage(messageHtml) {
            convHistory.innerHTML += messageHtml;
            scrollToBottom();
        }

    });

    const searchInput = document.getElementById('searchInput');
    const searchModal = document.getElementById('searchModal');
    const closeSearchModal = document.getElementById('closeSearchModal');
    const resultsContainer = document.getElementById('searchResults');
    const chatsContainer = document.getElementById('chatsContainer');
    const menuIcon = document.getElementById('menuIcon');
    const closeIcon = document.getElementById('closeSearchModal');

    searchInput.addEventListener('focus', function () {
        chatsContainer.style.display = 'none';
        menuIcon.style.display = 'none';
        closeIcon.style.display = 'block';
    });

    searchInput.addEventListener('blur', function () {
        if (this.value === '') {
            chatsContainer.style.display = 'block';
            menuIcon.style.display = 'block';
            closeIcon.style.display = 'none';
        }
    });

    closeSearchModal.onclick = function() {
        searchModal.style.display = "none";
        chatsContainer.style.display = 'block';
        menuIcon.style.display = 'block';
        closeIcon.style.display = 'none';
        document.getElementById('searchInput').value = "";
    }

    window.onclick = function(event) {
        if (event.target === searchModal) {
            searchModal.style.display = "none";
            chatsContainer.style.display = 'block';
            menuIcon.style.display = 'block';
            closeIcon.style.display = 'none';
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

                    if (data.length > 0) {
                        data.forEach(user => {
                            const userElement = document.createElement('div');
                            userElement.classList.add('user-item');
                            userElement.textContent = user.name;

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
                                        image_url: null
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
                        resultsContainer.innerHTML = '<p>No users found !</p>';
                    }
                    searchModal.style.display = "block";
                })
                .catch(error => {
                    console.error('Error during user search:', error);
                });
        } else {
            resultsContainer.innerHTML = '';
            searchModal.style.display = "none";
        }
    });

    let mouseInside = false;
    const userId = {{ auth()->user()->id }};
    const otherUserId = {{ $selectedChat->user_one == auth()->id() ? $selectedChat->user_two : $selectedChat->user_one }};  // The other user's ID

    document.body.addEventListener('mouseenter', function() {
        console.log('Mouse entered the body - User is active');
        mouseInside = true;
    });

    document.body.addEventListener('mouseleave', function() {
        console.log('Mouse left the body - User is inactive');
        mouseInside = false;
    });

    function updateUserStatus(isActive) {
        const data = {
            active: isActive,
            user_id: userId,
        };

        fetch('/update-user-status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            body: JSON.stringify(data),
        })
            .then(response => response.json())
            .then(result => {
                console.log('User status updated:', result);
            })
            .catch(error => {
                console.error('Error sending user status:', error);
            });
    }

    setInterval(function() {
        if (mouseInside) {
            console.log('User is still active.');
            updateUserStatus(true);
        } else {
            console.log('User is inactive.');
            updateUserStatus(false);
        }
    }, 5000);

    setInterval(() => {
        fetch(`/users/${otherUserId}/status`)
            .then(response => response.json())
            .then(data => {
                const chatStatusElement = document.querySelector('.chatStatus , .chatStatusoffline');
                if (data.active) {
                    chatStatusElement.textContent = 'online';
                    chatStatusElement.classList.remove('chatStatusoffline');
                    chatStatusElement.classList.add('chatStatus');
                } else {
                    chatStatusElement.textContent = 'offline';
                    chatStatusElement.classList.add('chatStatusoffline');
                    chatStatusElement.classList.remove('chatStatus');
                }
            })
            .catch(error => {
                console.error('Error fetching user status:', error);
            });
    }, 5000);




</script>




