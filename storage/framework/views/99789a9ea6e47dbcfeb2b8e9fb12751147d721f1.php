<div class="modal fade" id="merchantModal" tabindex="-1" aria-labelledby="merchantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="merchantModalLabel">Update Bank Settlement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" id="updatebs">
                    <div class="mb-3">
                        <label for="internalPayment" class="form-label">MID</label>
                        <input type="text" class="form-control" name="mid" id="MiD" readonly
                            style="background-color: #e9ecef;">
                    </div>
                    <div class="mb-3">
                        <label for="internalPayment" class="form-label">Merchant Name</label>
                        <input type="hidden" class="form-control" id="id" name="id">
                        <input type="text" class="form-control" id="merchantName" readonly name="name"
                            style="background-color: #e9ecef;">
                    </div>
                    <!-- Input untuk Internal Payment -->
                    <div class="mb-3">
                        <label for="internalPayment" class="form-label">Bank Settlement</label>
                        <input type="text" class="form-control" id="internalPayment" readonly name="bs"
                            style="background-color: #e9ecef;">
                    </div>
                    <div class="mb-3">
                        <label for="newinternalPayment" class="form-label">Updated Bank Settlement</label>
                        <input type="text" class="form-control" id="newinternalPayment" name="updatebs"
                            placeholder="Input New Bank Settlement">
                    </div>
                    <div class="mb-5">
                        <label for="newinternalPayment" class="form-label">New Record Bank Settlement</label>
                        <input type="text" class="form-control" id="updatedinternalPayment" readonly name="newbs"
                            style="background-color: #e9ecef;">
                    </div>
                    <div class="d-flex justify-content-end gap-3 mt-3">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
            </div>
            
            </form>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\finance-server\resources\views/modules/reconcile/list/tabs/modalupdatebs.blade.php ENDPATH**/ ?>