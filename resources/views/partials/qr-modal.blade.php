{{-- MODAL QR-CODE (login & register). Butuh qrcode.min.js dimuat via @push('head'). --}}
<div class="modal fade" id="ModalQrCode" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header d-flex align-items-center">
                <h4 class="modal-title" hidden>{{ url()->current() }}</h4>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12" align="center">
                            <br>
                            <div id="qrcode"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup&emsp;<i class="ti ti-x"></i></button>
            </div>
        </div>
    </div>
</div>
<script>
    (function () { try { new QRCode("qrcode", "{{ url()->current() }}"); } catch (e) {} })();
</script>
