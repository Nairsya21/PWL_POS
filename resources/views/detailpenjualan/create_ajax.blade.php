<form action="{{ url('/detailpenjualan/ajax') }}" method="POST" id="form-tambah-detailpenjualan"> 
    @csrf 
    <div id="modal-master" class="modal-dialog modal-lg" role="document"> 
        <div class="modal-content"> 
            <div class="modal-header"> 
                <h5 class="modal-title" id="exampleModalLabel">Tambah Detail Penjualan</h5> 
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> 
            </div> 
            <div class="modal-body">
                <div class="form-group"> 
                    <label>Penjualan</label> 
                    <select name="penjualan_id" id="penjualan_id" class="form-control" required> 
                        <option value="">- Pilih Penjualan -</option> 
                        @foreach($penjualans as $p) 
                            <option value="{{ $p->penjualan_id }}">{{ $p->penjualan_kode }}</option> 
                        @endforeach 
                    </select> 
                    <small id="error-penjualan_id" class="error-text form-text text-danger"></small> 
                </div> 
                <div class="form-group"> 
                    <label>Barang</label> 
                    <select name="barang_id" id="barang_id" class="form-control" required> 
                        <option value="">- Pilih Barang -</option> 
                        @foreach($barangs as $b) 
                            <option value="{{ $b->barang_id }}">{{ $b->barang_nama }}</option> 
                        @endforeach 
                    </select> 
                    <small id="error-barang_id" class="error-text form-text text-danger"></small> 
                </div>
                <div class="form-group"> 
                    <label>Jumlah</label> 
                    <input type="number" name="jumlah" id="jumlah" class="form-control" required> 
                    <small id="error-jumlah" class="error-text form-text text-danger"></small> 
                </div> 
                <div class="form-group"> 
                    <label>Harga</label> 
                    <input type="number" name="harga" id="harga" class="form-control" readonly> 
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
        $("#barang_id").change(function() {
            let barangId = $(this).val();
            if (barangId) {
                $.ajax({
                    url: 'detailpenjualan/get-harga-barang/' + barangId,  // Endpoint untuk mengambil harga barang
                    type: 'GET',
                    success: function(response) {
                        if (response.status) {
                            $("#harga").val(response.harga);  // Masukkan harga ke kolom harga
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                console.log("Status Code: ", xhr.status);          // Status kode HTTP dari respons
                console.log("Response Text: ", xhr.responseText);  // Teks respons dari server
                console.log("Error Thrown: ", error);              // Pesan error yang dilempar

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mengambil data harga'
                });                    }
                });
            } else {
                $("#harga").val('');  // Kosongkan kolom harga jika tidak ada barang dipilih
            }
        });
        $("#form-tambah-detailpenjualan").validate({ 
            rules: { 
                penjualan_id: {required: true}, 
                barang_id: {required: true}, 
                harga: {required: true, number: true}, 
                jumlah: {required: true, number: true} 
            }, 
            submitHandler: function(form) { 
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
