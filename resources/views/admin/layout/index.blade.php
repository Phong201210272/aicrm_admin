<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
    <meta content="Giải pháp chăm sóc khách hàng tiết kiệm và hiệu quả trên Zalo - Zalo Notification Service"
        name="description" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
        integrity="sha384-DyZ88mC6Up2uqS0zUpUf2BwA6E81y/eK2snElDkC2DdAb5I8Tx1gfZDA1ibbhwYs" crossorigin="anonymous">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css.map') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fonts.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.css.map') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins.css.map') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/css/bootstrap-notify.css">
    <script src="{{ asset('validator/validator.js') }}"></script>

    <title>Hệ thống gửi tin nhắn tự động Zalo ZNS by SGO Việt Nam | AICRM</title>
</head>
<style>
    /* Đặt cấu trúc cơ bản cho header */
    .main-header {
        width: 100%;
        background-color: #fff;
        /* Hoặc màu bạn muốn */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        /* Đổ bóng nhẹ */
        position: relative;
        /* Đảm bảo header không bị mất khi cuộn */
        z-index: 1000;
        /* Đặt z-index cao để nó luôn hiển thị trên cùng */
    }

    /* Đảm bảo rằng logo và các nút trong header không bị ẩn */
    .logo-header,
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 15px;
        /* Thêm padding cho header */
    }

    /* Định dạng cho navbar */
    .navbar-nav {
        flex-direction: row;
        /* Đảm bảo các mục trong navbar xếp theo hàng */
    }

    .nav-item {
        display: block !important;
        /* Đảm bảo nó hiển thị */
        width: 100%;
        /* Chiếm toàn bộ chiều rộng */
    }

    /* Media query cho màn hình nhỏ */
    /* @media (max-width: 768px) {
        .navbar-nav {
            flex-direction: column !important;
            /* Đảm bảo các mục trong navbar xếp theo hàng */
        }

        .show-on-mobile {
            display: block !important;
            /* Đảm bảo hiển thị trên di động */
        }

        /* Đảm bảo các nút không bị ẩn */
        .nav-item {
            display: block !important;
            width: 100%;
            /* Đảm bảo chiếm toàn bộ chiều rộng */
        }
    } */

    .collapse {
        display: none;
    }

    .collapse.show {
        display: block;
    }

    #button-contact-vr {
        position: fixed;
        bottom: 0;
        z-index: 99999;
    }

    #button-contact-vr .button-contact .phone-vr {
        position: relative;
        visibility: visible;
        background-color: transparent;
        width: 90px;
        height: 90px;
        cursor: pointer;
        z-index: 11;
        -webkit-backface-visibility: hidden;
        -webkit-transform: translateZ(0);
        transition: visibility .5s;
        left: 0;
        bottom: 0;
        display: block;
    }

    #button-contact-vr .button-contact {
        position: relative;
        margin-top: -5px;
    }
</style>

