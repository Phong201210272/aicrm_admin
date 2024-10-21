@if ($messages->isEmpty())
    <div class="no-data-message">Không có tin nhắn nào để hiển thị.</div>
@else
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>OA</th>
                    <th>Tên</th>
                    <th>Số điện thoại</th>
                    <th>Ngày gửi</th>
                    <th>Template</th>
                    <th>Phí</th>
                    <th>Trạng thái</th>
                    <th>Thông báo</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($messages as $message)
                    <tr>
                        <td>{{ $message->zaloOa->name }}</td>
                        <td>{{ $message->name }}</td>
                        <td>{{ $message->phone }}</td>
                        <td>{{ \Carbon\Carbon::parse($message->sent_at)->format('H:i:s d/m/Y') }}</td>
                        <td>{{ $message->template->template_name ?? 'N/A' }}</td>
                        <td>{{ $message->status == 1 ? $message->template->price ?? '0' : '0' }} đ</td>
                        <td>
                            @if ($message->status == 1)
                                Thành công
                            @else
                                Thất bại
                            @endif
                        </td>
                        <td>{{ $message->note }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
