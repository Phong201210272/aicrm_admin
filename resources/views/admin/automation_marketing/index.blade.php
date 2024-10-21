@extends('admin.layout.index')
@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

        /* Cơ bản cho switch */
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        /* Slider */
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 4px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        /* Khi input được checked, thay đổi màu nền */
        input:checked+.slider {
            background-color: #9370db;
            /* Màu nền khi bật */
        }

        /* Hiệu ứng khi focus */
        input:focus+.slider {
            box-shadow: 0 0 1px #2196F3;
        }

        /* Thay đổi vị trí của slider khi input được checked */
        input:checked+.slider:before {
            transform: translateX(26px);
        }


        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .icon-bell:before {
            content: "\f0f3";
            font-family: FontAwesome;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            background-color: #fff;
            margin-bottom: 2rem;
        }

        .card-header {
            background: linear-gradient(135deg, #6f42c1, #007bff);
            color: white;
            border-top-left-radius: 0px;
            border-top-right-radius: 0px;
            padding: 1.5rem;
            text-align: center;
        }

        .card-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
        }

        .breadcrumbs {
            background: #fff;
            padding: 0.75rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .breadcrumbs a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumbs i {
            color: #6c757d;
        }

        .table-responsive {
            margin-top: 1rem;
        }

        .table {
            margin-bottom: 0;
        }

        .table th,
        .table td {
            padding: 1rem;
            vertical-align: middle;
        }

        .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        .btn-warning,
        .btn-danger {
            border-radius: 20px;
            padding: 5px 15px;
            font-size: 14px;
            font-weight: bold;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .btn-warning:hover,
        .btn-danger:hover {
            transform: scale(1.05);
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .table-hover tbody tr:hover {
            background-color: #e9ecef;
        }

        .dataTables_info,
        .dataTables_paginate {
            margin-top: 1rem;
        }

        .pagination .page-link {
            color: #007bff;
        }

        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
        }

        .pagination .page-item:hover .page-link {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .pagination .page-item.active .page-link,
        .pagination .page-item .page-link {
            transition: all 0.3s ease;
        }

        .dataTables_filter {
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .dataTables_filter label {
            margin-right: 0.5rem;
        }

        /* Accordion styles */
        .accordion-button {
            cursor: pointer;
            text-align: left;
            border: none;
            outline: none;
            background: #f8f9fa;
            padding: 0.5rem;
            width: 100%;
            font-size: 16px;
            font-weight: 500;
        }

        .accordion-content {
            display: none;
            padding: 0.5rem;
            border-top: 1px solid #dee2e6;
            background: #fff;
        }

        .accordion-content ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .accordion-content ul li {
            padding: 0.25rem 0;
        }
    </style>
    <div class="page-inner mt-0">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title" style="text-align: center; color:white">Automation Marketing</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div id="basic-datatables_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4">
                                <div class="row">
                                    <div class="col-sm-12 col-md-6">
                                        {{-- <form
                                            action="{{ route('admin.{username}.store.findByPhone', ['username' => Auth::user()->username]) }}"
                                            method="GET">
                                            <div class="dataTables_filter">
                                                <label>Tìm kiếm</label>
                                                <input type="text" name="phone" class="form-control form-control-sm"
                                                    placeholder="Nhập số điện thoại" value="{{ old('phone') }}">
                                            </div>
                                        </form> --}}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table id="basic-datatables"
                                            class="display table table-striped table-hover dataTable" role="grid"
                                            aria-describedby="basic-datatables_info">
                                            <thead>
                                                <tr>
                                                    <th>STT</th>
                                                    <th>Tên</th>
                                                    <th>Template</th>
                                                    <th>Trạng thái</th>
                                                    <th>Giờ gửi</th>
                                                    <th>Chu kỳ (ngày)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>{{ $user->name }}</td>
                                                    <td>
                                                        <select name="template_id" class="form-control template-dropdown"
                                                            data-id="{{ $user->id }}">
                                                            <!-- Hiển thị template hiện tại -->
                                                            <option value="{{ $user->template->id }}">
                                                                {{ $user->template->template_name }}</option>

                                                            <!-- Hiển thị danh sách templates còn lại -->
                                                            @foreach ($templates as $template)
                                                                @if ($template->id != $user->template->id)
                                                                    <option value="{{ $template->id }}">
                                                                        {{ $template->template_name }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>

                                                    </td>
                                                    <td>
                                                        <label class="switch">
                                                            <input type="checkbox" class="toggle-status"
                                                                data-id="{{ $user->id }}"
                                                                {{ $user->status == 1 ? 'checked' : '' }}>
                                                            <span class="slider round"></span>
                                                        </label>
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                {{-- @if ($stores && $stores->count() > 0)
                                                    @php
                                                        $stt = ($stores->currentPage() - 1) * $stores->perPage();
                                                    @endphp
                                                    @foreach ($stores as $value)
                                                        @if (is_object($value))
                                                            <tr>
                                                                <td>{{ ++$stt }}</td>
                                                                <td>{{ $value->name ?? '' }}</td>
                                                                <td>{{ $value->phone ?? '' }}</td>
                                                                <td>{{ $value->created_at ? $value->created_at->format('d/m/Y') : '' }}
                                                                </td>
                                                                <td>{{ $value->source ?? 'Thêm thủ công' }}</td>
                                                                <td>
                                                                    @if ($value->campaignDetails && $value->campaignDetails->isNotEmpty())
                                                                        <button class="accordion-button">
                                                                            Xem chiến dịch
                                                                        </button>
                                                                        <div class="accordion-content">
                                                                            <ul>
                                                                                @foreach ($value->campaignDetails as $campaignDetail)
                                                                                    <li>{{ $campaignDetail->campaign->name ?? 'Không có tên chiến dịch' }}
                                                                                    </li>
                                                                                @endforeach
                                                                            </ul>
                                                                        </div>
                                                                    @else
                                                                        Không có chiến dịch
                                                                    @endif
                                                                </td>
                                                                <td style="text-align:center">
                                                                    <a class="btn btn-warning"
                                                                        href="{{ route('admin.{username}.store.detail', ['username' => Auth::user()->username, 'id' => $value->id]) }}">
                                                                        <i class="fa-solid fa-eye"></i>
                                                                    </a>
                                                                    <a onclick="return confirm('Bạn có chắc chắn muốn xóa?')"
                                                                        class="btn btn-danger"
                                                                        href="{{ route('admin.{username}.store.delete', ['username' => Auth::user()->username, 'id' => $value->id]) }}"><i
                                                                            class="fa-solid fa-trash"></i></a>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td class="text-center" colspan="7">
                                                            <div class="">
                                                                Chưa có khách hàng
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif --}}
                                            </tbody>
                                        </table>

                                        {{-- @if ($stores instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                            {{ $stores->links('vendor.pagination.custom') }}
                                        @endif --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/js/bootstrap-notify.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Khi người dùng thay đổi trạng thái của checkbox
            $('.toggle-status').on('change', function() {
                var userId = $(this).data('id'); // Lấy ID người dùng từ thuộc tính data-id
                var status = $(this).is(':checked') ? 1 :
                0; // Lấy trạng thái mới (1 nếu checked, 0 nếu không)

                // Gửi AJAX request để cập nhật trạng thái
                $.ajax({
                    url: '{{ route('admin.{username}.automation.updateStatus', ['username' => Auth::user()->username]) }}', // Đường dẫn tới route update
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}', // Bảo mật với CSRF token
                        status: status,
                        user_id: userId
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                text: 'Cập nhật trạng thái thành công!',
                                timer: 1500,
                                showConfirmButton: false,
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: 'Cập nhật trạng thái thất bại!',
                                timer: 1500,
                                showConfirmButton: false,
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Có lỗi xảy ra, vui lòng thử lại!',
                            timer: 1500,
                            showConfirmButton: false,
                        });
                    }
                });
            });

            // Khi người dùng thay đổi template từ dropdown
            $('.template-dropdown').on('change', function() {
                var userId = $(this).data('id'); // Lấy ID người dùng từ thuộc tính data-id
                var templateId = $(this).val(); // Lấy ID template mới được chọn

                // Gửi AJAX request để cập nhật template
                $.ajax({
                    url: '{{ route('admin.{username}.automation.updateTemplate', ['username' => Auth::user()->username]) }}', // Đường dẫn tới route update template
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}', // Bảo mật với CSRF token
                        template_id: templateId,
                        user_id: userId
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                text: 'Cập nhật template thành công!',
                                timer: 1500,
                                showConfirmButton: false,
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: 'Cập nhật template thất bại!',
                                timer: 1500,
                                showConfirmButton: false,
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Có lỗi xảy ra, vui lòng thử lại!',
                            timer: 1500,
                            showConfirmButton: false,
                        });
                    }
                });
            });
        });
    </script>
@endsection
