@extends('tenant.layouts.app')

@section('title', 'Requirements')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<style>
    .category-button {
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 5px;
        background: rgb(6, 29, 62);
        font-family: "Montserrat", sans-serif;
        box-shadow: 0px 2px 8px 0px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        cursor: pointer;
        border: none;
        margin: 0;
        min-width: 120px;
    }

    .category-button:after {
        content: " ";
        width: 0%;
        height: 100%;
        background: #ffd401;
        position: absolute;
        transition: all 0.4s ease-in-out;
        right: 0;
    }

    .category-button:hover::after {
        right: auto;
        left: 0;
        width: 100%;
    }

    .category-button span {
        text-align: center;
        text-decoration: none;
        width: 100%;
        padding: 10px 15px;
        color: #fff;
        font-size: 0.9em;
        font-weight: 700;
        letter-spacing: 0.2em;
        z-index: 20;
        transition: all 0.3s ease-in-out;
    }

    .category-button:hover span {
        color:rgb(11, 29, 54);
        animation: scaleUp 0.3s ease-in-out;
    }

    .category-button.active {
        background: #ffd401;
    }

    .category-button.active span {
        color: #183153;
    }

    @keyframes scaleUp {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(0.95);
        }

        100% {
            transform: scale(1);
        }
    }

    .category-buttons {
        display: flex;
        justify-content: flex-start;
        gap: 3px;
        margin-top: 8px;
    }

    .search-wrapper {
        margin-top: 8px;
    }

    .search-container {
        position: relative;
        margin-top: 3px;
        margin-left: auto;
    }

    .search-input {
        padding: 8px 15px;
        padding-left: 35px;
        border: 2px solid rgb(6, 29, 62);
        border-radius: 5px;
        font-family: "Montserrat", sans-serif;
        font-size: 0.9em;
        width: 250px;
        transition: all 0.3s ease;
    }

    .search-input:focus {
        outline: none;
        border-color: #ffd401;
        box-shadow: 0 0 5px rgba(255, 212, 1, 0.3);
    }

    .search-icon {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: rgb(6, 29, 62);
    }
    
    /* Card shadows */
    .card {
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
        border: 1px solid #f0f0f0;
    }
    
    /* Drag drop zone styling */
    .drag-drop-zone {
        transition: all 0.3s ease;
        background-color: #fafafa;
        min-height: 150px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.01);
    }
    
    .drag-drop-zone.dragover {
        background-color: #f8f8f8;
        border-color: #0d6efd !important;
    }
    
    /* Modal content */
    .modal-content {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Requirements</h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createFolderModal">
                        <i class="fas fa-folder-plus"></i> Add New Requirements
                    </button>
                </div>

                <div class="card-body pt-0">
                    <!-- Search and filters -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="category-buttons" role="group" aria-label="Category filters">
                                <button type="button" class="category-button active" data-category="Regular">
                                    <span>REGULAR</span>
                                </button>
                                <button type="button" class="category-button" data-category="Irregular">
                                    <span>IRREGULAR</span>
                                </button>
                                <button type="button" class="category-button" data-category="Probation">
                                    <span>PROBATION</span>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="search-wrapper">
                                <form id="searchForm" method="GET">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control" 
                                               placeholder="Search folders..." 
                                               id="searchInput" 
                                               name="search"
                                               value="{{ request('search') }}"
                                               autocomplete="off">
                                               <button type="submit" class="btn btn-primary">Search</button>

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-4">
                            <div class="table-responsive">
                                <table class="table table-hover" id="folderContentsTable">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th class="fw-bold">Name</th>
                                            <th class="fw-bold text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="3" class="text-center">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <!-- Pagination -->
                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <div class="text-muted pagination-info">
                                        Showing 0 to 0 of 0 entries
                                    </div>
                                    <div class="pagination-container">
                                        <!-- Pagination links will be inserted here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Folder Modal -->
<div class="modal fade" id="createFolderModal" tabindex="-1" aria-labelledby="createFolderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createFolderModalLabel">Create New Folder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createFolderForm">
                    <div class="mb-3">
                        <label for="folderName" class="form-label">Folder Name</label>
                        <input type="text" class="form-control" id="folderName" required>
                    </div>
                    <div class="mb-3">
                        <label for="folderType" class="form-label">Student Category</label>
                        <select class="form-select" id="folderType" required>
                            <option value="Regular">Regular</option>
                            <option value="Probation">Probation</option>
                            <option value="Irregular">Irregular</option>
                        </select>
                    </div>
                    <input type="hidden" id="parent-id-input" value="">
                    <input type="hidden" id="folder-type-prefix" value="">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="createFolderBtn">Create Folder</button>
            </div>
        </div>
    </div>
</div>

<!-- Rename Modal -->
<div class="modal fade" id="renameFolderModal" tabindex="-1" aria-labelledby="renameFolderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="renameFolderModalLabel">Rename Folder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="renameFolderForm">
                    <div class="mb-3">
                        <label for="newFolderName" class="form-label">New Name</label>
                        <input type="text" class="form-control" id="newFolderName" required>
                    </div>
                    <input type="hidden" id="folder-id-to-rename" value="">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="renameFolderBtn">Rename</button>
            </div>
        </div>
    </div>
</div>



<!-- Folder Contents Modal -->
<div class="modal fade" id="folderContentsModal" tabindex="-1" aria-labelledby="folderContentsModalLabel">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="folderContentsModalLabel">Files</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span class="visually-hidden">Close modal</span>
                </button>
            </div>
            <div class="modal-body">
                <nav aria-label="breadcrumb">
                  
                </nav>
                <div class="mb-3">
                    <form id="uploadFileFormModal" class="d-flex flex-column gap-2">
                        <div id="dragDropZone" class="drag-drop-zone p-4 border border-2 border-dashed rounded text-center">
                            <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                            <p class="mb-0">Drag and drop files here or</p>
                            <div class="mt-2">
                                <label for="modalFileUpload" class="btn btn-outline-primary mb-0">
                                    <i class="fas fa-folder-open"></i> Browse Files
                                </label>
                                <input type="file" class="d-none" id="modalFileUpload" name="file">
                            </div>
                        </div>
                        <div id="filePreview" class="d-none mt-2 p-3 border rounded bg-light">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file me-2 text-primary"></i>
                                <span id="selectedFileName" class="text-truncate me-auto">No file selected</span>
                                <button type="button" id="removeFile" class="btn btn-sm btn-outline-danger ms-2">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="progress mt-2" style="height: 5px;">
                                <div id="uploadProgress" class="progress-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary" id="modalUploadBtn" disabled>
                                <i class="fas fa-upload" aria-hidden="true"></i> Upload
                            </button>
                        </div>
                        <input type="hidden" id="modalCurrentFolderId" name="folderId">
                    </form>
                </div>
                <style>
                    .drag-drop-zone {
                        transition: all 0.3s ease;
                        background-color: #f8f9fa;
                        min-height: 150px;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        cursor: pointer;
                    }
                    .drag-drop-zone.dragover {
                        background-color: #e9ecef;
                        border-color: #0d6efd !important;
                    }
                    .drag-drop-zone i {
                        color: #6c757d;
                    }
                    .drag-drop-zone:hover {
                        background-color: #e9ecef;
                    }
                    #filePreview {
                        transition: all 0.3s ease;
                    }
                    #selectedFileName {
                        max-width: 250px;
                    }
                </style>
            </div>
            <div class="modal-footer">
                <!-- Remove close button from footer -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
    let currentFolderId = null;
    let folderContentsModal = null;
    let currentCategory = 'Regular';

    // Add the root folder constant at the top of your script
    const ROOT_FOLDER_URL = 'https://drive.google.com/drive/folders/1ODyX_npnV8qy99_S1OyVdlHiCewMRhtJ';

    // Initialize the page
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize category filter buttons
        const categoryButtons = document.querySelectorAll('[data-category]');
        categoryButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Update active state
                categoryButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                
                // Update current category and reload folders
                currentCategory = button.dataset.category;
                loadFolder(currentFolderId);
            });
        });

        // Initialize the folder contents modal
        const folderContentsModalEl = document.getElementById('folderContentsModal');
        folderContentsModal = new bootstrap.Modal(folderContentsModalEl);
        
        // Handle modal events for accessibility
        folderContentsModalEl.addEventListener('hidden.bs.modal', function () {
            // Reset modal content
            document.getElementById('modalFileContents').innerHTML = `
                <div class="col-12 text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;
            
            // Reset file input
            const fileInput = document.getElementById('modalFileUpload');
            if (fileInput) {
                fileInput.value = '';
            }
        });

        folderContentsModalEl.addEventListener('shown.bs.modal', function () {
            // Set focus to the file input when modal opens
            const fileInput = document.getElementById('modalFileUpload');
            if (fileInput) {
                fileInput.focus();
            }
        });
        
        // Load initial contents
        loadFolder();
        
        // Set up event listeners
        setupCreateFolderHandler();
        setupRenameFolderHandler();
        setupGoogleDriveStatus();
        setupModalFileUploadHandler();

        // Search functionality
        const searchForm = document.getElementById('searchForm');
        const searchInput = document.getElementById('searchInput');
        let searchTimeout;

        // Handle search input with debounce
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const searchTerm = this.value.toLowerCase();
                const folderRows = document.querySelectorAll('#folderContentsTable tbody tr');
                
                folderRows.forEach(row => {
                    const folderName = row.querySelector('td:first-child')?.textContent.toLowerCase() || '';
                    
                    if (searchTerm === '') {
                        row.style.display = ''; // Show all when search is empty
                    } else if (folderName.includes(searchTerm)) {
                        row.style.display = ''; // Show matching rows
                    } else {
                        row.style.display = 'none'; // Hide non-matching rows
                    }
                });

                // Show "no results" message if all rows are hidden
                const visibleRows = Array.from(folderRows).filter(row => row.style.display !== 'none');
                const tbody = document.querySelector('#folderContentsTable tbody');
                const noResultsRow = tbody.querySelector('.no-results-row');

                if (visibleRows.length === 0 && searchTerm !== '') {
                    if (!noResultsRow) {
                        const tr = document.createElement('tr');
                        tr.className = 'no-results-row';
                        tr.innerHTML = '<td colspan="2" class="text-center">No folders found matching your search</td>';
                        tbody.appendChild(tr);
                    }
                } else if (noResultsRow) {
                    noResultsRow.remove();
                }
            }, 300); // 300ms debounce
        });
    });

    function updateDebugTimestamp() {
        const timestamp = document.getElementById('debug-timestamp');
        if (timestamp) {
            timestamp.textContent = `Last updated: ${new Date().toLocaleTimeString()}`;
        }
    }

    function updateBreadcrumbs(path) {
        const breadcrumbList = document.getElementById('modalFolderPath');
        if (!breadcrumbList) {
            console.warn('Breadcrumb list element not found');
            return;
        }

        breadcrumbList.innerHTML = `
            <li class="breadcrumb-item">
                <a href="#" onclick="loadFolder(null); return false;">Root</a>
            </li>
        `;

        if (path && Array.isArray(path) && path.length > 0) {
            path.forEach((item, index) => {
                if (!item || typeof item !== 'object') return;
                
                const li = document.createElement('li');
                li.className = 'breadcrumb-item';
                if (index === path.length - 1) {
                    li.classList.add('active');
                    li.textContent = item.name || 'Unnamed';
                } else {
                    li.innerHTML = `<a href="#" onclick="loadFolder('${item.id || ''}'); return false;">${item.name || 'Unnamed'}</a>`;
                }
                breadcrumbList.appendChild(li);
            });
        }
    }

    function setupCreateFolderHandler() {
        const createFolderBtn = document.getElementById('createFolderBtn');
        const createFolderModal = document.getElementById('createFolderModal');
        const bsCreateFolderModal = new bootstrap.Modal(createFolderModal);

        createFolderBtn.addEventListener('click', async function() {
            const folderNameInput = document.getElementById('folderName');
            const folderTypeInput = document.getElementById('folderType');
            const folderName = folderNameInput.value.trim();
            const folderType = folderTypeInput.value;

            if (!folderName || !folderType) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Input',
                    text: 'Please enter a folder name and select a folder type'
                });
                return;
            }

            // Disable the button and show loading state
            createFolderBtn.disabled = true;
            createFolderBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Creating...';

            try {
                const response = await fetch('{{ route("tenant.admin.requirements.folder.create", ["tenant" => tenant("id")]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name: folderName,
                        category: folderType,
                        parent_id: currentFolderId
                    })
                });

                const result = await response.json();

                if (result.success) {
                    // Properly hide the modal and remove backdrop
                    bsCreateFolderModal.hide();
                    // Remove modal backdrop and any modal-open classes
                    document.body.classList.remove('modal-open');
                    const modalBackdrops = document.getElementsByClassName('modal-backdrop');
                    while (modalBackdrops.length > 0) {
                        modalBackdrops[0].parentNode.removeChild(modalBackdrops[0]);
                    }
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Folder Created',
                        text: 'The folder has been created successfully',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });

                    // Clear the inputs
                    folderNameInput.value = '';
                    
                    // Update current category to match the created folder's category
                    currentCategory = folderType;
                    
                    // Update category button UI
                    const categoryButtons = document.querySelectorAll('[data-category]');
                    categoryButtons.forEach(btn => {
                        btn.classList.remove('active');
                        if (btn.dataset.category === folderType) {
                            btn.classList.add('active');
                        }
                    });
                    
                    // Reload the current folder contents
                    loadFolder(currentFolderId);
                } else {
                    throw new Error(result.message || 'Failed to create folder');
                }
            } catch (error) {
                console.error('Create folder error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Failed to create folder'
                });
            } finally {
                // Reset the button state
                createFolderBtn.disabled = false;
                createFolderBtn.innerHTML = 'Create Folder';
            }
        });

        // Add event listener for modal hidden event
        createFolderModal.addEventListener('hidden.bs.modal', function () {
            // Reset form and button state when modal is closed
            const folderNameInput = document.getElementById('folderName');
            if (folderNameInput) {
                folderNameInput.value = '';
            }
            createFolderBtn.disabled = false;
            createFolderBtn.innerHTML = 'Create Folder';
            
            // Ensure backdrop and modal-open class are removed
            document.body.classList.remove('modal-open');
            const modalBackdrops = document.getElementsByClassName('modal-backdrop');
            while (modalBackdrops.length > 0) {
                modalBackdrops[0].parentNode.removeChild(modalBackdrops[0]);
            }
        });
    }

    function setupRenameFolderHandler() {
        const renameFolderModal = document.getElementById('renameFolderModal');
        const bsRenameFolderModal = new bootstrap.Modal(renameFolderModal);
        const renameFolderBtn = document.getElementById('renameFolderBtn');

        // Event delegation for rename buttons
        document.addEventListener('click', function(e) {
            if (e.target.closest('.rename-folder')) {
                const button = e.target.closest('.rename-folder');
                const folderId = button.dataset.folderId;
                const folderName = button.closest('tr').querySelector('a').textContent;
                
                document.getElementById('folder-id-to-rename').value = folderId;
                document.getElementById('newFolderName').value = folderName;
                bsRenameFolderModal.show();
            }
        });

        renameFolderBtn.addEventListener('click', async function() {
            const folderId = document.getElementById('folder-id-to-rename').value;
            const newName = document.getElementById('newFolderName').value.trim();

            if (!newName) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Name',
                    text: 'Please enter a valid folder name'
                });
                return;
            }

            // Disable the button and show loading state
            renameFolderBtn.disabled = true;
            renameFolderBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Renaming...';

            try {
                console.log('Renaming folder:', {
                    folderId,
                    newName
                });

                const response = await fetch('{{ route("tenant.admin.requirements.folder.rename", ["tenant" => tenant("id"), "folderId" => "__id__"]) }}'.replace('__id__', folderId), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name: newName
                    })
                });

                const result = await response.json();
                console.log('Rename folder response:', result);

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Folder Renamed',
                        text: 'The folder has been renamed successfully',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });

                    bsRenameFolderModal.hide();
                    loadFolder(currentFolderId);
                } else {
                    throw new Error(result.message || 'Failed to rename folder');
                }
            } catch (error) {
                console.error('Rename folder error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Failed to rename folder'
                });
            } finally {
                // Reset the button state
                renameFolderBtn.disabled = false;
                renameFolderBtn.innerHTML = 'Rename';
            }
        });
    }


    function setupGoogleDriveStatus() {
        const checkStatusBtn = document.getElementById('checkGoogleStatus');
        if (checkStatusBtn) {
            checkStatusBtn.addEventListener('click', async function() {
                try {
                    const btn = this;
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';
                    
                    const response = await fetch('/google/status', {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    
                    if (!response.ok) {
                        throw new Error(`Server error: ${response.status}`);
                    }
                    
                    const data = await response.json();
                    console.log('Google Drive status:', data);
                    
                    if (data.success) {
                        // Create HTML for the detailed results
                        let statusHtml = '<div class="mb-3 text-left">';
                        
                        // Config status
                        statusHtml += '<h5>Configuration Status</h5>';
                        statusHtml += '<ul class="list-group mb-3">';
                        for (const [key, value] of Object.entries(data.config_status)) {
                            if (key !== 'config_values') {
                                const icon = value ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>';
                                statusHtml += `<li class="list-group-item">${icon} ${key.replace(/_/g, ' ')}</li>`;
                            }
                        }
                        statusHtml += '</ul>';
                        
                        // Client status
                        statusHtml += '<h5>Client Status</h5>';
                        statusHtml += '<ul class="list-group mb-3">';
                        statusHtml += `<li class="list-group-item">${data.client_status.initialized ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>'} Client initialized</li>`;
                        statusHtml += `<li class="list-group-item">${data.client_status.has_valid_token ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>'} Has valid token</li>`;
                        if (data.client_status.token_error) {
                            statusHtml += `<li class="list-group-item text-danger"><i class="fas fa-exclamation-triangle"></i> Token error: ${data.client_status.token_error}</li>`;
                        }
                        statusHtml += '</ul>';
                        
                        // Drive status
                        statusHtml += '<h5>Drive Status</h5>';
                        statusHtml += '<ul class="list-group mb-3">';
                        statusHtml += `<li class="list-group-item">${data.drive_status.accessible ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>'} Drive accessible</li>`;
                        if (data.drive_status.error) {
                            statusHtml += `<li class="list-group-item text-danger"><i class="fas fa-exclamation-triangle"></i> Drive error: ${data.drive_status.error}</li>`;
                        }
                        
                        // Sample files
                        if (data.drive_status.files_sample && data.drive_status.files_sample.length > 0) {
                            statusHtml += '<li class="list-group-item"><strong>Sample files:</strong><ul class="mt-2">';
                            data.drive_status.files_sample.forEach(file => {
                                statusHtml += `<li>${file.name} (ID: ${file.id.substring(0, 8)}...)</li>`;
                            });
                            statusHtml += '</ul></li>';
                        } else {
                            statusHtml += '<li class="list-group-item text-warning"><i class="fas fa-exclamation-triangle"></i> No files found in sample</li>';
                        }
                        statusHtml += '</ul>';
                        
                        // Environment info
                        statusHtml += '<h5>Environment Info</h5>';
                        statusHtml += '<ul class="list-group">';
                        statusHtml += `<li class="list-group-item"><strong>PHP Version:</strong> ${data.php_version}</li>`;
                        statusHtml += `<li class="list-group-item"><strong>Environment:</strong> ${data.environment}</li>`;
                        statusHtml += `<li class="list-group-item"><strong>Time:</strong> ${data.time}</li>`;
                        statusHtml += '</ul>';
                        
                        statusHtml += '</div>';
                        
                        Swal.fire({
                            title: 'Google Drive Status',
                            icon: data.drive_status.accessible ? 'success' : 'warning',
                            html: statusHtml,
                            width: '600px'
                        });
                    } else {
                        throw new Error(data.message || 'Failed to check Google Drive status');
                    }
                } catch (error) {
                    console.error('Google Drive status check error:', error);
                    Swal.fire({
                        title: 'Error Checking Status',
                        icon: 'error',
                        text: error.message || 'Failed to check Google Drive status',
                        footer: 'Check browser console for more details'
                    });
                } finally {
                    const btn = document.getElementById('checkGoogleStatus');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-sync"></i> Check Status';
                }
            });
        }
    }

    function loadFolder(folderId = null, page = 1) {
        console.log('Loading folder:', folderId);
        console.log('Current category:', currentCategory);
        const tenantId = '{{ tenant("id") }}';
        console.log('Current tenant ID:', tenantId);
        
        if (!tenantId) {
            console.error('Tenant ID is missing or undefined');
            $('#folderContentsTable tbody').html('<tr><td colspan="3" class="text-center text-danger">Error: Could not determine tenant ID. Please refresh the page or contact support.</td></tr>');
            return;
        }
        
        currentFolderId = folderId;
        
        $('#folderContentsTable tbody').html('<tr><td colspan="3" class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
        
        // Use the Laravel route helper
        let url = "{{ route('tenant.admin.requirements.folder.contents', ['tenant' => tenant('id')]) }}";
        if (folderId) {
            url += "/" + encodeURIComponent(folderId);
        }
        
        // Add category parameter to the URL
        url += (url.includes('?') ? '&' : '?') + 'category=' + encodeURIComponent(currentCategory);
        
        // Add page parameter
        url += '&page=' + page;
        
        // Add a debug parameter with the tenant ID
        url += '&debug_tenant=' + encodeURIComponent(tenantId);
        
        console.log('Requesting URL:', url);
        
        $.ajax({
            url: url,
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('Folder contents response:', response);
                
                if (response.success) {
                    if (response.files && Array.isArray(response.files) && response.files.length > 0) {
                        console.log('Found folders to display:', response.files.length);
                        displayFolderContents(response.files, response.path || []);
                        
                        // Update pagination info - check if pagination exists first
                        if (response.pagination) {
                        const pagination = response.pagination;
                            const from = pagination.from || 0;
                            const to = pagination.to || 0;
                            const total = pagination.total || 0;
                            
                            $('.pagination-info').html(`Showing ${from} to ${to} of ${total} entries`);
                        
                        // Generate pagination links
                        let paginationHtml = '';
                        if (pagination.last_page > 1) {
                            paginationHtml = '<ul class="pagination">';
                            
                            // Previous button
                            if (pagination.current_page > 1) {
                                paginationHtml += `<li class="page-item">
                                    <a class="page-link" href="#" onclick="loadFolder('${folderId}', ${pagination.current_page - 1}); return false;">Previous</a>
                                </li>`;
                            } else {
                                paginationHtml += '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
                            }
                            
                            // Page numbers
                            for (let i = 1; i <= pagination.last_page; i++) {
                                if (i === pagination.current_page) {
                                    paginationHtml += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
                                } else {
                                    paginationHtml += `<li class="page-item">
                                        <a class="page-link" href="#" onclick="loadFolder('${folderId}', ${i}); return false;">${i}</a>
                                    </li>`;
                                }
                            }
                            
                            // Next button
                            if (pagination.current_page < pagination.last_page) {
                                paginationHtml += `<li class="page-item">
                                    <a class="page-link" href="#" onclick="loadFolder('${folderId}', ${pagination.current_page + 1}); return false;">Next</a>
                                </li>`;
                            } else {
                                paginationHtml += '<li class="page-item disabled"><span class="page-link">Next</span></li>';
                            }
                            
                            paginationHtml += '</ul>';
                        }
                        
                        // Update pagination container
                        $('.pagination-container').html(paginationHtml);
                    } else {
                            // No pagination data
                            $('.pagination-info').html('Showing 0 to 0 of 0 entries');
                            $('.pagination-container').empty();
                        }
                    } else {
                        console.log('No folders found in response or files array is empty/invalid');
                        $('#folderContentsTable tbody').html('<tr><td colspan="3" class="text-center">No folders found in this category. Create a new folder to get started.</td></tr>');
                        $('.pagination-info').html('Showing 0 to 0 of 0 entries');
                        $('.pagination-container').empty();
                    }
                } else {
                    console.error('Error in response - success flag is false:', response.message);
                    $('#folderContentsTable tbody').html('<tr><td colspan="3" class="text-center text-danger">' + (response.message || 'Failed to load folder contents') + '</td></tr>');
                    $('.pagination-info').html('Showing 0 to 0 of 0 entries');
                    $('.pagination-container').empty();
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', xhr.status, error);
                console.error('Response text:', xhr.responseText);
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    console.error('Parsed error response:', errorResponse);
                } catch (e) {
                    console.error('Failed to parse error response');
                }
                $('#folderContentsTable tbody').html('<tr><td colspan="3" class="text-center text-danger">Failed to load folder contents. Please try again. Error: ' + error + '</td></tr>');
                $('.pagination-info').html('Showing 0 to 0 of 0 entries');
                $('.pagination-container').empty();
            }
        });
    }

    function showFolderContentsInModal(folderId, folderName) {
        console.log('Opening modal for folder:', { folderId, folderName });
        
        $('#folderContentsModalLabel').text('Files in: ' + folderName);
        $('#modalCurrentFolderId').val(folderId);
        
        $('#modalFileUpload').val('');
        
        $('#modalFileContents').html(`
            <div class="col-12 text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);
        
        folderContentsModal.show();
        
        setTimeout(() => {
            loadFolderContents(folderId);
        }, 100);
    }

    function loadFolderContents(folderId) {
        console.log('Loading folder contents for modal:', folderId);
        
        // Use the Laravel route helper
        let url = "{{ route('tenant.admin.requirements.folder.contents', ['tenant' => tenant('id')]) }}";
        if (folderId) {
            url += "/" + encodeURIComponent(folderId);
        }
        
        // Add category parameter to the URL
        url += (url.includes('?') ? '&' : '?') + 'category=' + encodeURIComponent(currentCategory);

        console.log('Requesting URL:', url);

        $.ajax({
            url: url,
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('Modal folder contents response:', response);
                if (response.success) {
                    // Check if files is a valid array
                    if (response.files && Array.isArray(response.files)) {
                        console.log('Files to display:', response.files.length);
                    displayModalContents(response.files);
                    } else {
                        console.error('Invalid or missing files array in response:', response);
                        $('#modalFileContents').html('<div class="col-12 text-center text-warning">No files found or invalid data format</div>');
                    }
                } else {
                    console.error('Error loading folder contents:', response.message);
                    $('#modalFileContents').html('<div class="col-12 text-center text-danger">' + (response.message || 'Failed to load contents') + '</div>');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error, xhr.responseText);
                $('#modalFileContents').html('<div class="col-12 text-center text-danger">Failed to load contents. Please try again.</div>');
            }
        });
    }

    function displayModalContents(files) {
        console.log('Displaying modal contents, received files:', files);
        
        if (!Array.isArray(files)) {
            console.error('Files is not an array:', files);
            $('#modalFileContents').html('<div class="col-12 text-center text-danger">Invalid data format received</div>');
            return;
        }

        // Get current tenant ID
        const currentTenantId = '{{ tenant("id") }}';
        
        // Filter out folders and only include files belonging to the current tenant
        const filesList = files.filter(file => {
            // Skip folders, we only want files
            if (file.mimeType === 'application/vnd.google-apps.folder') return false;
            
            // If the file has no name, skip it
            if (!file.name) {
                console.warn('File has no name property:', file);
                return false;
            }
            
            // Check if file belongs to current tenant
            let isTenantMatch = false;
            
            // First try exact prefix match
            if (file.name.startsWith(`[${currentTenantId}]`)) {
                isTenantMatch = true;
            } else if (file.name.startsWith('[')) {
                // Extract the tenant ID for comparison
                const tenantMatch = file.name.match(/^\[([^\]]+)\]/);
                const fileTenantId = tenantMatch ? tenantMatch[1] : null;
                
                if (fileTenantId) {
                    // Normalize both tenant IDs for comparison
                    const normalizedFileTenantId = fileTenantId.toLowerCase().trim();
                    const normalizedCurrentTenantId = currentTenantId.toLowerCase().trim();
                    
                    // Check if normalized versions match or one contains the other
                    isTenantMatch = normalizedFileTenantId === normalizedCurrentTenantId || 
                        normalizedFileTenantId.includes(normalizedCurrentTenantId) || 
                        normalizedCurrentTenantId.includes(normalizedFileTenantId);
                        
                    console.log('File tenant ID check:', {
                        filename: file.name,
                        fileTenantId,
                        currentTenantId,
                        normalizedFileTenantId,
                        normalizedCurrentTenantId,
                        isTenantMatch
                    });
                }
            } else {
                // For files without tenant prefix, include them
                isTenantMatch = true;
            }
            
            // Skip files that don't belong to this tenant
            if (!isTenantMatch) {
                console.log('Skipping file, tenant mismatch:', file.name);
                return false;
            }
            
            return true;
        });
        
        console.log('Filtered files list:', filesList);
        
        let container = $('#modalFileContents');
        container.removeClass('d-none'); // Show the container when we have content to display
        container.empty();

        if (filesList.length === 0) {
            console.log('No files found in folder');
            container.html('<div class="col-12 text-center">No files in this folder</div>');
            return;
        }

        filesList.sort((a, b) => a.name.localeCompare(b.name));

        container.append(`
            <div class="col-12">
                <div class="list-group">
                    ${filesList.map(file => {
                        const icon = getFileIcon(file.mimeType);
                        const name = escapeHtml(file.name);
                        const modifiedDate = file.modifiedTime ? new Date(file.modifiedTime).toLocaleString() : 'N/A';
                        const size = file.size ? formatFileSize(file.size) : 'N/A';
                        
                        return `
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <div>
                                        <i class="fas ${icon} text-primary me-2"></i>
                                        <span class="fw-bold">${name}</span>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted me-3">Size: ${size}</small>
                                        <small class="text-muted">Modified: ${modifiedDate}</small>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a href="${file.webViewLink}" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                        <i class="fas fa-external-link-alt"></i> Open
                                    </a>
                                    <button class="btn btn-sm btn-secondary" disabled title="Delete functionality not available">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                    }).join('')}
                </div>
            </div>
        `);
    }

    function displayFolderContents(files, path) {
        console.log('Displaying folder contents, received files:', files.length);
        
        // Check if path is valid
        if (!path || !Array.isArray(path)) {
            console.warn('Invalid path data received:', path);
            path = [];
        }
        
        updateBreadcrumbs(path);

        // Sort files (folders first, then alphabetical)
        try {
            files.sort((a, b) => {
                const aIsFolder = a.mimeType === 'application/vnd.google-apps.folder';
                const bIsFolder = b.mimeType === 'application/vnd.google-apps.folder';
                
                if (aIsFolder && !bIsFolder) return -1;
                if (!aIsFolder && bIsFolder) return 1;
                    
                // Use name as a fallback if it exists
                const aName = a.name || '';
                const bName = b.name || '';
                return aName.localeCompare(bName);
            });
        } catch (error) {
            console.error('Error sorting files:', error);
        }

        let foldersBody = $('#folderContentsTable tbody');
        foldersBody.empty();

        // If we received no files, show appropriate message 
        if (!files || !Array.isArray(files) || files.length === 0) {
            foldersBody.html('<tr><td colspan="3" class="text-center">No folders found. Folder list is empty.</td></tr>');
            return;
        }

        // Get current tenant ID
        const currentTenantId = '{{ tenant("id") }}';
        console.log('Current tenant ID for filtering:', currentTenantId);
        
        // Log each folder before filtering
        console.log('All folders before filtering:');
        files.forEach((file, index) => {
            if (file && file.mimeType === 'application/vnd.google-apps.folder') {
                console.log(`Folder ${index}: ${file.name}`);
            }
        });

        // Filter folders by tenant ID and category
        try {
            var folders = files.filter(file => {
                // Validate file object
                if (!file || typeof file !== 'object') {
                    console.warn('Invalid file object in files array:', file);
                    return false;
                }
                
                // Check if it's a folder
                if (!file.mimeType || file.mimeType !== 'application/vnd.google-apps.folder') {
                    return false;
                }
                
                // Check if name exists
                if (!file.name) {
                    console.warn('File has no name property:', file);
                    return false;
                }
                
                console.log('Checking folder:', file.name);
                
                // Check if folder belongs to current tenant
                const tenantMatch = file.name.match(/^\[([^\]]+)\]/);
                const folderTenantId = tenantMatch ? tenantMatch[1] : null;
                
                // Try to normalize tenant IDs for comparison
                const normalizedFolderTenantId = folderTenantId ? folderTenantId.toLowerCase().trim() : null;
                const normalizedCurrentTenantId = currentTenantId ? currentTenantId.toLowerCase().trim() : null;
                
                // Log this check for debugging
                console.log('Tenant check for folder:', { 
                    folderName: file.name,
                    tenantMatch: tenantMatch ? tenantMatch[0] : null,
                    folderTenantId: folderTenantId,
                    normalizedFolderTenantId: normalizedFolderTenantId,
                    currentTenantId: currentTenantId,
                    normalizedCurrentTenantId: normalizedCurrentTenantId,
                    exactMatch: folderTenantId === currentTenantId,
                    normalizedMatch: normalizedFolderTenantId === normalizedCurrentTenantId
                });
                
                // Modified tenant matching logic - more permissive
                let isTenantMatch = false;
                
                // 1. If the folder has no tenant prefix at all, include it
                if (!folderTenantId) {
                    isTenantMatch = true;
                    console.log('Including folder with no tenant prefix:', file.name);
                } 
                // 2. Check for an exact match (case insensitive)
                else if (normalizedFolderTenantId === normalizedCurrentTenantId) {
                    isTenantMatch = true;
                    console.log('Found exact match using normalized tenant IDs');
                }
                // 3. Check if one tenant ID contains the other (for partial matches)
                else if (normalizedFolderTenantId && normalizedCurrentTenantId &&
                    (normalizedFolderTenantId.includes(normalizedCurrentTenantId) || 
                     normalizedCurrentTenantId.includes(normalizedFolderTenantId))) {
                    isTenantMatch = true;
                    console.log('Found partial tenant ID match');
                }
                
                // Skip folders that don't belong to this tenant
                if (!isTenantMatch) {
                    console.log('Skipping folder, tenant mismatch:', file.name, folderTenantId, currentTenantId);
                    return false;
                }
            
            // Extract category from folder name
            const categoryMatch = file.name.match(/\[(Regular|Irregular|Probation)\]/);
            const folderCategory = categoryMatch ? categoryMatch[1] : 'Regular';
            
            // Only show folders matching current category - more flexible matching
            let categoryMatches = false;
            
            // 1. If the folder has an exact category match
            if (folderCategory === currentCategory) {
                categoryMatches = true;
            }
            // 2. If no category is specified in the folder name but we're showing Regular
            else if (!categoryMatch && currentCategory === 'Regular') {
                categoryMatches = true;
                console.log('Including folder with no category as Regular:', file.name);
            }
            // 3. Case insensitive match
            else if (folderCategory.toLowerCase() === currentCategory.toLowerCase()) {
                categoryMatches = true;
                console.log('Found case-insensitive category match');
            }
            
            console.log('Category check:', { 
                folderName: file.name,
                folderCategory: folderCategory, 
                currentCategory: currentCategory,
                matches: categoryMatches
            });
            
            const shouldInclude = categoryMatches;
            console.log(`Final decision for ${file.name}: ${shouldInclude ? 'INCLUDE' : 'EXCLUDE'}`);
            
            return shouldInclude;
        });
            
            console.log('Filtered folders for display:', folders.length);
            
            // Log folders after filtering
            if (folders.length > 0) {
                console.log('Folders that passed filtering:');
                folders.forEach((folder, index) => {
                    console.log(`Folder ${index}: ${folder.name}`);
                });
            } else {
                console.log('No folders passed the filtering');
            }
        } catch (error) {
            console.error('Error filtering folders:', error);
            folders = [];
        }

        if (!folders || folders.length === 0) {
            foldersBody.html('<tr><td colspan="3" class="text-center">No folders found matching the current filters</td></tr>');
            return;
        }

        folders.forEach(folder => {
            try {
            // Remove tenant prefix, category, and timestamp from display name
            const displayName = folder.name
                .replace(/^\[[^\]]+\]\s*/, '') // Remove tenant prefix
                .replace(/\[(Regular|Irregular|Probation)\]\s*/, '') // Remove category
                .replace(/,\s*\d{1,2}\/\d{1,2}\/\d{4},\s*\d{1,2}:\d{2}:\d{2}\s*(?:AM|PM)/i, ''); // Remove timestamp
            
            const driveUrl = folder.webViewLink || `https://drive.google.com/drive/folders/${folder.id}`;
            
            let row = '<tr>';
            row += '<td class="align-middle">';
            row += '<span class="text-dark" data-folder-id="' + folder.id + '">';
            row += '<i class="fas fa-folder text-warning me-2"></i>' + escapeHtml(displayName);
            row += '</span>';
            row += '</td>';
            row += '<td class="align-middle text-center">';
            row += `<button class="btn btn-sm btn-outline-success me-2" onclick="showFolderContentsInModal('${folder.id}', '${escapeHtml(displayName)}')">`;
            row += '<i class="fas fa-upload"></i> Upload File';
            row += '</button>';
            row += `<a href="${driveUrl}" target="_blank" class="btn btn-sm btn-outline-info">`;
            row += '<i class="fas fa-folder-open"></i> Open Folder';
            row += '</a>';
            row += '</td>';
            row += '</tr>';
            
            foldersBody.append(row);
            } catch (error) {
                console.error('Error rendering folder row:', error, folder);
            }
        });
    }

    function getFileType(mimeType) {
        if (mimeType.includes('spreadsheet')) return 'Spreadsheet';
        if (mimeType.includes('document')) return 'Document';
        if (mimeType.includes('presentation')) return 'Presentation';
        if (mimeType.includes('pdf')) return 'PDF';
        if (mimeType.includes('image')) return 'Image';
        return 'File';
    }

    function getFileIcon(mimeType) {
        if (mimeType.includes('spreadsheet')) return 'fa-file-excel';
        if (mimeType.includes('document')) return 'fa-file-word';
        if (mimeType.includes('presentation')) return 'fa-file-powerpoint';
        if (mimeType.includes('pdf')) return 'fa-file-pdf';
        if (mimeType.includes('image')) return 'fa-file-image';
        return 'fa-file';
    }

    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function getTenantDomain() {
        const fullDomain = '{{ tenant("domains")->first()->domain }}';
        return fullDomain.replace('.localhost', '');
    }

    function formatFileSize(bytes) {
        if (!bytes) return '0 B';
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(1024));
        return `${(bytes / Math.pow(1024, i)).toFixed(2)} ${sizes[i]}`;
    }

    function displayFirstFolderFiles(files) {
        const filesTable = $('#firstFolderFilesTable tbody');
        filesTable.empty();

        const filesList = files.filter(file => file.mimeType !== 'application/vnd.google-apps.folder');

        if (filesList.length === 0) {
            filesTable.html('<tr><td colspan="5" class="text-center">No files found in this folder</td></tr>');
            return;
        }

        filesList.sort((a, b) => a.name.localeCompare(b.name));

        filesList.forEach(file => {
            const name = escapeHtml(file.name);
            const type = getFileType(file.mimeType);
            const size = file.size ? formatFileSize(file.size) : 'N/A';
            const modifiedDate = file.modifiedTime ? new Date(file.modifiedTime).toLocaleString() : 'N/A';
            const icon = getFileIcon(file.mimeType);

            const row = `
                <tr>
                    <td class="align-middle">
                        <i class="fas ${icon} text-primary me-2"></i>
                        <span>${name}</span>
                    </td>
                    <td class="align-middle">${type}</td>
                    <td class="align-middle">${size}</td>
                    <td class="align-middle">${modifiedDate}</td>
                    <td class="align-middle text-center">
                        <a href="${file.webViewLink}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-external-link-alt"></i> Open
                        </a>
                    </td>
                </tr>
            `;
            filesTable.append(row);
        });
    }

    function loadFirstFolderContents(folderId) {
        if (!folderId) return;

        $('#firstFolderFilesTable tbody').html('<tr><td colspan="5" class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');

        let url = "{{ route('tenant.admin.requirements.folder.contents', ['tenant' => tenant('id')]) }}/" + encodeURIComponent(folderId);
        
        $.ajax({
            url: url,
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('First folder contents response:', response);
                
                if (response.success) {
                    displayFirstFolderFiles(response.files);
                } else {
                    $('#firstFolderFilesTable tbody').html('<tr><td colspan="5" class="text-center text-danger">' + (response.message || 'Failed to load files') + '</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error loading first folder:', xhr.responseText);
                $('#firstFolderFilesTable tbody').html('<tr><td colspan="5" class="text-center text-danger">Failed to load files. Please try again.</td></tr>');
            }
        });
    }

    function loadFirstFolderFiles() {
    const table = document.getElementById('firstFolderFilesTable').getElementsByTagName('tbody')[0];
    table.innerHTML = `
        <tr>
            <td colspan="5" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </td>
        </tr>
    `;

    // Load files for the current folder
    const folderId = currentFolderId || 'root';
    fetch(`{{ route('tenant.admin.requirements.folder.contents', ['tenant' => tenant('id')]) }}/${folderId}`)
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                displayFirstFolderFiles(result.contents.filter(item => item.mimeType !== 'application/vnd.google-apps.folder'));
            } else {
                throw new Error(result.message || 'Failed to load files');
            }
        })
        .catch(error => {
            console.error('Load files error:', error);
            table.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-danger">
                        <i class="fas fa-exclamation-circle"></i> Failed to load files
                    </td>
                </tr>
            `;
        });
}

function displayFirstFolderFiles(files) {
    const table = document.getElementById('firstFolderFilesTable');
    if (!table) {
        console.warn('First folder files table not found');
        return;
    }
    
    const tbody = table.querySelector('tbody');
    if (!tbody) {
        console.warn('Table body not found in first folder files table');
        return;
    }

    if (!files || !Array.isArray(files) || files.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-muted">
                    <i class="fas fa-folder-open"></i> No files found
                </td>
            </tr>
        `;
        return;
    }

    table.innerHTML = files.map(file => `
        <tr>
            <td>
                <i class="fas fa-file"></i> <a href="${file.webViewLink}" target="_blank">${file.name}</a>
            </td>
            <td>${file.mimeType || 'Unknown'}</td>
            <td>${formatFileSize(file.size)}</td>
            <td>${new Date(file.lastModified).toLocaleString()}</td>
            <td class="text-center">
                <button class="btn btn-sm btn-secondary" disabled title="Delete functionality not available">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

function deleteFile(fileId) {
    Swal.fire({
        title: 'Delete File',
        text: 'Are you sure you want to delete this file?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#dc3545'
    }).then((result) => {
        if (result.isConfirmed) {
            const url = '#'; // Delete functionality not implemented
            Swal.fire({
                icon: 'info',
                title: 'Feature Not Available',
                text: 'The delete functionality has not been integrated yet.'
            });
            return;
            // Delete code below is commented out since it's not implemented yet
            /*fetch(url, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'File Deleted',
                        text: 'The file has been deleted successfully',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    loadFolderContents(currentFolderId);
                } else {
                    throw new Error(result.message || 'Failed to delete file');
                }
            })
            .catch(error => {
                console.error('Delete file error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Failed to delete file'
                });
            });*/
        }
    });
}

function setupModalFileUploadHandler() {
    const uploadForm = document.getElementById('uploadFileFormModal');
    const dragDropZone = document.getElementById('dragDropZone');
    const fileInput = document.getElementById('modalFileUpload');
    const filePreview = document.getElementById('filePreview');
    const selectedFileName = document.getElementById('selectedFileName');
    const uploadBtn = document.getElementById('modalUploadBtn');
    const removeFileBtn = document.getElementById('removeFile');
    
    // Function to update UI when file is selected
    function updateFileSelection() {
        if (fileInput.files && fileInput.files.length > 0) {
            const file = fileInput.files[0];
            selectedFileName.textContent = file.name;
            filePreview.classList.remove('d-none');
            uploadBtn.disabled = false;
            
            // Change icon based on file type
            const fileIcon = document.querySelector('#filePreview i.fas');
            if (fileIcon) {
                fileIcon.className = 'fas ' + getFileIcon(file.type) + ' me-2 text-primary';
            }
        } else {
            filePreview.classList.add('d-none');
            uploadBtn.disabled = true;
        }
    }
    
    // File input change handler
    if (fileInput) {
        fileInput.addEventListener('change', updateFileSelection);
    }
    
    // Remove file button handler
    if (removeFileBtn) {
        removeFileBtn.addEventListener('click', (e) => {
            e.preventDefault();
            fileInput.value = '';
            filePreview.classList.add('d-none');
            uploadBtn.disabled = true;
        });
    }
    
    if (dragDropZone) {
        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dragDropZone.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        // Highlight drop zone when dragging over it
        ['dragenter', 'dragover'].forEach(eventName => {
            dragDropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dragDropZone.addEventListener(eventName, unhighlight, false);
        });

        // Handle dropped files
        dragDropZone.addEventListener('drop', handleDrop, false);

        // Handle click to upload - prevent direct click on the drag zone to avoid duplicate file selection
        // We'll let the Browse Files button handle the file selection
        // dragDropZone.addEventListener('click', () => {
        //     fileInput.click();
        // });
    }
    
    if (uploadForm) {
        uploadForm.addEventListener('submit', handleUpload);
    }

    function preventDefaults (e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function highlight(e) {
        dragDropZone.classList.add('dragover');
    }

    function unhighlight(e) {
        dragDropZone.classList.remove('dragover');
    }

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        fileInput.files = files;
        
        // Update the UI to show selected file
        updateFileSelection();
    }

    async function handleUpload(e) {
        e.preventDefault();
        
        const fileInput = document.getElementById('modalFileUpload');
        const folderId = document.getElementById('modalCurrentFolderId').value;
        const uploadProgress = document.getElementById('uploadProgress');
        
        if (!fileInput.files || fileInput.files.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'No File Selected',
                text: 'Please select a file to upload'
            });
            return;
        }

        // Validate file size (e.g., 10MB limit)
        const maxSize = 10 * 1024 * 1024; // 10MB in bytes
        if (fileInput.files[0].size > maxSize) {
            Swal.fire({
                icon: 'error',
                title: 'File Too Large',
                text: 'Please select a file smaller than 10MB'
            });
            return;
        }

        const formData = new FormData();
        formData.append('file', fileInput.files[0]);
        formData.append('folderId', folderId);
        
        // Reset progress bar
        if (uploadProgress) {
            uploadProgress.style.width = '0%';
        }
        
        // Get CSRF token from meta tag
        const token = document.querySelector('meta[name="csrf-token"]');
        if (!token) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'CSRF token not found. Please refresh the page.'
            });
            return;
        }
        formData.append('_token', token.getAttribute('content'));

        try {
            const uploadBtn = document.getElementById('modalUploadBtn');
            uploadBtn.disabled = true;
            uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';

            // Use the Laravel route helper with the folderId parameter
            const uploadUrl = "{{ route('tenant.admin.requirements.folder.upload', ['tenant' => tenant('id'), 'folderId' => '__FOLDER_ID__']) }}".replace('__FOLDER_ID__', folderId);

            console.log('Uploading file to:', uploadUrl);

            // Create a new XMLHttpRequest to track upload progress
            const xhr = new XMLHttpRequest();
            
            // Track upload progress
            xhr.upload.onprogress = (e) => {
                if (e.lengthComputable && uploadProgress) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    uploadProgress.style.width = percentComplete + '%';
                }
            };
            
            // Setup promise to handle response
            const uploadPromise = new Promise((resolve, reject) => {
                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            resolve(response);
                        } catch (e) {
                            reject(new Error('Invalid JSON response'));
                        }
                    } else {
                        reject(new Error('HTTP Error: ' + xhr.status));
                    }
                };
                
                xhr.onerror = function() {
                    reject(new Error('Network Error'));
                };
            });
            
            // Open and send the request
            xhr.open('POST', uploadUrl, true);
            xhr.setRequestHeader('X-CSRF-TOKEN', token.getAttribute('content'));
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.send(formData);
            
            // Wait for response
            const result = await uploadPromise;

            if (result.success) {
                // Set progress to 100% to indicate completion
                if (uploadProgress) {
                    uploadProgress.style.width = '100%';
                }
                
                Swal.fire({
                    icon: 'success',
                    title: 'File Uploaded',
                    text: 'The file has been uploaded successfully',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });

                // Reload the folder contents
                loadFolderContents(folderId);
                fileInput.value = '';
                filePreview.classList.add('d-none');
            } else {
                throw new Error(result.message || 'Failed to upload file');
            }
        } catch (error) {
            console.error('File upload error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Upload Failed',
                text: error.message || 'Failed to upload file'
            });
        } finally {
            const uploadBtn = document.getElementById('modalUploadBtn');
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = '<i class="fas fa-upload"></i> Upload';
        }
    }
}
</script>
@endpush