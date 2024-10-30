@empty($detail)
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data yang anda cari tidak ditemukan
                </div>
                <a href="{{ url('/detailpenjualan') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/detailpenjualan/' . $detail->detail_id . '/update_ajax') }}" method="POST" id="form-edit-detailpenjualan"> 
        @csrf 
        @method('PUT')
        <div id="modal-master" class="modal-dialog modal-lg" role="document"> 
            <div class="modal-content"> 
                <div class="modal-header"> 
                    <h5 class="modal-title" id="exampleModalLabel">Edit Detail Penjualan</h5> 
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> 
                </div> 
                <div class="modal-body">
                    <div class="form-group"> 
                        <label>Penjualan</label> 
                        <select name="penjualan_id" id="penjualan_id" class="form-control" required> 
                            <option value="">- Pilih Penjualan -</option> 
                            @foreach($penjualans as $p) 
                                <option value="{{ $p->penjualan_id }}" {{ $p->penjualan_id == $detail->penjualan_id ? 'selected' : '' }}>{{ $p->penjualan_kode }}</option> 
                            @endforeach 
                        </select> 
                        <small id="error-penjualan_id" class="error-text form-text text-danger"></small> 
                    </div> 
                    <div class="form-group"> 
                        <label>Barang</label> 
                        <select name="barang_id" id="barang_id" class="form-control" required> 
                            <option value="">- Pilih Barang -</option> 
                            @foreach($barangs as $b) 
                                <option value="{{ $b->barang_id }}" {{ $b->barang_id == $detail->barang_id ? 'selected' : '' }}>{{ $b->barang_nama }}</option> 
                            @endforeach 
                        </select> 
                        <small id="error-barang_id" class="error-text form-text text-danger"></small> 
                    </div>
                    <div class="form-group"> 
                        <label>Jumlah</label> 
                        <input type="number" name="jumlah" id="jumlah" class="form-control" value="{{ $detail->jumlah }}" required> 
                        <small id="error-jumlah" class="error-text form-text text-danger"></small> 
                    </div> 
                    <div class="form-group"> 
                        <label>Harga</label> 
                        <input type="number" name="harga" id="harga" class="form-control" value="{{ $detail->harga }}" readonly> 
                        <small id="error-harga" class="error-text form-text text-danger"></small> 
                    </div> 
                </div> 
                <div class="modal-footer"> 
                    <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button> 
                    <button type="submit" class="btn btn-primary">Simpan</button> 
                </div> 
            </div> 
        </div> 
    </form> 

    <script> 
        $(document).ready(function() { 
            // Load harga when barang_id is selected or changed
            $("#barang_id").change(function() {
                let barangId = $(this).val();
                if (barangId) {
                    $.ajax({
                        url: 'detailpenjualan/get-harga-barang/' + barangId,  // Endpoint untuk mengambil harga barang
                        type: 'GET',
                        success: function(response) {
                            if (response.status) {
                                $("#harga").val(response.harga);  // Set harga in harga input field
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log("Status Code: ", xhr.status);
                            console.log("Response Text: ", xhr.responseText);
                            console.log("Error Thrown: ", error);

                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Gagal mengambil data harga'
                            });
                        }
                    });
                } else {
                    $("#harga").val('');  // Clear harga field if no barang selected
                }
            });

            // Form validation and submission
            $("#form-edit-detailpenjualan").validate({ 
                rules: { 
                    barang_id: {required: true}, 
                    harga: {required: true, number: true}, 
                    jumlah: {required: true, number: true} 
                }, 
                submitHandler: function(form) { 
                    console.log($(form).serialize());
                    $.ajax({ 
                        url: form.action, 
                        type: form.method, 
                        data: $(form).serialize(), 
                        success: function(response) { 
                            if(response.status){ 
                                $('#myModal').modal('hide'); 
                                Swal.fire({ 
                                    icon: 'success', 
                                    title: 'Berhasil', 
                                    text: response.message 
                                }); 
                                dataDetailPenjualan.ajax.reload(); 
                            } else { 
                                $('.error-text').text(''); 
                                $.each(response.msgField, function(prefix, val) { 
                                    $('#error-'+prefix).text(val[0]); 
                                }); 
                                Swal.fire({ 
                                    icon: 'error', 
                                    title: 'Terjadi Kesalahan', 
                                    text: response.message 
                                }); 
                            } 
                        }             
                    }); 
                    return false; 
                }, 
                errorElement: 'span', 
                errorPlacement: function (error, element) { 
                    error.addClass('invalid-feedback'); 
                    element.closest('.form-group').append(error); 
                }, 
                highlight: function (element, errorClass, validClass) { 
                    $(element).addClass('is-invalid'); 
                }, 
                unhighlight: function (element, errorClass, validClass) { 
                    $(element).removeClass('is-invalid'); 
                } 
            }); 
        }); 
    </script>
@endempty
