@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <a class="btn btn-sm btn-primary mt-1" href="{{ url('barang/create') }}">Tambah Barang</a>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            
            <div class="form-group row">
                <label class="col-sm-1 col-form-label">Kategori:</label>
                <div class="col-sm-3">
                    <select class="form-control" id="kategori_filter">
                        <option value="">- Semua Kategori -</option>
                        @foreach ($kategori as $item)
                            <option value="{{ $item->kategori_id }}">{{ $item->kategori_nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <table class="table table-bordered table-striped table-hover table-sm" id="table-barang">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            var dataBarang = $('#table-barang').DataTable({
                serverSide: true,      
                ajax: { 
                    "url": "{{ url('barang/list') }}",  // URL untuk mengambil data
                    "type": "POST", 
                    "dataType": "json",
                    "data": function (d) {
                        d.kategori_id = $('#kategori_filter').val(); // Mengirim nilai kategori yang dipilih
                    }
                }, 
                columns: [ 
                    {
                        data: "DT_RowIndex",
                        className: "text-center", 
                        orderable: false, 
                        searchable: false     
                    },
                    { 
                        data: "barang_kode",
                        className: "", 
                        orderable: true,     
                        searchable: true     
                    },
                    { 
                        data: "barang_nama",
                        className: "", 
                        orderable: true,     
                        searchable: true     
                    },
                    { 
                        data: "kategori",  
                        className: "", 
                        orderable: true,     
                        searchable: true     
                    },
                    { 
                        data: "harga_beli",
                        className: "", 
                        orderable: true,     
                        searchable: true     
                    },
                    { 
                        data: "harga_jual",
                        className: "", 
                        orderable: true,     
                        searchable: true     
                    },
                    { 
                        data: "aksi",
                        className: "", 
                        orderable: false,     
                        searchable: false     
                    } 
                ]
            });

            // Mengatur event ketika kategori dipilih
            $('#kategori_filter').on('change', function(){
                dataBarang.ajax.reload(); // Reload data setelah memilih kategori
            });
        });
    </script>
@endpush
