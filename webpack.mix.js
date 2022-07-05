const mix = require('laravel-mix');

 mix.js('resources/js/app.js', 'public/js')
 .vue()
 .sass('resources/sass/defaults.scss', 'public/css')
 .sass('resources/sass/darkTheme.scss', 'public/css')
 .sass('resources/sass/lightTheme.scss', 'public/css')
 .sass('resources/sass/home.scss', 'public/css')
 .sass('resources/sass/signup.scss', 'public/css')
 .sass('resources/sass/login.scss', 'public/css')
 .sass('resources/sass/messages.scss', 'public/css')
 .sass('resources/sass/preferences.scss', 'public/css')
 .sass('resources/sass/ProfileOverview.scss', 'public/css')
 .sass('resources/sass/profiles.scss', 'public/css')
 .sass('resources/sass/errors.scss', 'public/css')
 .sass('resources/sass/peopleSearch.scss', 'public/css')
 .sass('resources/sass/profileView.scss', 'public/css')
 .sass('resources/sass/contactRequestsList.scss', 'public/css')
 .sass('resources/sass/ContactBlockRemoveAlert.scss', 'public/css')
 .sass('resources/sass/messagesScreen.scss', 'public/css')
 .sass('resources/sass/SecuritySettings.scss', 'public/css')
 .sass('resources/sass/notifications.scss', 'public/css')
 .sass('resources/sass/TwoFaChallenge.scss', 'public/css')
 .sass('resources/sass/ConversationsView.scss', 'public/css');


mix.disableNotifications();