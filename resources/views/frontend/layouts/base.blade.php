<!DOCTYPE html>
<html lang="{{ systemLanguage() ? systemLanguage()->locale : 'en' }}"
      dir="{{ systemLanguage() ? systemLanguage()->text_direction : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta name="paginate" content="{{ setting('paginate') }}"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | {{ setting('system_name') != '' ? setting('system_name') : 'OvoyLMS' }}</title>

    <!-- SEO -->
    <meta name="title" content="{{ $meta['meta_title'] }}"/>
    <meta name="description" content="{{ $meta['meta_description'] }}"/>
    <meta name="keywords" content="{{ $meta['meta_keywords'] }}"/>
    <meta property="article:published_time" content="{{ $meta['meta_published_time'] }}"/>
    <meta property="article:section" content="{{ $meta['meta_section'] }}"/>
    <!-- END SEO -->

    <!-- Open Graph -->
    <meta property="og:title" content="{{ $meta['meta_title'] }}"/>
    <meta property="og:description" content="{{ $meta['meta_description'] }}"/>
    <meta property="og:url" content="{{ $meta['meta_url'] }}"/>
    <meta property="og:type" content="{{ $meta['meta_section'] }}"/>
    <meta property="og:locale" content="{{ app()->getLocale() }}"/>
    <meta property="og:site_name" content="{{ setting('system_name') }}"/>
    <meta property="og:image" content="{{ $meta['meta_image'] }}"/>
    <meta property="og:image:size" content="{{ $meta['image_size'] }}"/>
    <!-- END Open Graph -->

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary"/>
    <meta name="twitter:title" content="{{ $meta['meta_title'] }}"/>
    <meta name="twitter:site" content="{{ $meta['meta_url'] }}"/>
    @php
        $icon = setting('favicon');
    @endphp

    @if ($icon)
        <link rel="apple-touch-icon" sizes="57x57"
              href="{{ $icon != [] && @is_file_exists($icon['image_57x57_url']) ? static_asset($icon['image_57x57_url']) : static_asset('images/default/favicon/favicon-57x57.png') }}">
        <link rel="apple-touch-icon" sizes="60x60"
              href="{{ $icon != [] && @is_file_exists($icon['image_60x60_url']) ? static_asset($icon['image_60x60_url']) : static_asset('images/default/favicon/favicon-60x60.png') }}">
        <link rel="apple-touch-icon" sizes="72x72"
              href="{{ $icon != [] && @is_file_exists($icon['image_72x72_url']) ? static_asset($icon['image_72x72_url']) : static_asset('images/default/favicon/favicon-72x72.png') }}">
        <link rel="apple-touch-icon" sizes="76x76"
              href="{{ $icon != [] && @is_file_exists($icon['image_76x76_url']) ? static_asset($icon['image_76x76_url']) : static_asset('images/default/favicon/favicon-76x76.png') }}">
        <link rel="apple-touch-icon" sizes="114x114"
              href="{{ $icon != [] && @is_file_exists($icon['image_114x114_url']) ? static_asset($icon['image_114x114_url']) : static_asset('images/default/favicon/favicon-114x114.png') }}">
        <link rel="apple-touch-icon" sizes="120x120"
              href="{{ $icon != [] && @is_file_exists($icon['image_120x120_url']) ? static_asset($icon['image_120x120_url']) : static_asset('images/default/favicon/favicon-120x120.png') }}">
        <link rel="apple-touch-icon" sizes="144x144"
              href="{{ $icon != [] && @is_file_exists($icon['image_144x144_url']) ? static_asset($icon['image_144x144_url']) : static_asset('images/default/favicon/favicon-144x144.png') }}">
        <link rel="apple-touch-icon" sizes="152x152"
              href="{{ $icon != [] && @is_file_exists($icon['image_152x152_url']) ? static_asset($icon['image_152x152_url']) : static_asset('images/default/favicon/favicon-152x152.png') }}">
        <link rel="apple-touch-icon" sizes="180x180"
              href="{{ $icon != [] && @is_file_exists($icon['image_180x180_url']) ? static_asset($icon['image_180x180_url']) : static_asset('images/default/favicon/favicon-180x180.png') }}">
        <link rel="icon" type="image/png" sizes="192x192"
              href="{{ $icon != [] && @is_file_exists($icon['image_192x192_url']) ? static_asset($icon['image_192x192_url']) : static_asset('images/favicon-192x192.png') }}">
        <link rel="icon" type="image/png" sizes="32x32"
              href="{{ $icon != [] && @is_file_exists($icon['image_32x32_url']) ? static_asset($icon['image_32x32_url']) : static_asset('images/default/favicon/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="96x96"
              href="{{ $icon != [] && @is_file_exists($icon['image_96x96_url']) ? static_asset($icon['image_96x96_url']) : static_asset('images/default/favicon/favicon-96x96.png') }}">
        <link rel="icon" type="image/png" sizes="16x16"
              href="{{ $icon != [] && @is_file_exists($icon['image_16x16_url']) ? static_asset($icon['image_16x16_url']) : static_asset('images/default/favicon/favicon-16x16.png') }}">
        <link rel="manifest" href="{{ static_asset('images/default/favicon/manifest.json') }}">

        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage"
              content="{{ $icon != [] && @is_file_exists($icon['image_144x144_url']) ? static_asset($icon['image_144x144_url']) : static_asset('images/default/favicon/favicon-144x144.png') }}">
    @else
        <link rel="shortcut icon" href="{{ static_asset('images/default/favicon/favicon-96x96.png') }}">
    @endif
    <!--====== Bootstrap CSS ======-->
    <link rel="stylesheet" href="{{ static_asset('frontend/css/bootstrap.min.css') }}">
    <!--====== Slick Slider ======-->
    <link rel="stylesheet" href="{{ static_asset('frontend/css/slick.min.css') }}">
    <!--====== Magnific ======-->
    <link rel="stylesheet" href="{{ static_asset('frontend/css/magnific-popup.min.css') }}">
    <!--====== Nice Select ======-->
    <link rel="stylesheet" href="{{ static_asset('frontend/css/select2.min.css') }}">
    <!--====== Nice Select ======-->
    <link rel="stylesheet" href="{{ static_asset('frontend/css/nice-select.min.css') }}">
    <!--====== Plyr CSS ======-->
    <link rel="stylesheet" href="{{ static_asset('frontend/css/plyr.css') }}">
    <!--====== Font Awesome ======-->
    <link rel="stylesheet" href="{{ static_asset('frontend/fonts/fontawesome/css/all.min.css') }}">
    <!--====== Box Icons ======-->
    <link rel="stylesheet" href="{{ static_asset('frontend/fonts/boxicons/css/boxicons.min.css') }}">
    <!--====== Spacing CSS ======-->
    <link rel="stylesheet" href="{{ static_asset('frontend/css/spacing.min.css') }}">
    <!--====== AOS CSS ======-->
    <link rel="stylesheet" href="{{ static_asset('frontend/css/aos.css') }}">
    <!--====== Main CSS ======-->
    <link rel="stylesheet" href="{{ static_asset('frontend/css/style.css') }}?v={{ setting('current_version') }}">
    {{-- <link rel="stylesheet" href="{{ static_asset('frontend/css/style.min.css') }}"> --}}
    <style>
        :root {
            --body-font: '{{ setting("body_font") }}', sans-serif;
            /* --body-font: '


        {{ setting("body_font_size") }}   ', sans-serif; */
            --header-font: '{{ setting("header_font") }}', sans-serif;
            /* --header-font: '


        {{ setting("header_font_size") }}   ', sans-serif; */
        }
        .custom-popup, .modal-content {
         max-width: 100% !important;
         top: 260px;
        }
    </style>
    <!--====== Responsive CSS ======-->
    <link rel="stylesheet" href="{{ static_asset('frontend/css/responsive.css') }}">
    {{-- <link rel="stylesheet" href="{{ static_asset('frontend/css/responsive.min.css') }}"> --}}
    <!--====== Color CSS ======-->
    <link rel="stylesheet" href="{{ static_asset('frontend/css/toastr.min.css') }}">
    @php
        $theme_color = setting('theme_color');
    @endphp
    @if ($theme_color)
        <link rel="stylesheet" href="{{ static_asset('frontend/css/theme/' . $theme_color . '.css') }}">
    @endif
    @stack('css')
    <style>
        @if (base64_decode(setting('custom_css')))
            {{ base64_decode(setting('custom_css')) }}
        @endif
    </style>

    @if (setting('is_google_analytics_activated') && setting('tracking_code'))
        {!! base64_decode(setting('tracking_code')) !!}
    @endif
    @if (setting('custom_header_script'))
        {!! base64_decode(setting('custom_header_script')) !!}
    @endif
    @if (setting('is_facebook_pixel_activated') && setting('facebook_pixel_id'))
        {!! base64_decode(setting('facebook_pixel_id')) !!}
    @endif

    <!--====== Google Fonts ======-->

    {!! font_link() !!}



    @if (setting('disable_preloader') != '1')
        <script type="text/javascript">
            window.addEventListener("load", function () {
                const preloader = document.querySelector(".preloader");
                preloader.classList.add("preloader-finish");
            });
        </script>
