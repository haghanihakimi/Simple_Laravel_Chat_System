<div class="login-container">
    <div class="login-box">
        <div class="login-form-container">
            <form action="{{ route('signin') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <h1 class="form-title">tuba</h1>
                <div class="username-box">
                    <input type="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" name="email" value="{{ old('email') }}" autofocus autocomplete="false" placeholder="Email Address">
                    <span>&nbsp;</span>
                </div>
                <div class="password-box">
                    <input type="password" pattern=".{8,}" name="password" autocomplete="false" placeholder="Password">
                    <span>&nbsp;</span>
                </div>
                <label class="rememberme-box">Stay Signed In
                    <input type="checkbox" name="rememberme" checked="checked">
                    <span class="checkmark"></span>
                </label>
                <button name="login">Login</button>
                <a href="#" target="_self">Forgotten account</a>
                @if (session('status'))
                    <p class="errors">
                        {!! session('status') !!}
                    </p>
                @endif
                @if (session('failure'))
                    <p class="errors">
                        {!! session('failure') !!}
                    </p>
                @endif
            </form>
        </div>
        <div class="login-media-container">
            <img src="{{ asset('/storage/locals/signin.jpg') }}" alt="Login to Tuba to view and send messages to your friends">
        </div>
    </div>
</div>