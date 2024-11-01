<!-- Reply Bar -->
<div class="replyBar">
    <!-- Image Upload Form -->
    <form id="imageForm" action="{{ route('messages.send', $selectedChat->id) }}" method="POST" enctype="multipart/form-data" style="background: #333">
        <input type="file" id="fileInput" name="image_url" accept="image/*" hidden onchange="submitForm()">
        <label for="fileInput" class="attach">
            <i class="fas fa-paperclip"></i>
        </label>
    </form>

    <!-- Message Form -->
    <form id="messageForm" action="{{ route('messages.send', $selectedChat->id) }}" method="POST">
        @csrf
        <input type="text" class="replyMessage" id="replyMessage" name="message" placeholder="Type your message..." autofocus />
    </form>

    <!-- Emoji Bar -->
    <!-- Emoji Bar -->
    <div class="emojiBar">
        <div class="emoticonType">
            <button id="panelEmoji" type="button">Emoji</button>
            <button id="panelStickers" type="button">Stickers</button>
            <button id="panelGIFs" type="button">GIFs</button>
        </div>

        <!-- Emoji List -->
        <div class="emojiList">
            <button class="pick">😀</button>
            <button class="pick">😁</button>
            <button class="pick">😂</button>
            <button class="pick">🤣</button>
            <button class="pick">😃</button>
            <button class="pick">😄</button>
            <button class="pick">😅</button>
            <button class="pick">😆</button>
            <button class="pick">😉</button>
            <button class="pick">😊</button>
            <button class="pick">😋</button>
            <button class="pick">😎</button>
            <button class="pick">😍</button>
            <button class="pick">😘</button>
            <button class="pick">😗</button>
            <button class="pick">😙</button>
            <button class="pick">😚</button>
            <button class="pick">🙂</button>
            <button class="pick">🤗</button>
            <button class="pick">🤔</button>
            <button class="pick">😐</button>
            <button class="pick">😑</button>
            <button class="pick">😶</button>
            <button class="pick">🙄</button>
            <button class="pick">😏</button>
            <button class="pick">😣</button>
            <button class="pick">😥</button>
            <button class="pick">😮</button>
            <button class="pick">🤐</button>
            <button class="pick">😯</button>
            <button class="pick">😪</button>
            <button class="pick">😴</button>
            <button class="pick">😵</button>
            <button class="pick">🤯</button>
            <button class="pick">🤠</button>
            <button class="pick">😷</button>
            <button class="pick">🤒</button>
            <button class="pick">🤕</button>
            <button class="pick">🤑</button>
            <button class="pick">😈</button>
            <button class="pick">👿</button>
            <button class="pick">👹</button>
            <button class="pick">👺</button>
            <button class="pick">💀</button>
            <button class="pick">👻</button>
            <button class="pick">👽</button>
            <button class="pick">🤖</button>
            <button class="pick">💩</button>
            <button class="pick">👋</button>
            <button class="pick">👍</button>
            <button class="pick">👊</button>
            <button class="pick">✌️</button>
            <button class="pick">🤟</button>
            <button class="pick">👏</button>
            <button class="pick">🙌</button>
            <button class="pick">👐</button>
            <button class="pick">✋</button>
            <button class="pick">🤚</button>
            <button class="pick">🤝</button>
            <button class="pick">🙏</button>
        </div>
    </div>

    <div class="stickersList" style="display: none;">
        <button class="pick" data-sticker-url="https://media.giphy.com/media/3o7btY1wv76clYiwTe/giphy.gif">
            <img src="https://media.giphy.com/media/3o7btY1wv76clYiwTe/giphy.gif" alt="Sticker 1" style="width: 40px; height: 40px;">
        </button>
        <button class="pick" data-sticker-url="https://media.giphy.com/media/l3q2K5jinSpB7R4J2/giphy.gif">
            <img src="https://media.giphy.com/media/l3q2K5jinSpB7R4J2/giphy.gif" alt="Sticker 2" style="width: 40px; height: 40px;">
        </button>
        <button class="pick" data-sticker-url="https://media.giphy.com/media/3o7btNr40wHzzEtmVi/giphy.gif">
            <img src="https://media.giphy.com/media/3o7btNr40wHzzEtmVi/giphy.gif" alt="Sticker 3" style="width: 40px; height: 40px;">
        </button>
        <button class="pick" data-sticker-url="https://media.giphy.com/media/l4FGwHE6nNfyPVKb6/giphy.gif">
            <img src="https://media.giphy.com/media/l4FGwHE6nNfyPVKb6/giphy.gif" alt="Sticker 4" style="width: 40px; height: 40px;">
        </button>
        <button class="pick" data-sticker-url="https://media.giphy.com/media/xT5LMOYHSKQy6WwH4U/giphy.gif">
            <img src="https://media.giphy.com/media/xT5LMOYHSKQy6WwH4U/giphy.gif" alt="Sticker 5" style="width: 40px; height: 40px;">
        </button>
        <!-- Add more stickers as needed -->
    </div>

    <!-- Other Tools -->
    <div class="otherTools">
        <button class="toolButtons emoji">
            <i class="fas fa-smile"></i>
        </button>
        <button class="toolButtons audio">
            <i class="fas fa-microphone" style="position: absolute;" id="MicEmoji"></i>
            <i class="fa-sharp fa-solid fa-paper-plane fa-rotate-90" style="transform: rotate(40deg); color: #4c84dc; font-size: 20px; opacity: 0;" id="SendEmoji"></i>
        </button>
    </div>
