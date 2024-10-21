@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('stok/import') }}')" class="btn btn-info">Import stok</button>
                <a href="{{ url('/stok/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i>Export stok</a>
                <a href="{{ url('/stok/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"></i>Export Stok</a>
                <button onclick="modalAction('{{ url('/stok/create_ajax') }}')" class="btn btn-sm btn-success mt-1">Tambah Ajax</button>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group row">
                        <label class="col-1 control-label col-form-label">Filter:</label>
                        <div class="col-3">
                            <select class="form-control" id="supplier_id" name="supplier_id" required>
                                <option value="">- Semua -</option>
                                @foreach ($suppliers as $item)
                                    <option value="{{ $item->supplier_id }}">{{ $item->supplier_nama }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Supplier</small>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-bordered table-striped table-hover table-sm" id="table-stok">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Barang</th>
                        <th>Supplier</th>
                        <th>User</th>
                        <th>Tanggal</th>
                        <th>Jumlah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true"></div> 
@endsection

@push('css')
@endpush

@push('js')
<script>
    function modalAction(url = '') { 
        $('#myModal').load(url, function() { 
            $('#myModal').modal('show'); 
        }); 
    } 

    var dataStok;
    $(document).ready(function() { 
        dataStok = $('#table-stok').DataTable({
            serverSide: true,      
            ajax: { 
                "url": "{{ url('stok/list') }}", 
                "type": "POST", 
                "dataType": "json",
                "data": function(d) { // Send CSRF token with the request
                    d.supplier_id = $('#supplier_id').val();
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
                    data: "barang.barang_nama",                
                    className: "", 
                    orderable: true,     
                    searchable: true     
                },
                { 
                    data: "supplier.supplier_nama",                
                    className: "", 
                    orderable: true,     
                    searchable: true     
                },
                { 
                    data: "user.nama",                
                    className: "", 
                    orderable: false,     
                    searchable: false     
                },
                { 
                    data: "stok_tanggal",                
                    className: "", 
                    orderable: true,     
                    searchable: false     
                },
                { 
                    data: "stok_jumlah",                
                    className: "text-right", 
                    orderable: true,     
                    searchable: false     
                },
                { 
                    data: "aksi",                
                    className: "", 
                    orderable: false,     
                    searchable: false     
                } 
            ] 
        }); 

        $('#supplier_id').on('change', function() {
            dataStok.ajax.reload();
        });
    }); 
</script>
@endpush
