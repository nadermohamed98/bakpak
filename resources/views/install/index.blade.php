@php

$str = Str::random(21);
session()->put('hash_token', $str);

$php_version_success = false;
$mysql_success = false;
$curl_success = false;
$gd_success = false;
$allow_url_fopen_success = false;
$timezone_success = true;

$php_version_required_min = '8.0.2';
$php_version_required_max = '9.0';
$current_php_version = phpversion();

//check required php version
if (floatval($current_php_version) >= floatval($php_version_required_min) && floatval($current_php_version) < $php_version_required_max):
    $php_version_success=true;
    endif;

    //check mySql
    if (function_exists('mysqli_connect')):
    $mysql_success=true;
    endif;

    //check curl
    if (function_exists('curl_version')):
    $curl_success=true;
    endif;

    //check gd
    if (extension_loaded('gd') && function_exists('gd_info')):
    $gd_success=true;
    endif;

    //check allow_url_fopen
    if (ini_get('allow_url_fopen')):
    $allow_url_fopen_success=true;
    endif;

    //check allow_url_fopen
    $timezone_settings=ini_get('date.timezone');
    if ($timezone_settings):
    $timezone_success=true;
    endif;

    //check if all requirement is success
    if ($php_version_success && $mysql_success && $curl_success && $gd_success && $allow_url_fopen_success):
    $all_requirement_success=true;
    else:
    $all_requirement_success=false;
    endif;

    if (strpos(php_sapi_name(), 'cli' ) !==false || defined('LARAVEL_START_FROM_PUBLIC')):
    $writeable_directories=['../app', '../routes' , '../resources' , '../public' , '../storage' , '../.env' , '../bootstrap/cache' ];
    else:
    $writeable_directories=['./app', './routes' , './resources' , './public' , './storage' , '.env' , './bootstrap/cache' ];
    endif;

    foreach ($writeable_directories as $value):
    if (!is_writeable($value)):
    $all_requirement_success=false;
    endif;
    endforeach;

    @endphp
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="SpaGreen">

        <title>BakPak</title>

        <link rel="shortcut icon" href="{{ static_asset('images/default/favicon/favicon-96x96.png') }}">

        <link rel='stylesheet' type='text/css' href="{{ static_asset('install/bootstrap/css/bootstrap.min.css') }}" />
        <link rel='stylesheet' type='text/css'
            href="{{ static_asset('install/js/font-awesome/css/font-awesome.min.css') }}" />

        <link rel='stylesheet' type='text/css' href="{{ static_asset('install/css/install.css?ver=1.0.0') }}" />
        <!--====== Color CSS ======-->
        <link rel="stylesheet" href="{{ static_asset('frontend/css/theme/green.css') }}">
    </head>

    <body>
        <div class="install-box">

            <div class="panel-heading panel-install">
                <div class="text-center">
                    @if (config('app.mobile_mode') == 'on')
                    <h2>Connection failed | BakPak </h2>
                    @else
                    <h2>Connection failed</h2>
                    @endif

                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
        <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

        <script>
            window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

            axios.defaults.withCredentials = true;

            $(document).ready(function() {
                var $preInstallationTab = $("#pre-installation-tab");
                var $configurationTab = $("#configuration-tab");

                $(".form-next").on('click', function() {
                    if ($preInstallationTab.hasClass("active")) {
                        $preInstallationTab.removeClass("active");
                        $configurationTab.addClass("active");
                        $("#pre-installation").find("i").removeClass("fa-circle-o").addClass("fa-check-circle");
                        $("#configuration").addClass("active");
                        $("#host").focus();
                    }
                });

                $(document).on('submit', '#config-form', function(e) {
                    e.preventDefault();
                    $('#error_m').addClass('hide');
                    $('#success_m').addClass('hide');
                    $("input").removeClass('error_border');
                    $("#config-form strong").text('');
                    let selector = this;
                    let url = $(selector).attr('action');
                    let method = $(selector).attr('method');
                    $('.button-text').addClass('hide');
                    $('.loader').removeClass('hide');
                    $('.form_submitter').addClass('disable_btn');
                    let formData = new FormData(selector);

                    $.ajax({
                        method: method,
                        url: url,
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            if (response.success) {
                                console.log('first succes');
                                $.ajax({
                                    method: 'GET',
                                    url: response.route,
                                    data: formData,
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    success: function(response) {
                                        $('#success_m').removeClass('hide').text(
                                            response.success);
                                        window.location.href = response.route;
                                    },
                                });
                            } else {
                                $('.button-text').removeClass('hide');
                                $('.loader').addClass('hide');
                                $('.form_submitter').removeClass('disable_btn');
                                $('#error_m').removeClass('hide').text(response.error);
                            }
                        },
                        error: function(error) {
                            $('.button-text').removeClass('hide');
                            $('.loader').addClass('hide');
                            $('.form_submitter').removeClass('disable_btn');

                            if (error.status == 422) {
                                let errors = error.responseJSON.errors;
                                let error_length = Object.keys(error.responseJSON.errors);

                                for (let i = 0; i < error_length.length; i++) {
                                    $('input[name = ' + error_length[i] + ']').addClass(
                                        'error_border');
                                    $('#' + error_length[i] + '_error').text(errors[error_length[i]]
                                        [0]);
                                }
                            }
                        }
                    })
                });
            });
        </script>
    </body>

    </html>