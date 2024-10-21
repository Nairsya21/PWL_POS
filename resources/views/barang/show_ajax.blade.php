<div id="modal-user" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Detail Barang</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <table class="table table-bordered table-striped table-hover table-sm"> 
                <tr> 
                    <th>ID Barang</th> 
                    <td>{{ $barang->barang_id }}</td> 
                </tr> 
                <tr> 
                    <th>Kategori</th> 
                    <td>{{ $barang->kategori->kategori_nama }}</td> 
                </tr> 
                <tr> 
                    <th>Kode Barang</th> 
                    <td>{{ $barang->barang_kode }}</td> 
                </tr> 
                <tr> 
                    <th>Nama Barang</th> 
                    <td>{{ $barang->barang_nama }}</td> 
                </tr> 
                <tr> 
                    <th>Harga Beli</th> 
                    <td>{{ $barang->harga_beli }}</td> 
                </tr> 
                <tr> 
                    <th>Harga Jual</th> 
                    <td>{{ $barang->harga_jual }}</td> 
                </tr> 
                <!-- Add more fields if necessary -->
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-secondary">Tutup</button>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#modal-user').on('show.bs.modal', function(event) {
            let userId = $(event.relatedTarget).data('id'); // Assuming user ID is passed via data attribute

            $.ajax({
                url: '/user/show_ajax/' + userId,
                type: 'GET',
                success: function(response) {
                    if (response.status) {
                        // Populate modal fields with the retrieved data
                        $('#barang_id').text(response.data.barang_id);
                        $('#barang_kode').text(response.data.barang_kode);
                        $('#barang_nama').text(response.data.barang_nama);
                        $('#harga_beli').text(response.data.harga_beli);
                        $('#harga_jual').text(response.data.harga_jual);
                        $('#kategori_nama').text(response.data.kategori_nama);
                        // Populate other fields as necessary
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Unable to fetch user data.'
                    });
                }
            });
        });
    });
</script>
