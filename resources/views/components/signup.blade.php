<div class="signup-container">
    <div class="signup-box">
        <div class="signup-form-container">
            <form action="{{ route('create.account') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <h1 class="form-title">tuba</h1>
                <div class="form-inputs">
                    <input type="text" name="fname" value="{{ old('fname') }}" autofocus autocomplete="false" placeholder="First Name*">
                    <input type="text" name="sname" value="{{ old('sname') }}" autocomplete="false" placeholder="Surname*">
                </div>
                <div class="form-inputs">
                    <input type="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" name="email" value="{{ old('email') }}" autocomplete="false" placeholder="Email Address*">
                    <input type="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" name="email_confirmation" autocomplete="false" placeholder="Confirm Email*">
                </div>
                <div class="form-inputs">
                    <input type="password" name="password" autocomplete="false" placeholder="Password*">
                    <input type="password" name="password_confirmation" autocomplete="false" placeholder="Confirm Password*">
                </div>
                <div class="form-inputs">
                    <input type="date" name="birthdate" autocomplete="false" min="{{ $carbon::now()->subYears(120)->subMonths($months)->format('Y-m-d') }}" max="{{ $carbon::now()->subYears(15)->format('Y-m-d') }}" value="{{ $carbon::now()->subYears(18)->format('Y-m-d') }}">
                    <select name="gender">
                        <option value="female">female</option>
                        <option value="male">male</option>
                    </select>
                </div>
                <button name="signup">Create Account</button>
            </form>
        </div>
        <div class="signup-media-container">
            <img src="{{ asset('/storage/locals/signup.jpg') }}" alt="Tuba connects you with your online friends with just an account!">
        </div>
    </div>
</div>