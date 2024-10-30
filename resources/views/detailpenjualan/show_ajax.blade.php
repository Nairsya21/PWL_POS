<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Detail Data Penjualan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <table class="table table-bordered table-striped table-hover table-sm"> 
                <tr> 
                    <th>Kode Transaksi</th> 
                    <td>{{ $penjualans->penjualan_kode }}</td> 
                </tr> 
                <tr> 
                    <th>Barang</th> 
                    <td>{{ $barangs->barang_nama }}</td> 
                </tr> 
                <tr> 
                    <th>Harga</th> 
                    <td>{{ $details->harga }}</td> 
                </tr> 
                <tr> 
                    <th>Jumlah</th> 
                    <td>{{ $details->jumlah }}</td> 
                </tr> 
                
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-secondary">Tutup</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Assuming you are fetching data via AJAX when modal is shown
        $('#modal-master').on('show.bs.modal', function(event) {
            let detailID = $(event.relatedTarget).data('id'); // Assuming stok ID is passed via data attribute
            $.ajax({
                url: '/detailpenjualan/' + detailID+ 'show_ajax',
                type: 'GET',
                success: function(response) {
                    // Populate modal fields with the retrieved data
                    $('#penjualan_kode').val(response.data.penjualan_kode);
                    $('#barang_nama').val(response.data.barang_nama);
                    $('#harga').val(response.data.harga);
                    $('#jumlah').val(response.data.jumlah);
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Tidak dapat memuat data stok.'
                    });
                }
            });
        });
    });
</script>
