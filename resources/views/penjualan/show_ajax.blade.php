<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Detail Data Penjualan {{ $penjualan->penjualan_kode }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <table class="table table-bordered table-striped table-sm mb-3">
                <tr>
                    <th>Nama Pembeli</th>
                    <td>{{ $penjualan->pembeli }}</td>
                </tr>
                <tr>
                    <th>Kode Penjualan</th>
                    <td>{{ $penjualan->penjualan_kode }}</td>
                </tr>
                <tr>
                    <th>Tanggal Penjualan</th>
                    <td>{{ $penjualan->penjualan_tanggal }}</td>
                </tr>
                <tr>
                    <th>Nama User</th>
                    <td>{{ $users->nama }}</td>
                </tr>
            </table>
            <table class="table table-bordered table-striped table-hover table-sm">
                <thead>
                    <tr>
                        <th>ID Detail</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($details as $detail)
                        <tr>
                            <td>{{ $detail->detail_id }}</td>
                            <td>{{ $detail->barang->barang_nama }}</td> <!-- Relasi ke barang -->
                            <td>{{ $detail->jumlah }}</td>
                            <td>{{ $detail->harga }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-secondary">Tutup</button>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    // Ketika modal dibuka
    $('#modal-master').on('show.bs.modal', function(event) {
        let penjualanId = $(event.relatedTarget).data('id'); // ID penjualan yang dikirim dari tombol pemicu modal

        // Panggil AJAX untuk mengambil data
        $.ajax({
            url: '/penjualan/' + penjualanId + '/show_ajax', // Mengambil data detail penjualan
            type: 'GET',
            success: function(response) {
                // Menampilkan modal dengan konten yang sudah di-render dari server
                $('#modal-master .modal-content').html(response);
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Tidak dapat memuat data penjualan.'
                });
            }
        });
    });
});

</script>