@endif

<body>
@if (setting('disable_preloader') != '1')
    <div class="preloader">
        <div class="loading">
            <img
                src="{{ setting('preloader_logo') && @is_file_exists(setting('preloader_logo')['original_image']) ? get_media(setting('preloader_logo')['original_image']) : get_media('images/default/logo/preloader.png') }}"
                alt="{{ setting('system_title') }}">
        </div>
    </div>
@endif
@yield('base.content')
@if (setting('is_facebook_messenger_activated') == 1)
    <div class="fb-customerchat" attribution=setup_tool page_id="{{ (int) setting('facebook_page_id') }}"
         theme_color="{{ setting('facebook_messenger_color') }}">
    </div>
@endif
<!--====== jQuery ======-->
<script src="{{ static_asset('frontend/js/jquery-3.6.0.min.js') }}"></script>
<!--====== Popper JS ======-->
<script src="{{ static_asset('frontend/js/popper.min.js') }}"></script>
<!--====== Bootstrap ======-->
<script src="{{ static_asset('frontend/js/bootstrap.min.js') }}"></script>
<!--====== Slick Slider ======-->
<script src="{{ static_asset('frontend/js/slick.min.js') }}"></script>
<!--====== Magnific ======-->
<script src="{{ static_asset('frontend/js/jquery.magnific-popup.min.js') }}"></script>
<!--====== Plyr JS ======-->
<script src="{{ static_asset('frontend/js/plyr.js') }}"></script>
<!--====== Nice Select ======-->
<script src="{{ static_asset('frontend/js/jquery.nice-select.min.js') }}"></script>
<!--====== Nice Select ======-->
<script src="{{ static_asset('frontend/js/select2.min.js') }}"></script>
<!--====== AOS JS ======-->
<script src="{{ static_asset('frontend/js/aos.js') }}"></script>
<!--====== Cookie Alert ======-->
<script src="{{ static_asset('frontend/js/cookiealert.js') }}"></script>
<!--====== Main JS ======-->
<script src="{{ static_asset('frontend/js/main.js') }}?v={{ setting('current_version') }}"></script>
<!--====== App JS ======-->
<script src="{{ static_asset('frontend/js/app.js') }}?v={{ setting('current_version') }}"></script>
@if (auth()->check() && auth()->user()->role_id > 1)
    <script src="{{ static_asset('admin/js/OneSignalSDK.js') }}" defer></script>
