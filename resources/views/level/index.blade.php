@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <a href="{{ url('/level/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"></i>Export Level</a>
                <button onclick="modalAction('{{ url('/level/create_ajax') }}')" class="btn btn-sm btn-success mt-1">Tambah Ajax</button>

            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            
            <table class="table table-bordered table-striped table-hover table-sm" id="table-level">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kode Level</th>
                        <th>Nama Level</th>
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
        function modalAction(url = ''){ 
            $('#myModal').load(url,function(){ 
                $('#myModal').modal('show'); 
            }); 
        }
        var dataLevel;
        $(document).ready(function() { 
            dataLevel= $('#table-level').DataTable({
                // serverSide: true, if using server-side processing 
                serverSide: true,      
                ajax: { 
                    "url": "{{ url('level/list') }}", 
                    "type": "POST", 
                    "dataType": "json"
                }, 
                columns: [ 
                    {
                        // nomor urut from Laravel datatable addIndexColumn() 
                        data: "DT_RowIndex",             
                        className: "text-center", 
                        orderable: false, 
                        searchable: false     
                    },
                    { 
                        data: "level_kode",                
                        className: "", 
                        orderable: true,     
                        searchable: true     
                    },
                    { 
                        data: "level_nama",                
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
        }); 
    </script> 
@endpush
