<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>MOZAIC Minimarket</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('resources/assets/img/logo_baru_mozaic/logo baru set-09.png') }}" />
        <!-- Font Awesome icons (free version)-->
        <script src="https://use.fontawesome.com/releases/v5.15.3/js/all.js" crossorigin="anonymous"></script>
        <!-- Google fonts-->
        <link rel="preconnect" href="https://fonts.gstatic.com" />
        <link href="https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&amp;display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400;1,500;1,700&amp;display=swap" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="{{ asset('resources/css/styles.css') }}" rel="stylesheet" />
    </head>
    <body>
        <!-- Background Video-->
        <img class="bg-video" src="{{asset('resources/assets/img/kasir2.jpg')}}" ></img>
        <!-- Masthead-->
        <div class="masthead">
            {{-- <img src="{{asset('resources/assets/img/logo_baru_mozaic/logo baru set-09.png')}}" id="logo" style="width: 130px; height: 130px; position:absolute; top: 0; left:0;"> --}}
            <div class="masthead-content text-white">
                <div class="container-fluid px-4 px-lg-0">
                    {{-- <img src="{{asset('resources/img/logosmart/logosmart5-05crop.png')}}" id="logo" style="width: 400px; height: 100px;"> --}}
                    <div class="d-flex align-items-center">
                        <img src="{{asset('resources/assets/img/logo_baru_mozaic/logo baru set-09.png')}}" id="logo" style="width: 115px; height: 115px;" class="mt-2">
                        <div>
                            <h1 class="fst-italic lh-1 mb-2">MOZAIC</h1>
                            <div class="mb-3">Minimarket Offline</div>
                        </div>
                    </div>
                    <form>
                        <!-- Email address input-->
                        <div class="row input-group-newsletter">
                            {{-- <div class="col-auto"><a href="{{ route('register') }}" class="btn btn-primary">Daftar</a></div> --}}
                            <div class="col-auto" href="{{ route('login') }}" ><a href="{{ route('login') }}" class="btn btn-primary" style="margin-top:30px;">Masuk</a></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div style="position:absolute; bottom: 0; right:0;">
            <a>www.ciptasolutindo.id</a>
        </div>
        <!-- Social Icons-->
        <!-- For more icon options, visit https://fontawesome.com/icons?d=gallery&p=2&s=brands-->
        <div class="social-icons">
            <div class="d-flex flex-row flex-lg-column justify-content-center align-items-center h-100 mt-3 mt-lg-0">
                <!-- <a class="btn btn-dark m-3" href="#!"><i class="fab fa-twitter"></i></a>
                <a class="btn btn-dark m-3" href="#!"><i class="fab fa-facebook-f"></i></a>
                <a class="btn btn-dark m-3" href="#!"><i class="fab fa-instagram"></i></a> -->
            </div>
        </div>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="{{ asset('resources/js/scripts.js') }}"></script>
        <!-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *-->
        <!-- * *                               SB Forms JS                               * *-->
        <!-- * * Activate your form at https://startbootstrap.com/solution/contact-forms * *-->
        <!-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *-->
        <script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
    </body>
</html>
