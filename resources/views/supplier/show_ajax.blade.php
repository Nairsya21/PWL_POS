<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Detail Supplier</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <table class="table table-bordered table-striped table-hover table-sm"> 
                <tr> 
                    <th>ID Supplier</th> 
                    <td>{{$supplier->supplier_id}}</td> 
                </tr> 
                <tr> 
                    <th>Kode Supplier</th> 
                    <td>{{$supplier->supplier_kode}}</td> 
                </tr> 
                <tr> 
                    <th>Nama Supplier</th> 
                    <td>{{$supplier->supplier_nama}}</td> 
                </tr> 
                <tr> 
                    <th>Alamat Supplier</th> 
                    <td>{{$supplier->supplier_alamat}}</td> 
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
    $('#modal-master').on('show.bs.modal', function(event) {
        let supplierId = $(event.relatedTarget).data('id'); // Assuming supplier ID is passed via data attribute

        $.ajax({
            url: '/supplier/show_ajax/' + supplierId,
            type: 'GET',
            success: function(response) {
                if(response.status) {
                    // Populate modal fields with the retrieved data
                    $('#supplier_nama').text(response.data.supplier_nama);
                    $('#supplier_id').text(response.data.supplier_id);
                    $('#supplier_alamat').text(response.data.supplier_alamat);
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
                    text: 'Unable to fetch supplier data.'
                });
            }
        });
    });
});
</script>