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

        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #9370db;
        }

        input:focus+.slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked+.slider:before {
            transform: translateX(26px);
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
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            padding: 1.5rem;
        }

        .card-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
            text-align: center;
        }

        .breadcrumbs {
            background: #fff;
            padding: 0.75rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
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
            text-align: center;
            /* Center align the text in the cells */
        }

        .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        .table-hover tbody tr:hover {
            background-color: #e9ecef;
        }

        .btn-warning,
        .btn-danger,
        .btn-primary {
            border-radius: 20px;
            padding: 5px 15px;
            font-size: 14px;
            font-weight: bold;
            transition: background 0.3s ease, transform 0.3s ease;
            margin: 0 2px;
            /* Add margin between buttons */
        }

        .btn-warning:hover,
        .btn-danger:hover,
        .btn-primary:hover {
            transform: scale(1.05);
        }

        .page-header {
            margin-bottom: 2rem;
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

        table th,
        table td {
            padding: 1rem;
            vertical-align: middle;
            text-align: center;
            /* Center align the text in the cells */
        }

        table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }
    </style>
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title" style="text-align: center; color:white">Danh sách sản phẩm - dịch vụ</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div id="basic-datatables_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4">
                                <div class="row">
                                    <div class="col-sm-12 col-md-6">
                                        <div class="dataTables_length" id="basic-datatables_length">
                                            <a class="btn btn-primary" href="javascript:void(0)"
                                                id="open-add-product-modal">
                                                Thêm mới
                                            </a>
                                        </div>
                                    </div>
                                    {{-- <div class="col-sm-12 col-md-6">
                                        <form action="" method="GET">
                                            <div class="dataTables_filter">
                                                <label>Tìm kiếm</label>
                                                <input type="text" name="name" clabss="form-control form-control-sm"
                                                    placeholder="Nhập tên chiến dịch" value="{{ old('name') }}">
                                            </div>
                                        </form>
                                    </div> --}}
                                </div>
                                <div class="row">
                                    <div class="col-sm-12" id="company-table">
                                        @include('admin.product.table', ['products' => $products])
                                    </div>
                                    <div class="col-sm-12" id="pagination">

                                        @if ($products instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                            {{ $products->links('vendor.pagination.custom') }}
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
    <div class="modal fade" id="updateProductModal" tabindex="-1" aria-labelledby="updateProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateProductModalLabel">Thêm sản phẩm mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="update-product-form">
                        @csrf
                        <!-- Tên sản phẩm -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <input type="hidden" id="product-id" name="id"> <!-- Input ẩn cho ID sản phẩm -->
                            <small id="name-error" class="text-danger"></small>
                        </div>
                        <button type="submit" class="btn btn-primary">Xác nhận</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addClientModalLabel">Thêm sản phẩm mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="add-product-form">
                        @csrf
                        <!-- Tên sản phẩm -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <input hidden name="user_id" value="{{ Auth::user()->username }}">
                            <small id="name-error" class="text-danger"></small>
                        </div>
                        <button type="submit" class="btn btn-primary">Xác nhận</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/js/bootstrap-notify.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#open-add-product-modal').on('click', function() {
                $('#add-product-form')[0].reset(); // Thay đổi thành id chính xác
                $('.invalid-feedback').hide();
                $('#addProductModal').modal('show');
            });

            $('#add-client-form').on('submit', function(e) {
                let username = "{{ Auth::user()->username }}";
                e.preventDefault();
                $.ajax({
                    url: "{{ route('admin.{username}.product.store', ['username' => '__USERNAME__']) }}"
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
                        Swal.fire({
                            icon: 'error',
                            title: 'Thất bại!',
                            text: 'Thêm khách hàng thất bại',
                        });
                    }
                });
            });
        })
    </script>
    <script>
        $(document).ready(function() {
            // Khi bấm vào nút sửa sản phẩm
            $('.btn-edit-product').on('click', function(e) {
                e.preventDefault();

                // Lấy thông tin sản phẩm từ data-attribute
                var productId = $(this).data('id');
                var productName = $(this).data('name');

                // Đổ tên sản phẩm và ID vào input trong modal
                $('#updateProductModal #name').val(productName);
                $('#updateProductModal #product-id').val(productId); // Thiết lập ID sản phẩm

                // Hiển thị modal cập nhật sản phẩm
                $('#updateProductModal').modal('show');

                // Gỡ bỏ sự kiện submit cũ (nếu có) để tránh bị gắn nhiều lần
                $('#update-product-form').off('submit');

                // Đặt hành động submit cho form
                $('#update-product-form').on('submit', function(e) {
                    e.preventDefault();

                    var formData = $(this).serialize();

                    // Gửi Ajax request để cập nhật sản phẩm
                    $.ajax({
                        url: '{{ route('admin.{username}.product.update', ['username' => Auth::user()->username]) }}', // Đường dẫn để gọi updateProduct
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.success) {
                                // Ẩn modal và hiển thị thông báo thành công
                                $('#updateProductModal').modal('hide');
                                alert(response
                                .message); // Có thể thay bằng Swal hoặc notify

                                // Cập nhật bảng sản phẩm và phân trang nếu có
                                $('#productTable').html(response.table);
                                $('#pagination').html(response.pagination);
                            } else {
                                alert(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('Có lỗi xảy ra khi cập nhật sản phẩm.');
                        }
                    });
                });
            });

            $('#open-add-product-modal').on('click', function() {
                $('#add-product-form')[0].reset(); // Reset form
                $('.invalid-feedback').hide();
                $('#addProductModal').modal('show'); // Hiển thị modal
            });

            $('#add-product-form').on('submit', function(e) {
                let username = "{{ Auth::user()->username }}";
                e.preventDefault();
                $.ajax({
                    url: "{{ route('admin.{username}.product.store', ['username' => '__USERNAME__']) }}"
                        .replace('__USERNAME__', username),
                    type: 'POST',
                    data: $(this).serialize(), // Lấy dữ liệu từ form
                    success: function(response) {
                        console.log(response); // Kiểm tra phản hồi từ server
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: 'Thêm sản phẩm thành công',
                                showConfirmButton: false,
                                timer: 1500,
                                position: 'top-end',
                                toast: true
                            });
                            $('#addProductModal').modal('hide'); // Đóng modal khi thành công
                            // Cập nhật bảng và phân trang nếu cần
                            $('#company-table').html(response.table);
                            $('#pagination').html(response.pagination);
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
                        Swal.fire({
                            icon: 'error',
                            title: 'Thất bại!',
                            text: 'Thêm sản phẩm thất bại',
                        });
                    }
                });
            });
            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                const id = $(this).data('id');

                if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
                    $.ajax({
                        url: '{{ route('admin.{username}.product.delete', ['username' => Auth::user()->username]) }}',
                        type: 'POST',
                        data: {
                            id: id,
                            _token: '{{ csrf_token() }}' // Thêm token bảo mật
                        },
                        success: function(response) {
                            if (response.success) {
                                // Cập nhật bảng và phân trang sau khi xóa
                                $('#company-table').html(response.table);
                                $('#pagination').html(response.pagination);
                                $.notify({
                                    icon: 'icon-bell',
                                    title: 'Sản phẩm',
                                    message: response.message,
                                }, {
                                    type: 'success',
                                    placement: {
                                        from: "bottom",
                                        align: "right"
                                    },
                                    time: 1000,
                                });
                            } else {
                                $.notify({
                                    icon: 'icon-bell',
                                    title: 'Sản phẩm',
                                    message: response.message,
                                }, {
                                    type: 'danger',
                                    placement: {
                                        from: "bottom",
                                        align: "right"
                                    },
                                    time: 1000,
                                });
                            }
                        },
                        error: function(xhr) {
                            $.notify({
                                icon: 'icon-bell',
                                title: 'Sản phẩm',
                                message: 'Có lỗi xảy ra trong quá trình xóa',
                            }, {
                                type: 'danger',
                                placement: {
                                    from: "bottom",
                                    align: "right"
                                },
                                time: 1000,
                            });
                        }
                    });
                }
            });

        });
    </script>



    @if (session('success'))
        <script>
            $(document).ready(function() {
                $.notify({
                    icon: 'icon-bell',
                    title: 'Chiến dịch',
                    message: '{{ session('success') }}',
                }, {
                    type: 'secondary',
                    placement: {
                        from: "bottom",
                        align: "right"
                    },
                    time: 1000,
                });
            });
        </script>
    @endif
@endsection