@endif
<!--============= toastr=======-->
<script src="{{ static_asset('frontend/js/toastr.min.js') }}"></script>

{!! Toastr::message() !!}
<script src="{{ static_asset('admin/js/sweetalert211.min.js') }}"></script>
@if (setting('is_pusher_notification_active') && auth()->check())
    <script src="{{ static_asset('admin/js/pusher.min.js') }}"></script>
    <script>
        const pusher = new Pusher('{{ setting('pusher_app_key') }}', {
            cluster: '{{ setting('pusher_app_cluster') }}',
            encrypted: true
        });

        const channel = pusher.subscribe('notification-send-{{ auth()->id() }}');
        channel.bind('App\\Events\\PusherNotification', (data) => {
            toastr[data.message_type](data.message);
        });
    </script>
@endif
@stack('js')
<script>
    $(document).ready(function () {
        $(document).on('click', '.list-groups a', function (e) {
            let name = $(this).data('name');
            let value = $(this).data('value');
            $(this).closest('.list-groups').find('li').removeClass('active');
            $(this).closest('li').addClass('active');
            $(this).closest('form').find('input[name="' + name + '"]').val(value);
        });
    });
    //facebook chat
    @if (setting('is_tawk_messenger_activated') == 1)
        var Tawk_API = Tawk_API || {},
            Tawk_LoadStart = new Date();
        (function () {
            var s1 = document.createElement("script"),
                s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/{{ setting('tawk_property_id') }}/{{ setting('tawk_widget_id') }}';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        })();
    @endif

    @if (setting('is_facebook_messenger_activated') == 1)
        window.fbAsyncInit = function () {
            FB.init({
                appId: 'facebook-developer-app-id',

                autoLogAppEvents: true,
                xfbml: true,
                version: 'v3.3'
            });
        };
        (function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {
                return;
            }
            js = d.createElement(s);
            js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    @endif

    @if (auth()->check() && auth()->user()->role_id > 1)
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('{{ static_asset('admin/js/OneSignalSDKWorker.js') }}')
                .then(function (registration) {
                    console.log('Service Worker registered with scope:', registration.scope);
                })
                .catch(function (error) {
                    console.error('Service Worker registration failed:', error);
                });
        }
        window.OneSignal = window.OneSignal || [];
        OneSignal.push(function () {
            OneSignal.init({
                appId: "{{ setting('onesignal_app_id') }}",
                safari_web_id: "{{ setting('safari_web_id') }}",
                notifyButton: {
                    enable: true,
                },
                serviceWorker: {
                    path: "{{ static_asset('admin/js/OneSignalSDKWorker.js') }}",
                }
            });
            OneSignal.on('subscriptionChange', function (isSubscribed) {
                if (isSubscribed) {
                    OneSignal.getUserId().then(function (userId) {
                        $.ajax({
                            url: '{{ route('onesignal.update-subscription') }}',
                            method: 'POST',
                            data: {
                                player_id: userId,
                                subscribed: 1
                            }
                        });
                    });
                }
            else{
                $.ajax({
                    url: '{{ route('onesignal.update-subscription') }}',
                    method: 'POST',
                    data: {
                        subscribed: 0
                    }
                });
            }
        });
    });
    @endif

    @if (auth()->check() && auth()->user()->user_type == 'student')
     $(document).ready(function () {
        let logoutTimer;
        let warningTimer;
        const timeoutDuration = 60 * 60 * 1000; // 60 minutes in milliseconds
        const warningDuration = 1 * 60 * 1000; 

        // Function to log out the user
        function logoutUser() {
            // Make sure the CSRF token is included in the POST request
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: '{{ route('student.logout') }}',
                type: 'POST',
                data: {
                    _token: csrfToken // Add CSRF token to the request
                },
                success: function () {
                    // Redirect to login page after successful logout
                    window.location.href = '/login?inactivity={{Str::uuid()}}';
                },
                error: function (xhr) {
                    console.error("Logout failed:", xhr.responseText);
                    // Redirect to login page as a fallback
                    window.location.href = '/login?inactivity={{Str::uuid()}}';
                }
            });
        }

        function showWarningDialog() {
            let countdown = 30; // Countdown starts from 30 seconds

            // Append the modal HTML to the body
            $('body').append(`
                <div id="inactivity-warning" class="modal fade" tabindex="-1" aria-labelledby="inactivityWarningLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="inactivityWarningLabel">Inactivity Warning</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p id="warning-message">You have been inactive for a while. You will be logged out in <span id="countdown">${countdown}</span> seconds. Do you want to stay logged in?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" id="stay-logged-in" class="template-btn" data-bs-dismiss="modal">Stay Logged In</button>
                            </div>
                        </div>
                    </div>
                </div>
            `);

            // Initialize and show the modal using Bootstrap's JavaScript API
            const modal = new bootstrap.Modal(document.getElementById('inactivity-warning'), {
                backdrop: 'static', // Disable clicking outside to close
                keyboard: false // Disable closing with the Esc key
            });

            modal.show();

            // Countdown logic
            const countdownElement = document.getElementById('countdown');
            const countdownInterval = setInterval(() => {
                countdown--; // Decrement countdown
                countdownElement.textContent = countdown; // Update countdown display

                // If countdown reaches 0, perform logout
                if (countdown <= 0) {
                    clearInterval(countdownInterval); // Stop the countdown
                    modal.hide(); // Hide the modal
                    $('#inactivity-warning').remove(); // Remove modal from DOM
                    logoutUser(); // Call logout function
                }
            }, 1000); // Interval is 1000ms (1 second)

            // Handle the Stay Logged In button click
            $('#stay-logged-in').on('click', function () {
                clearInterval(countdownInterval); // Stop the countdown
                modal.hide(); // Hide the modal
                $('#inactivity-warning').remove(); // Remove modal from DOM
                resetTimers(); // Reset timers to extend session
            });
        }



        // Reset both the warning and logout timers
        function resetTimers() {
            clearTimeout(logoutTimer);
            clearTimeout(warningTimer);

            // Set up the warning and logout timers again
            warningTimer = setTimeout(showWarningDialog, timeoutDuration - warningDuration);
            logoutTimer = setTimeout(logoutUser, timeoutDuration);
        }

        // Set up the initial inactivity timers
        resetTimers();

        // Reset timers on any user activity
        $(document).on('mousemove keypress click scroll', resetTimers);
    });
    @endif 
</script>

@if (setting('custom_footer_script'))
    {!! base64_decode(setting('custom_footer_script')) !!}
@endif
</body>

</html>
