<section class="menuWrap">
    <div class="menu">
        <div class="me userBg">
            <div class="image"></div>

            <div class="myinfo">
                <p class="name">{{ Auth::user()->name }}</p>
                <p class="phone">{{ Auth::user()->id }}</p>
            </div>

            <button class="cloud">
                <i class="fas fa-bookmark"></i>
            </button>

            <button class="settings">
                <i class="fas fa-cog"></i>
            </button>
        </div>
        <nav>
            <button class="ng">
                <i class="fas fa-users"></i>
                <span>New Group</span>
            </button>

            <button class="nc">
                <i class="fas fa-broadcast-tower"></i>
                <span>New Channel</span>
            </button>

            <button class="cn">
                <i class="fas fa-address-book"></i>
                <span>Contacts</span>
            </button>

            <button class="cl">
                <i class="fas fa-history"></i>
                <span>Calls History</span>
            </button>

            <a href="https://telegram.org/faq" target="_blank">
                <button class="faq">
                    <i class="fas fa-question-circle"></i>
                    <span>FAQ and Support</span>
                </button>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="lo">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Log Out</span>
                </button>
            </form>


            <button class="lo">
                <i class="fas fa-moon"></i>
                <span>Dark Theme</span>
                <input type="checkbox" id="checkNight" class="toggleTracer theme-toggle">
                <label style="left: 90px;" class="check DarkThemeTrigger" for="checkNight">
                    <div class="tracer"></div>
                </label>
            </button>
        </nav>

        <div class="info">
            <p>Telegram Web</p>
            <p>Ver 0.0.2 - <a href="https://en.wikipedia.org/wiki/Telegram_(messaging_service)">About</a></p>
            <p>Layout coded by: <a href="https://www.github.com/Hasanboyman">Hasanboy</a></p>
        </div>
    </div>
</section>
<!-- CONVERSATION OPTIONS MENU -->
<div class="moreMenu">
    <button class="option about">See Info</button>
    <button class="option notify">Disable Notifications</button>
    <button class="option block">Block User</button>
</div>

<section class="switchMobile">
    <p class="title">Mobile Device Detected</p>
    <p class="desc">Switch to the mobile app for a better performance.</p>
    <a href="https://play.google.com/store/apps/details?id=org.telegram.messenger&hl=pt_BR&gl=US">
        <button class="okay">OK</button>
    </a>
</section>

<section class="config">
    <section class="configSect">
        <div class="profile">
            <p class="confTitle">Settings</p>
            <div class="image">
                <form method="POST" action="{{ route('profile.picture.update') }}" enctype="multipart/form-data"
                      style="display: flex; position: relative; top: 90px; justify-content: center;">
                    @csrf
                    @method('PUT')

                    <!-- Hidden file input -->
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*" style="display: none;">

                    <!-- Label that triggers file input -->
                    <label for="profile_picture" class="changePic" style="cursor: pointer;">
                        <i class="fa-solid fa-camera"
                           style="font-size: 30px; top: 13px; rotate: 180deg; left: 56px; position: relative; color: #ffffff;">
                        </i>
                    </label>

                    <!-- Submit button to trigger form submission -->
                    <button type="submit" style="display: none;" id="submitForm">Submit</button>
                </form>
            </div>
            <div class="side">
                <p class="name">{{ Auth::user()->name }}</p>
                <p><span class="blue phone">+1 12 1234 5678</span></p>
                <p><span class="blue username">@Nobody_know</span></p>
            </div>
            <div style="display: flex">

            </div>
        </div>
    </section>

    <section class="configSect second">

        <!-- PROFILE INFO SECTION -->
        <p class="confTitle">Your Info</p>

        <div class="information">
            <ul>
                <li>Phone Number: <span class="blue phone">+1 12 1234 5678</span></li>
                <li>Username: <span class="blue username">@USERNAME</span></li>
                <li>Profile: <span class="blue">https://t.me/USERNAME</span></li>
            </ul>
        </div>




    </section>
</section>

<section class="overlay"></section>

<script>
    document.getElementById('profile_picture').addEventListener('change', function() {
        document.getElementById('submitForm').click();
    });

    document.querySelector('.overlay').addEventListener('click',function (){
        document.querySelector(".config").style.opacity = "0";
        document.querySelector(".overlay").style.display = "none";
        document.querySelector(".menuWrap").style.display = "none";
    })

    document.querySelector(".bars_settings").addEventListener("click", function () {
        document.querySelector(".overlay").style.display = "block";
        document.querySelector(".menuWrap").style.display = "block";
        document.querySelector(".menu").style.opacity = "1";
        document.querySelector(".menu").style.left = "0px";
    });
</script>
