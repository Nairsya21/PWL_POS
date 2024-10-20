<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Detail Data Stok</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <table class="table table-bordered table-striped table-hover table-sm"> 
                <tr> 
                    <th>ID Stok</th> 
                    <td>{{ $stock->stok_id }}</td> 
                </tr> 
                <tr> 
                    <th>Nama Barang</th> 
                    <td>{{ $barangs->barang_nama }}</td> 
                </tr> 
                <tr> 
                    <th>Nama Supplier</th> 
                    <td>{{ $suppliers->supplier_nama }}</td> 
                </tr> 
                <tr> 
                    <th>User</th> 
                    <td>{{ auth()->user()->nama }}</td> 
                </tr> 
                <tr> 
                    <th>Tanggal</th> 
                    <td>{{ $stock->stok_tanggal }}</td> 
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
            let stokId = $(event.relatedTarget).data('id'); // Assuming stok ID is passed via data attribute
            $.ajax({
                url: '/stok/show/' + stokId,
                type: 'GET',
                success: function(response) {
                    // Populate modal fields with the retrieved data
                    $('#barang_id').val(response.data.barang_id);
                    $('#supplier_id').val(response.data.supplier_id);
                    $('#stok_tanggal').val(response.data.stok_tanggal);
                    $('#stok_jumlah').val(response.data.stok_jumlah);
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
