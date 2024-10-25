@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <!-- Nav Pills -->
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link active" id="data-penjualan-tab" data-toggle="pill" href="#data-penjualan" role="tab" aria-controls="data-penjualan" aria-selected="true">Data Penjualan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="detail-penjualan-tab" data-toggle="pill" href="#detail-penjualan" role="tab" aria-controls="detail-penjualan" aria-selected="false">Detail Penjualan</a>
                </li>
            </ul>
            <div class="card-tools mt-3">
                <button onclick="modalAction('{{ url('penjualan/import') }}')" class="btn btn-info">Import penjualan</button>
                <a href="{{ url('/penjualan/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i>Export penjualan</a>
                <a href="{{ url('/penjualan/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"></i>Export Penjualan</a>
                <button onclick="modalAction('{{ url('/penjualan/create_ajax') }}')" class="btn btn-sm btn-success mt-1">Tambah Ajax</button>
            </div>
        </div>

        <div class="card-body">
            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Tab for Data Penjualan -->
                <div class="tab-pane fade show active" id="data-penjualan" role="tabpanel" aria-labelledby="data-penjualan-tab">
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
                                    <select class="form-control" id="user_id" name="user_id" required>
                                        <option value="">- Semua -</option>
                                        @foreach ($users as $item)
                                            <option value="{{ $item->user_id }}">{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">User</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered table-striped table-hover table-sm" id="table-penjualan">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Pembeli</th>
                                <th>Kode Penjualan</th>
                                <th>Tanggal</th>
                                <th>User</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <!-- Tab for Detail Penjualan -->
                <div class="tab-pane fade" id="detail-penjualan" role="tabpanel" aria-labelledby="detail-penjualan-tab">
                    {{-- @include('penjualan.detailPenjualan_ajax') --}}
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
                                    <select class="form-control" id="penjualan_id" name="penjualan_id" required>
                                        <option value="">- Semua -</option>
                                        @foreach ($penjualan as $item)
                                            <option value="{{ $item->penjualan_id }}">{{ $item->penjualan_kode }}</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Transaksi</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered table-striped table-hover table-sm" id="table-detail-penjualan">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Kode Transaksi</th>
                                <th>Barang</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
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

    var dataPenjualan;
    $(document).ready(function() { 
        dataPenjualan = $('#table-penjualan').DataTable({
            serverSide: true,      
            ajax: { 
                "url": "{{ url('penjualan/list') }}", 
                "type": "POST", 
                "dataType": "json",
                "data": function(d) { // Send CSRF token with the request
                    d.user_id = $('#user_id').val();
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
                    data: "pembeli",                
                    className: "", 
                    orderable: true,     
                    searchable: true     
                },
                { 
                    data: "penjualan_kode",                
                    className: "", 
                    orderable: true,     
                    searchable: true     
                },
                { 
                    data: "penjualan_tanggal",                
                    className: "", 
                    orderable: true,     
                    searchable: false     
                },
                { 
                    data: "user.nama",                
                    className: "", 
                    orderable: false,     
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

        $('#user_id').on('change', function() {
            dataPenjualan.ajax.reload();
        });
    }); 
    var detailPenjualan;
    $(document).ready(function() { 
        detailPenjualan = $('#table-detail-penjualan').DataTable({
            serverSide: true,  
            autoWidth: false,  // Disable auto width calculation by DataTables
            responsive: true,     
            ajax: { 
                "url": "{{ url('detailpenjualan/list') }}", 
                "type": "POST", 
                "dataType": "json",
                "data": function(d) { // Send CSRF token with the request
                    d.penjualan = $('#penjualan').val();
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
                    data: "penjualan.penjualan_kode",                
                    className: "", 
                    orderable: true,     
                    searchable: true     
                },
                { 
                    data: "barang.barang_nama",                
                    className: "", 
                    orderable: true,     
                    searchable: true     
                },
                { 
                    data: "harga",                
                    className: "", 
                    orderable: true,     
                    searchable: false     
                },
                { 
                    data: "jumlah",                
                    className: "", 
                    orderable: false,     
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

        $('#penjualan_id').on('change', function() {
            detailPenjualan.ajax.reload();
        });
    }); 
</script>
@endpush
