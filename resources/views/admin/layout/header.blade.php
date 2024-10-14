<div class="main-header">
    <div class="main-header-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
            <a href="../index.html" class="logo">
                <img src="../assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20">
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>
        <!-- End Logo Header -->
    </div>
    <!-- Navbar Header -->
    <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
        <div class="container-fluid">
            <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">

                <li class="nav-item topbar-user dropdown hidden-caret">
                    <a href="javascript:void(0)" id="open-add-modal"
                        style="background-color: white; border-color: white">
                        <i style="font-size: 18px; padding: 0px 5px; color: rgb(138, 135, 135)"
                            class="fa-solid fa-plus"></i> Thêm khách hàng
                    </a>
                </li>

                <li class="nav-item topbar-user dropdown hidden-caret">
                    <a href="javascript:void(0)" id="open-add-oa-modal"
                        style="background-color: white; border-color: white">
                        <i style="font-size: 18px; padding: 0px 5px; color: rgb(138, 135, 135)"
                            class="fa-solid fa-plus"></i> Thêm OA
                    </a>
                </li>

                <li class="nav-item topbar-user dropdown hidden-caret">
                    <a class="dropdown-toggle profile-pic" target="_blank"
                        href="{{ route('admin.{username}.transaction.payment', ['username' => Auth::user()->username]) }}"
                        aria-expanded="false">
                        <i style="font-size: 18px; padding: 0px 5px; color: rgb(138, 135, 135)"
                            class="fa-solid fa-wallet"></i> Nạp tiền: {{ number_format(\Auth::user()->wallet) }} đ

                    </a>
                </li>
                <li class="nav-item topbar-user dropdown hidden-caret">
                    <a class="dropdown-toggle profile-pic" target="_blank" href="" aria-expanded="false">
                        <i style="font-size: 18px; padding: 0px 5px; color: rgb(138, 135, 135)"
                            class="fa-solid fa-wallet"></i> Ví phụ: {{ number_format(\Auth::user()->sub_wallet) }} đ

                    </a>
                </li>
                <li class="nav-item topbar-icon dropdown hidden-caret">
                    <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-bell"></i>
                        <span
                            class="notification">{{ $adminNotifications->count() + $adminTransferNotifications->count() ?? '0' }}</span>
                    </a>
                    <ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown">
                        <li>
                            <div class="dropdown-title">
                                You have
                                {{ $adminNotifications->count() + $adminTransferNotifications->count() ?? '' }} new
                                notifications
                            </div>
                        </li>
                        <li>
                            <div class="scroll-wrapper notif-scroll scrollbar-outer" style="position: relative;">
                                <div class="notif-scroll scrollbar-outer scroll-content"
                                    style="height: auto; margin-bottom: 0px; margin-right: 0px; max-height: 0px;">
                                    <div class="notif-center">
                                        @foreach ($adminTransferNotifications as $item)
                                            @php
                                                $createdAt = $item->created_at;
                                                $timeElapsed = Carbon\Carbon::parse($createdAt)
                                                    ->locale('vi')
                                                    ->diffForHumans();
                                            @endphp
                                            <a href="{{ route('admin.{username}.transfer.updateNotification', ['username' => Auth::user()->username, 'id' => $item->id]) }}"
                                                class="notification-item mark-as-read">
                                                <!-- Thêm data-href -->
                                                <div class="notif-icon notif-primary">
                                                    <i class="fas fa-bell"></i>
                                                </div>
                                                <div class="notif-content">
                                                    <span class="block">
                                                        Bạn vừa được chuyển {{ number_format($item->amount) }} vào ví
                                                        phụ
                                                    </span>
                                                    <span class="time">{{ $timeElapsed }}</span>
                                                </div>
                                            </a>
                                        @endforeach
                                        @foreach ($adminNotifications as $item)
                                            @php
                                                $createdAt = $item->created_at;
                                                $timeElapsed = Carbon\Carbon::parse($createdAt)
                                                    ->locale('vi')
                                                    ->diffForHumans();
                                            @endphp
                                            <a href="{{ route('admin.{username}.transaction.updateNotification', ['username' => Auth::user()->username, 'id' => $item->id]) }}"
                                                class="notification-item mark-as-read">
                                                <!-- Thêm data-href -->
                                                <div class="notif-icon notif-primary">
                                                    <i class="fas fa-bell"></i>
                                                </div>
                                                <div class="notif-content">
                                                    <span class="block">
                                                        Giao dịch của bạn đã
                                                        @if ($item->notification == 0)
                                                            được xác nhận
                                                        @else
                                                            bị từ chối
                                                        @endif
                                                    </span>
                                                    <span class="time">{{ $timeElapsed }}</span>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="scroll-element scroll-x">
                                    <div class="scroll-element_outer">
                                        <div class="scroll-element_size"></div>
                                        <div class="scroll-element_track"></div>
                                        <div class="scroll-bar"></div>
                                    </div>
                                </div>
                                <div class="scroll-element scroll-y">
                                    <div class="scroll-element_outer">
                                        <div class="scroll-element_size"></div>
                                        <div class="scroll-element_track"></div>
                                        <div class="scroll-bar"></div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a class="see-all" href="javascript:void(0);">See all notifications<i
                                    class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item topbar-user dropdown hidden-caret">
                    <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#"
                        aria-expanded="false">
                        <div class="avatar-sm">
                            <img src="{{ isset(session('authUser')->user_info->img_url) && !empty(session('authUser')->user_info->img_url) ? asset(session('authUser')->user_info->img_url) : asset('images/avatar2.jpg') }}"
                                alt="image profile" class="avatar-img rounded-circle">
                        </div>
                        <span class="profile-username">
                            <span class="op-7">Hi,</span>
                            <span class="fw-bold">{{ session('authUser')->name }}</span>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-user animated fadeIn">
                        <div class="scroll-wrapper dropdown-user-scroll scrollbar-outer" style="position: relative;">
                            <div class="dropdown-user-scroll scrollbar-outer scroll-content"
                                style="height: auto; margin-bottom: 0px; margin-right: 0px; max-height: 0px;">
                                <li>
                                    <div class="user-box">
                                        <div class="avatar-lg">
                                            <img src="{{ isset(session('authUser')->user_info->img_url) && !empty(session('authUser')->user_info->img_url) ? asset(session('authUser')->user_info->img_url) : asset('images/avatar2.jpg') }}"
                                                alt="image profile" class="avatar-img rounded-circle">
                                        </div>
                                        <div class="u-text">
                                            <h4>{{ session('authUser')->name }}</h4>
                                            <p class="text-muted">{{ session('authUser')->email }}</p>
                                            <a href="{{ route('admin.{username}.detail', ['username' => Auth::user()->username, 'id' => session('authUser')->id]) }}"
                                                class="btn btn-xs btn-secondary btn-sm">Trang cá nhân</a>
                                            <a href="#" class="btn btn-xs  btn-sm"
                                                style="background: red; color: #ffff"
                                                onclick="event.preventDefault(); document.getElementById('logoutForm').submit();">Đăng
                                                xuất</a>
                                            <form id="logoutForm"
                                                action="{{ route('admin.{username}.logout', ['username' => Auth::user()->username]) }}"
                                                method="POST" style="display: none;">
                                                @csrf
                                            </form>
                                        </div>
                                    </div>
                                </li>

                            </div>
                            <div class="scroll-element scroll-x">
                                <div class="scroll-element_outer">
                                    <div class="scroll-element_size"></div>
                                    <div class="scroll-element_track"></div>
                                    <div class="scroll-bar"></div>
                                </div>
                            </div>
                            <div class="scroll-element scroll-y">
                                <div class="scroll-element_outer">
                                    <div class="scroll-element_size"></div>
                                    <div class="scroll-element_track"></div>
                                    <div class="scroll-bar"></div>
                                </div>
                            </div>
                        </div>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <!-- End Navbar -->
