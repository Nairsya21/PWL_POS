@extends('layouts.template') 
@section('content') 
<div class="card card-outline card-primary"> 
  <div class="card-header"> 
    <h3 class="card-title">{{ $page->title }}</h3> 
    <div class="card-tools"></div> 
  </div> 
  <div class="card-body"> 
    <form method="POST" action="{{ url('stok') }}" class="form-horizontal"> 
      @csrf 
      <input type="hidden" name="user_id" value="{{ auth()->id() }}"> 
      <div class="form-group row"> 
        <label class="col-1 control-label col-form-label">Barang</label> 
        <div class="col-11"> 
          <select class="form-control" id="barang_id" name="barang_id" required> 
            <option value="">-- Pilih Barang --</option> 
            @foreach($barangs as $b) 
              <option value="{{ $b->barang_id }}" {{ old('barang_id') == $b->barang_id ? 'selected' : '' }}>
                {{ $b->barang_nama }}
              </option> 
            @endforeach 
          </select> 
          @error('barang_id') 
            <small class="form-text text-danger">{{ $message }}</small> 
          @enderror 
        </div> 
      </div> 

      <div class="form-group row"> 
        <label class="col-1 control-label col-form-label">Supplier</label> 
        <div class="col-11"> 
          <select class="form-control" id="supplier_id" name="supplier_id" required> 
            <option value="">-- Pilih Supplier --</option> 
            @foreach($suppliers as $supplier) 
              <option value="{{ $supplier->supplier_id }}" {{ old('supplier_id') == $supplier->supplier_id ? 'selected' : '' }}>
                {{ $supplier->supplier_nama }}
              </option> 
            @endforeach 
          </select> 
          @error('supplier_id') 
            <small class="form-text text-danger">{{ $message }}</small> 
          @enderror 
        </div> 
      </div> 

      <div class="form-group row"> 
        <label class="col-1 control-label col-form-label">Tanggal Stok</label> 
        <div class="col-11"> 
          <input type="date" class="form-control" id="stok_tanggal" name="stok_tanggal" value="{{ old('stok_tanggal') }}" required> 
          @error('stok_tanggal') 
            <small class="form-text text-danger">{{ $message }}</small> 
          @enderror 
        </div> 
      </div> 

      <div class="form-group row"> 
        <label class="col-1 control-label col-form-label">Jumlah Stok</label> 
        <div class="col-11"> 
          <input type="number" class="form-control" id="stok_jumlah" name="stok_jumlah" value="{{ old('stok_jumlah') }}" required> 
          @error('stok_jumlah') 
            <small class="form-text text-danger">{{ $message }}</small> 
          @enderror 
        </div> 
      </div> 

      <div class="form-group row"> 
        <label class="col-1 control-label col-form-label"></label> 
        <div class="col-11"> 
          <button type="submit" class="btn btn-primary btn-sm">Simpan</button> 
          <a class="btn btn-sm btn-default ml-1" href="{{ url('stok') }}">Kembali</a> 
        </div> 
      </div> 
    </form> 
  </div> 
</div> 
@endsection 

@push('css') 
@endpush 

@push('js') 
@endpush
