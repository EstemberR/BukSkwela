@props(['title' => 'Success!', 'message' => '', 'type' => 'success'])

<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-check-circle text-success mb-3" style="font-size: 3rem;"></i>
                    <p class="mb-0">{{ $message }}</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showSuccessModal(title, message) {
        const modal = new bootstrap.Modal(document.getElementById('successModal'));
        document.getElementById('successModalLabel').textContent = title;
        document.querySelector('#successModal .modal-body p').textContent = message;
        modal.show();
    }
</script>
@endpush
