@extends('tenant.layouts.app')

@section('title', 'My Requirements')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">My Requirements</h5>
                </div>
                <div class="card-body">
                    <!-- Breadcrumb navigation -->
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#" onclick="loadFolder('root')">Root</a></li>
                            <!-- Dynamic breadcrumbs will be added here -->
                        </ol>
                    </nav>

                    <!-- Files and folders table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Last Modified</th>
                                </tr>
                            </thead>
                            <tbody id="folderContents">
                                <!-- Contents will be loaded dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentFolderId = 'root';
    let breadcrumbPath = [];

    document.addEventListener('DOMContentLoaded', function() {
        loadFolder('root');
    });

    async function loadFolder(folderId) {
        try {
            const response = await fetch(`{{ route('tenant.requirements.folder.contents', ['tenant' => tenant('id')]) }}/${folderId}`);
            const result = await response.json();

            if (result.success) {
                currentFolderId = folderId;
                updateBreadcrumbs(result.path);
                displayFolderContents(result.contents);
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            Swal.fire('Error', error.message || 'Failed to load folder contents', 'error');
        }
    }

    function displayFolderContents(contents) {
        const tbody = document.getElementById('folderContents');
        tbody.innerHTML = '';

        contents.forEach(item => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    ${item.mimeType === 'application/vnd.google-apps.folder' 
                        ? `<i class="fas fa-folder text-warning"></i> <a href="#" onclick="loadFolder('${item.id}')">${item.name}</a>`
                        : `<i class="fas fa-file text-primary"></i> <a href="${item.webViewLink}" target="_blank">${item.name}</a>`
                    }
                </td>
                <td>${item.mimeType === 'application/vnd.google-apps.folder' ? 'Folder' : 'File'}</td>
                <td>${new Date(item.modifiedTime).toLocaleString()}</td>
            `;
            tbody.appendChild(row);
        });
    }

    function updateBreadcrumbs(path) {
        const ol = document.querySelector('.breadcrumb');
        ol.innerHTML = '<li class="breadcrumb-item"><a href="#" onclick="loadFolder(\'root\')">Root</a></li>';

        path.forEach((item, index) => {
            const li = document.createElement('li');
            li.className = 'breadcrumb-item';
            if (index === path.length - 1) {
                li.classList.add('active');
                li.textContent = item.name;
            } else {
                li.innerHTML = `<a href="#" onclick="loadFolder('${item.id}')">${item.name}</a>`;
            }
            ol.appendChild(li);
        });
    }
</script>
@endpush 