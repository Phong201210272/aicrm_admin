<table id="basic-datatables" class="display table table-striped table-hover dataTable" role="grid"
    aria-describedby="basic-datatables_info">
    <thead>
        <tr>
            <th>STT</th>
            <th>Tên</th>
            <th>Điện thoại</th>
            <th>Thời gian</th>
            <th>Danh mục</th>
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
                        <td>{{ $value->product->name ?? '' }}</td>
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
                            <a class="btn btn-danger btn-delete" data-id="{{ $value->id }}" href="#"><i
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
