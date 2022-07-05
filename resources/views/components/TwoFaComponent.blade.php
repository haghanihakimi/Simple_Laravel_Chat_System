<div class="twoFa__container">
    <div class="twoFa__box">
        <form action="{{route('two-factor.login')}}" enctype="multipart/form-data" method="GET">
            @csrf
            <h1 class="form__heading">Please give us 6 digit code you find on your authenticator app or use one of your backup codes.</h1>
            <input type="text" name="code" placeholder="Enter 6 digit code" autocomplete="false" autofocus="true" required>
            <button type="submit" role="button">Authenticate</button>
            @error('code')
                <p class="twoFa__error">
                    {!! $message !!}
                </p>
            @enderror
            @if (session('status'))
                <p class="twoFa__error">
                    {!! session('status') !!}
                </p>
            @endif
        </form>
    </div>
</div>