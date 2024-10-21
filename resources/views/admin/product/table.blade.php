<table id="basic-datatables" class="display table table-striped table-hover dataTable" role="grid"
    aria-describedby="basic-datatables_info">
    <thead>
        <tr>
            <th>STT</th>
            <th>Tên sản phầm</th>
            <th>Ngày tạo</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @if ($products->count() > 0)
            @foreach ($products as $key => $product)
                <tr>
                    <td>{{ ($products->currentPage() - 1) * $products->perPage() + $loop->index + 1 }}</td>
                    <td>{{ $product->name ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($product->created_at)->format('d/m/Y') }}</td>
                    <td>
                        <a class="btn btn-warning btn-edit-product" href="javascript:void(0)" data-id="{{ $product->id }}"
                            data-name="{{ $product->name }}">
                            <i class="fa-solid fa-wrench"></i>
                        </a>
                        <a class="btn btn-danger btn-delete" data-id="{{ $product->id }}" href="#"><i
                                class="fa-solid fa-trash"></i></a>
                    </td>

                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="5">
                    Chưa có chiến dịch nào
                </td>
            </tr>
        @endif
    </tbody>
</table>