</div>

<div class="modal fade" id="addClientModal" tabindex="-1" role="dialog" aria-labelledby="addClientModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addClientModalLabel">Thêm khách hàng</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Form Thêm khách hàng -->
                <form id="add-client-form">
                    <div class="form-group">
                        <label for="name">Tên khách hàng</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="invalid-feedback" id="name-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="phone">Số điện thoại</label>
                        <input type="text" class="form-control" id="phone" name="phone" required>
                        <div class="invalid-feedback" id="phone-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback" id="email-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="address">Địa chỉ</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                        <div class="invalid-feedback" id="address-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="address">Nguồn</label>
                        <input type="text" class="form-control" id="source" name="source">
                        <div class="invalid-feedback" id="address-error"></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Thêm khách hàng</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal thêm oa mới -->
<div class="modal fade" id="addOAModal" tabindex="-1" role="dialog" aria-labelledby="addOAModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addOAModalLabel">Thêm OA</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Form Thêm khách hàng -->
                <form id="add-oa-form">
                    <div class="form-group">
                        <label for="name">Tên OA</label>
                        <input type="text" class="form-control" id="oa_name" name="name" required>
                        <div class="invalid-feedback" id="oa_name-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="oa_id">OA ID</label>
                        <input type="text" class="form-control" id="oa_id" name="oa_id" required>
                        <div class="invalid-feedback" id="oa_id-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="access_token">Access Token</label>
                        <input type="access_token" class="form-control" id="access_token" name="access_token"
                            required>
                        <div class="invalid-feedback" id="access_token-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="refresh_token">Refresh Token</label>
                        <input type="text" class="form-control" id="refresh_token" name="refresh_token" required>
                        <div class="invalid-feedback" id="refresh_token-error"></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Thêm OA mới</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script></script>
