@extends('admin.layout.index')

@section('content')
    <style>
        /* CSS như cũ */
        .table-responsive {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            white-space: nowrap;
        }

        .summary-section {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .summary-table {
            border: 1px solid #ddd;
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        .summary-table th,
        .summary-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .summary-table th {
            background-color: #f4f4f4;
        }

        .total-fees {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .no-data-message {
            text-align: center;
            padding: 20px;
            color: #888;
        }
    </style>

    <div class="container-fluid">
        <h2>Danh sách tin nhắn</h2>

        <!-- Form Lọc Trạng Thái -->
        <label for="status">Chọn trạng thái:</label>
        <select name="status" id="status" onchange="filterByStatus()">
            <option value="">Tất cả</option> <!-- Thêm tùy chọn này để hiển thị tất cả tin nhắn -->
            <option value="0">Gửi thất bại</option>
            <option value="1">Gửi thành công</option>
        </select>


        <!-- Div hiển thị danh sách tin nhắn -->
        <div id="messageTable">
            @include('admin.message.table', ['messages' => $messages])
        </div>
    </div>

    <script>
        function filterByStatus() {
            const status = document.getElementById('status').value;
            let url = `{{ route('admin.{username}.message.status', ['username' => Auth::user()->username]) }}`;

            if (status !== "") {
                url += `?status=${status}`;
            }

            // Gửi yêu cầu AJAX đến server
            fetch(url)
                .then(response => response.text()) // Lấy dữ liệu phản hồi dưới dạng văn bản HTML
                .then(data => {
                    // Cập nhật bảng tin nhắn mà không cần tải lại trang
                    document.getElementById('messageTable').innerHTML = data;
                })
                .catch(error => {
                    console.error('Lỗi:', error);
                });
        }
    </script>
@endsection
