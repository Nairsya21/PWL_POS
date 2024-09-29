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
                // serverSide: true for server-side processing
                serverSide: true,      
                ajax: { 
                    "url": "{{ url('barang/list') }}",  // URL untuk mengambil data
                    "type": "POST", 
                    "dataType": "json"
                }, 
                columns: [ 
                    {
                        data: "DT_RowIndex", // Nomor urut otomatis dari DataTables
                        className: "text-center", 
                        orderable: false, 
                        searchable: false     
                    },
                    { 
                        data: "barang_kode",  // Kode Barang
                        className: "", 
                        orderable: true,     
                        searchable: true     
                    },
                    { 
                        data: "barang_nama",  // Nama Barang
                        className: "", 
                        orderable: true,     
                        searchable: true     
                    },
                    { 
                        data: "kategori",  // Kategori Barang (hubungan ke KategoriModel)
                        className: "", 
                        orderable: true,     
                        searchable: true     
                    },
                    { 
                        data: "harga_beli",  // Harga Beli
                        className: "", 
                        orderable: true,     
                        searchable: true     
                    },
                    { 
                        data: "harga_jual",  // Harga Jual
                        className: "", 
                        orderable: true,     
                        searchable: true     
                    },
                    { 
                        data: "aksi",  // Kolom aksi untuk tombol Edit dan Hapus
                        className: "", 
                        orderable: false,     
                        searchable: false     
                    } 
                ]
            });
        });
    </script>
@endpush