</div>

<!-- JavaScript for Interactivity -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Toggle Emoji Bar
    document.querySelector(".toolButtons.emoji").addEventListener("click", function () {
        const emojiBar = document.querySelector(".emojiBar");
        emojiBar.style.display = emojiBar.style.display === "block" ? "none" : "block";
    });

    // Toggle Send and Microphone Icons
    document.getElementById("replyMessage").addEventListener("input", function () {
        const sendIcon = document.getElementById("SendEmoji");
        const micIcon = document.getElementById("MicEmoji");

        if (this.value.trim() !== "") {
            sendIcon.style.opacity = "1";
            micIcon.style.opacity = "0";
        } else {
            sendIcon.style.opacity = "0";
            micIcon.style.opacity = "1";
        }
    });

    // Submit Form or Send Emoji on Send Icon Click
    document.getElementById("SendEmoji").addEventListener("click", function () {
        const messageInput = document.getElementById("replyMessage");
        if (messageInput.value.trim() !== "") {
            document.getElementById("messageForm").submit();
        }
    });

    // Image Form Submission
    function submitForm() {
        var formData = new FormData(document.getElementById('imageForm'));
        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        $.ajax({
            type: 'POST',
            url: $('#imageForm').attr('action'),
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                console.log("Image uploaded successfully.");
            },
            error: function(xhr, status, error) {
                console.error("Image upload failed:", xhr.responseText);
            }
        });
    }

    // Emoji Insertion
    document.addEventListener("DOMContentLoaded", function () {
        const emojiButtons = document.querySelectorAll(".emojiList .pick");
        const messageInput = document.getElementById("replyMessage");

        emojiButtons.forEach(button => {
            button.addEventListener("click", function () {
                const emoji = this.innerHTML.trim();
                insertEmoji(emoji);
            });
        });

        function insertEmoji(emoji) {
            const cursorPosition = messageInput.selectionStart;
            const textBeforeCursor = messageInput.value.substring(0, cursorPosition);
            const textAfterCursor = messageInput.value.substring(cursorPosition);
            messageInput.value = textBeforeCursor + emoji + textAfterCursor;
            messageInput.selectionStart = messageInput.selectionEnd = cursorPosition + emoji.length;
            messageInput.focus();
        }
    });

    document.getElementById("panelStickers").addEventListener("click", function () {
        const emojiList = document.querySelector(".emojiList");
        const stickersList = document.querySelector(".stickersList");
        emojiList.style.display = "none";
        stickersList.style.display = "block";
    });

    function submitForm() {
        var formData = new FormData(document.getElementById('imageForm'));
        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        $.ajax({
            type: 'POST',
            url: $('#imageForm').attr('action'),
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                console.log("Image uploaded successfully.");
            },
            error: function(xhr, status, error) {
                console.error("Image upload failed:", xhr.responseText);
            }
        });
    }

</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


