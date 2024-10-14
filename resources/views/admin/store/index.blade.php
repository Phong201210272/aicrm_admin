@extends('admin.layout.index')
@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

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
                        <h4 class="card-title" style="text-align: center; color:white">Danh sách khách hàng</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div id="basic-datatables_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4">
                                <div class="row">
                                    <div class="col-sm-12 col-md-5">
                                        <button id="open-import-modal" type="button" class="btn btn-primary">
                                            Import danh sách khách hàng
                                        </button>
                                        <a href="{{ asset('excel/sample.xlsx') }}" class="btn btn-secondary" download>
                                            Tải file mẫu
                                        </a>
                                    </div>
                                    <div class="col-sm-12 col-md-6">
                                        <form
                                            action="{{ route('admin.{username}.store.findByPhone', ['username' => Auth::user()->username]) }}"
                                            method="GET">
                                            <div class="dataTables_filter">
                                                <label>Tìm kiếm</label>
                                                <input type="text" name="phone" class="form-control form-control-sm"
                                                    placeholder="Nhập số điện thoại" value="{{ old('phone') }}">
                                            </div>
                                        </form>
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
                                                    <th>Điện thoại</th>
                                                    <th>Thời gian</th>
                                                    <th>Nguồn</th>
                                                    <th>Chiến dịch</th>
                                                    <th style="text-align: center">Hành động</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($stores && $stores->count() > 0)
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
                                                                    {{-- Accordion for campaigns --}}
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
                                                @endif
                                            </tbody>
                                        </table>

                                        @if ($stores instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                            {{ $stores->links('vendor.pagination.custom') }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Excel File</h5>
                    <button id="close-x" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="importForm" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="source">Nguồn</label>
                            <input type="text" class="form-control" id="source" name="source"
                                placeholder="Nhập nguồn">
                        </div>
                        <div class="form-group">
                            <label for="excelFile">Chọn file Excel</label>
                            <input type="file" class="form-control-file" id="excelFile" name="import_file"
                                accept=".xlsx, .xls">
                        </div>
                        <div class="form-group">
                            <small class="text-danger">
                                Bắt buộc nhập 2 trường họ tên và số điện thoại (số điện thoại phải đủ 10 số và bắt đầu bằng
                                số 0)
                            </small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="importForm" class="btn btn-primary">Import</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/js/bootstrap-notify.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#open-import-modal').on('click', function() {
                $('#importModal').modal('show');
            });

            $('#close-x').on('click', function() {
                $('#importModal').modal('hide');
            });

            $('#importForm').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('admin.{username}.store.import', ['username' => Auth::user()->username]) }}", // URL đúng
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                text: response.message,
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: response.message,
                            });
                        }
                    },
                    error: function(xhr) {
                        var errorMessage = 'Có lỗi xảy ra!';
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            errorMessage = Object.values(errors).map(error => error.join(', '))
                                .join('<br>');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            html: errorMessage,
                        });
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Accordion functionality
            $('.accordion-button').click(function() {
                $(this).next('.accordion-content').slideToggle();
                $(this).toggleClass('active');
            });

            // Notify functionality
            @if (session('success'))
                $.notify({
                    icon: 'icon-bell',
                    title: 'Thông báo',
                    message: '{{ session('success') }}',
                }, {
                    type: 'secondary',
                    placement: {
                        from: "bottom",
                        align: "right"
                    },
                    time: 1000,
                });
            @endif
        });
    </script>
@endsection
