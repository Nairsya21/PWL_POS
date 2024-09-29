@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <a class="btn btn-sm btn-primary mt-1" href="{{ url('user/create') }}">Tambah</a>
            </div>
        </div>
        <div class="card-body">
            @if (session('sucess'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <table class="table table-bordered table-striped table-hover table-sm" id="table-user">
                <thead>
                    <tr><th>ID</th><th>Username</th><th>Nama</th><th>Level Pengguna</th><th>Aksi</th></tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('js') 
  <script> 
    $(document).ready(function() { 
      var dataUser = $('#table-user').DataTable({ 
          // serverSide: true, if using server-side processing 
          serverSide: true,      
          ajax: { 
              "url": "{{ url('user/list') }}", 
              "type": "POST", 
              "dataType": "json",
              "data": function (d) { // Send CSRF token with the request
                  d._token = "{{ csrf_token() }}";
              }
          }, 
          columns: [ 
            {
                // nomor urut from Laravel datatable addIndexColumn() 
              data: "DT_RowIndex",             
              className: "text-center", 
              orderable: false, 
              searchable: false     
            },{ 
              data: "username",                
              className: "", 
              orderable: true,     
              searchable: true     
            },{ 
              data: "nama",                
              className: "", 
              orderable: true,     
              searchable: true     
            },{ 
              // Fetching related data from 'level' relationship 
              data: "level.level_nama",                
              className: "", 
              orderable: false,     
              searchable: false     
            },{ 
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
