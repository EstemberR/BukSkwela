<!-- Tenant Approval Modal -->
<div class="modal fade" id="tenantApprovalModal" tabindex="-1" role="dialog" aria-labelledby="tenantApprovalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="tenantApprovalModalLabel">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Account Pending Approval
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="notifications-container">
                    <div class="error-alert">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" class="error-svg">
                                    <path clip-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" fill-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="error-prompt-container">
                                <h6 class="error-prompt-heading mb-3">This tenant account has not been approved yet.</h6>
                                <div class="error-prompt-wrap">
                                    <p class="mb-3">Please note:</p>
                                    <ul class="error-prompt-list">
                                        <li>Your account is currently under review</li>
                                        <li>Approval process takes 24-48 hours</li>
                                        <li>You'll receive an email once approved</li>
                                        <li>Contact support for urgent assistance</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <a href="mailto:support@bukskwela.com" class="btn btn-primary">
                    <i class="fas fa-envelope mr-2"></i>Contact Support
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.notifications-container {
    width: 100%;
    height: auto;
    font-size: 0.875rem;
    line-height: 1.25rem;
}

.flex {
    display: flex;
    align-items: flex-start;
}

.flex-shrink-0 {
    flex-shrink: 0;
    margin-right: 1rem;
}

.error-alert {
    border-radius: 0.375rem;
    padding: 1.5rem;
    background-color: rgb(255, 243, 205);
    border: 1px solid rgb(255, 218, 106);
}

.error-svg {
    color: #ffc107;
    width: 2rem;
    height: 2rem;
}

.error-prompt-heading {
    color: #856404;
    font-size: 1.1rem;
    line-height: 1.5;
    font-weight: 600;
    margin-bottom: 1rem;
}

.error-prompt-container {
    flex: 1;
}

.error-prompt-wrap {
    color: #666;
    font-size: 0.95rem;
    line-height: 1.5;
}

.error-prompt-list {
    padding-left: 1.25rem;
    margin-top: 0.5rem;
    list-style-type: none;
}

.error-prompt-list li {
    margin-bottom: 0.5rem;
    position: relative;
    padding-left: 1.5rem;
}

.error-prompt-list li:before {
    content: "\f00c";
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    position: absolute;
    left: 0;
    color: #ffc107;
}

/* Modal Customization */
.modal-content {
    border: none;
    border-radius: 0.5rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.modal-header {
    border-bottom: 1px solid #ffe69c;
    border-top-left-radius: 0.5rem;
    border-top-right-radius: 0.5rem;
}

.modal-footer {
    border-top: 1px solid #dee2e6;
    border-bottom-left-radius: 0.5rem;
    border-bottom-right-radius: 0.5rem;
}

.btn-primary {
    background-color: #003366;
    border-color: #003366;
    color: #FFD700;
}

.btn-primary:hover {
    background-color: #002347;
    border-color: #002347;
    color: #FFD700;
}

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-secondary:hover {
    background-color: #5a6268;
    border-color: #545b62;
}
</style>