<body>
    <div id="wrapper">
        @include('admin.layout.sidebar')

        <div class="main-panel">

            @include('admin.layout.header');
            <div class="container">
                <div id="button-contact-vr" class="">
                    <div id="gom-all-in-one"><!-- v3 -->
                        <!-- zalo -->
                        <div id="zalo-vr" class="button-contact">
                            <div class="phone-vr">
                                <div class="phone-vr-circle-fill"></div>
                                <div class="phone-vr-img-circle">
                                    <a target="_blank" href="https://zalo.me/0981185620">
                                        <img alt="Zalo"
                                            src="https://sgomedia.vn/wp-content/plugins/button-contact-vr/img/zalo.png">
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- end zalo -->
                    </div><!-- end v3 class gom-all-in-one -->


                </div>
                @yield('content')
            </div>


            @include('admin.layout.footer')

        </div>

    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/chart-circle/circles.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jsvectormap/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jsvectormap/world.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/js/bootstrap-notify.min.js"></script>
    <script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>
    <script src="{{ asset('assets/js/kaiadmin.js') }}"></script>
    <script src="{{ asset('assets/js/setting-demo.js') }}"></script>
    <script src="{{ asset('assets/js/setting-demo2.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('assets/js/demo.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    {{-- <script>
        $("#lineChart").sparkline([102, 109, 120, 99, 110, 105, 115], {
          type: "line",
          height: "70",
          width: "100%",
          lineWidth: "2",
          lineColor: "#177dff",
          fillColor: "rgba(23, 125, 255, 0.14)",
        });

        $("#lineChart2").sparkline([99, 125, 122, 105, 110, 124, 115], {
          type: "line",
          height: "70",
          width: "100%",
          lineWidth: "2",
          lineColor: "#f3545d",
          fillColor: "rgba(243, 84, 93, .14)",
        });

        $("#lineChart3").sparkline([105, 103, 123, 100, 95, 105, 115], {
          type: "line",
          height: "70",
          width: "100%",
          lineWidth: "2",
          lineColor: "#ffa534",
          fillColor: "rgba(255, 165, 52, .14)",
        });


    </script> --}}

    <script>
        WebFont.load({
            google: {
                families: ["Public Sans:300,400,500,600,700"]
            },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
                urls: ["{{ asset('assets/css/fonts.min.css') }}"],
            },
            active: function() {
                sessionStorage.fonts = true;
            },
        });
    </script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#open-add-oa-modal').on('click', function() {
                $('#add-oa-form')[0].reset(); // Thay đổi thành id chính xác
                $('.invalid-feedback').hide();
                $('#addOAModal').modal('show');
            });
            // Sự kiện khi nhấn vào nút mở modal
            $('#open-add-modal').on('click', function() {
                $('#add-client-form')[0].reset();
                $('.invalid-feedback').hide(); // Ẩn tất cả các thông báo lỗi
                $('#addClientModal').modal('show'); // Hiển thị modal
            });

            // Sự kiện submit form
            $('#add-client-form').on('submit', function(e) {
                let username = "{{ Auth::user()->username }}";
                e.preventDefault();
                $.ajax({
                    url: "{{ route('admin.{username}.store.store', ['username' => '__USERNAME__']) }}"
                        .replace(
                            '__USERNAME__', username),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        console.log(response); // Kiểm tra phản hồi từ server
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: 'Thêm khách hàng thành công',
                                showConfirmButton: false,
                                timer: 1500,
                                position: 'top-end',
                                toast: true
                            });
                            $('#addClientModal').modal('hide'); // Đóng modal khi thành công
                        } else {
                            console.log('Response failed:', response);
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi!',
                                text: response.message ||
                                    'Có lỗi xảy ra, vui lòng thử lại',
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.name) {
                                $('#name').addClass('is-invalid');
                                $('#name-error').text(errors.name[0]).show();
                            }
                            if (errors.phone) {
                                $('#phone').addClass('is-invalid');
                                $('#phone-error').text(errors.phone[0]).show();
                            }
                            if (errors.address) {
                                $('#address').addClass('is-invalid');
                                $('#address-error').text(errors.address[0]).show();
                            }
                            if (errors.email) {
                                $('#email').addClass('is-invalid');
                                $('#email-error').text(errors.email[0]).show();
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Thất bại!',
                                text: 'Thêm khách hàng thất bại',
                            });
                        }
                    }
                });
            });

            // Sự kiện submit form thêm oa
            $('#add-oa-form').on('submit', function(e) {
                let username = "{{ Auth::user()->username }}";
                e.preventDefault();
                $.ajax({
                    url: "{{ route('admin.{username}.zalo.store', ['username' => Auth::user()->username]) }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: 'Thêm OA mới thành công',
                                showConfirmButton: false,
                                timer: 1500,
                                position: 'top-end',
                                toast: true
                            });
                            $('#addOAModal').modal('hide');
                        } else {
                            console.log('Response Failed: ', response);
                            Swal.fire({
                                icon: 'error',
                                title: 'Thất bại',
                                text: response.message ||
                                    'Có lỗi xảy ra, vui lòng thử lại',
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.name) {
                                $('#oa_name').addClass('is-invalid');
                                $('#oa_name-error').text(errors.name[0]).show();
                            }

                            if (errors.oa_id) {
                                $('#oa_id').addClass('is-invalid');
                                $('#oa_id-error').text(errors.oa_id[0]).show();
                            }

                            if (errors.access_token) {
                                $('#access_token').addClass('is-invalid');
                                $('#access_token-error').text(errors.access_token[0]).show();
                            }

                            if (errors.refresh_token) {
                                $('#refresh_token').addClass('is-invalid');
                                $('#refresh_token-error').text(errors.refresh_token[0]).show();
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Thất bại!',
                                text: 'Thêm OA thất bại',
                            });
                        }
                    }
                });
            });

        });
    </script>

</body>

</html>
