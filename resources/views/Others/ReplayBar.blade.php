<div class="replyBar">
    <form id="mediaForm" action="{{ route('messages.send', $selectedChat->id) }}" method="POST" enctype="multipart/form-data" style="background: #333; flex-direction: column; width: 133px; display: none; position: absolute; bottom: 70px;">
        @csrf
        <input type="file" id="fileInput" name="media" accept="image/jpeg, image/png, image/jpg, image/gif, video/mp4, video/webm" hidden onchange="uploadFile('fileInput', 'media')">
        <label for="fileInput" class="attach">
            <i class="emoji fa-solid fa-image"></i>
            <div>
                <pre>Image or Video</pre>
            </div>
        </label>



        <input type="file" id="fileInput" name="file" accept=".zip" hidden onchange="uploadFile('fileInput', 'file')">
        <label for="fileInput" class="attach">
            <i class="emoji fa-solid fa-file"></i>
            <div>
                <pre>Document      </pre>
            </div>
        </label>

    <input  id="Input" hidden >
        <label for="Input" class="attach">
            <i class=" emoji fa-solid fa-wallet"></i>
            <div>
                <pre>Wallet        </pre>
            </div>
        </label>


    <input id="Input" hidden />
        <label for="Input" class="attach">
            <i class="emoji fa-solid fa-gift"></i>
            <div>
                <pre>Gift Premium  </pre>
            </div>
        </label>
    </form>



{{--    Message --}}
<div>
    <form id="messageForm" action="{{ route('messages.send', $selectedChat->id) }}" method="POST">
        @csrf
        <div id="share">
        <i class="emoji fas fa-paperclip" id="mediaForm" style="color: #888"></i>
        </div>
        <input type="text" class="replyMessage" id="replyMessage" name="message" placeholder="Type your message..." autofocus />
        <div class="otherTools">
            <button type="button" class="toolButtons emoji">
                <i class="fas fa-smile"></i>
            </button>

            <button type="button" class="toolButtons audio" id="audio">
                <i class="fas fa-microphone" style="position: absolute;" id="MicEmoji"></i>
                <div class="" id="recordingStatus"></div>
                <i class="fa-sharp fa-solid fa-paper-plane fa-rotate-90" style="transform: rotate(40deg); color: #4c84dc; font-size: 20px; opacity: 0;" id="SendEmoji"></i>
            </button>
        </div>

    </form>
    <div/>


    <div class="emojiBar">
        <div class="emoticonType">
            <button id="panelEmoji" type="button">Emoji</button>
            <button id="panelStickers" type="button">Stickers</button>
            <button id="panelGIFs" type="button">GIFs</button>
        </div>

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

    </div>

    <!-- Other Tools -->

</div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.querySelector(".toolButtons.emoji").addEventListener("click", function () {
            const emojiBar = document.querySelector(".emojiBar");
            emojiBar.style.display = emojiBar.style.display === "block" ? "none" : "block";
        });

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

        document.getElementById("SendEmoji").addEventListener("click", function () {
            const messageInput = document.getElementById("replyMessage");
            if (messageInput.value.trim() !== "") {
                console.log("Send button clicked. Message: " + messageInput.value);
                messageInput.value = ""; // Clear input for demonstration
            }
        });

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

        function simulateFileUpload() {
            console.log("Simulating file upload...");
            setTimeout(() => {
                console.log("File uploaded successfully (simulated).");
            }, 1000);
        }


        function submitForm() {
            const form = document.getElementById('mediaForm');
            form.submit();
        }

    </script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    let mediaRecorder;
    let audioChunks = [];
    let isRecording = false;
    const recordingStatus = document.getElementById('recordingStatus');
    const Micemoji = document.getElementById('audio');
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let startTime;

    Micemoji.addEventListener('click', async () => {
        if (!isRecording) {
            recordingStatus.textContent = "Recording...";
            console.log("Starting recording...");
            startTime = Date.now();

            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                mediaRecorder = new MediaRecorder(stream);
                audioChunks = [];

                mediaRecorder.ondataavailable = (event) => {
                    audioChunks.push(event.data);
                    console.log("Audio chunk available: ", event.data);
                };

                mediaRecorder.onstop = async () => {
                    const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                    const durationInSeconds = ((Date.now() - startTime) / 1000).toFixed(2);
                    console.log("Recording duration (calculated):", durationInSeconds + " seconds");

                    const formData = new FormData();
                    formData.append('audio', audioBlob, 'audio.webm');
                    formData.append('message', "Your message here");
                    formData.append('_token', csrf);

                    const response = await fetch(`/chats/{{ $selectedChat->id }}/message`, {
                        method: 'POST',
                        body: formData
                    });

                    if (response.ok) {
                        const jsonResponse = await response.json();
                        console.log('Audio sent successfully:', jsonResponse);
                    } else {
                        console.error('Error sending audio:', await response.text());
                    }

                    const audioElement = document.createElement('audio');
                    audioElement.src = URL.createObjectURL(audioBlob);
                    audioElement.controls = true;
                    document.body.appendChild(audioElement);

                    audioElement.addEventListener('loadedmetadata', () => {
                        const actualDuration = audioElement.duration;
                        console.log("Recorded audio duration from metadata:", actualDuration + " seconds");
                    });
                };

                mediaRecorder.start();
                isRecording = true;
                console.log("MediaRecorder started.");
            } catch (error) {
                console.error("Error accessing microphone: ", error);
                recordingStatus.textContent = "Error accessing microphone.";
            }
        } else {
            recordingStatus.textContent = "Stopping recording...";
            console.log("Stopping recording...");
            mediaRecorder.stop();
            isRecording = false;
        }
    });


    document.getElementById('share').addEventListener('click', function () {
        const mediaForm = document.getElementById('mediaForm');
        mediaForm.style.display = 'flex'
        event.stopPropagation();
    });
    document.addEventListener('click',function () {
        const mediaForm = document.getElementById('mediaForm');
        mediaForm.style.display = 'none'
    })

    function uploadFile(inputId, fileKey) {
        const fileInput = document.getElementById(inputId);
        const file = fileInput.files[0];

        if (!file) return;

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append(fileKey, file);

        fetch("/chats/{{ $selectedChat->id }}/message", {
                method: 'POST',
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log("File uploaded successfully!",data);
                    } else {
                        console.log("File upload failed.");
                    }
                })
                .catch(error => {
                console.log("An error occurred while uploading the file.");
            });
    }
</script>



<style>
    #messageForm{
        display: flex;
        align-items: center;
        justify-content: space-around;
    }
</style>
