@extends('business::layouts.master')

@section('title')
    {{ __('Database Backup') }}
@endsection

@section('main_content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('Database Backup') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 6L9 17L4 12" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12" stroke="#2196F3" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <h3 class="mb-3">{{ __('Backup Your Database') }}</h3>
                            <p class="text-muted mb-4">
                                {{ __('Create a complete backup of your database. This will download a SQL file containing all your data.') }}
                            </p>
                            <button type="button" class="btn btn-primary btn-lg" id="backupBtn">
                                <i class="fas fa-download me-2"></i>
                                {{ __('Download Database Backup') }}
                            </button>
                        </div>
                        
                        <div class="alert alert-info mt-4" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>{{ __('Note:') }}</strong>
                            {{ __('The backup process may take a few moments depending on your database size. Please do not close this page until the download starts.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="backupConfirmModal" tabindex="-1" aria-labelledby="backupConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="backupConfirmModalLabel">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        {{ __('Confirm Database Backup') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('Are you sure you want to create a database backup?') }}</p>
                    <p class="text-muted mb-0">
                        <small>{{ __('This will create a complete backup of your database and download it to your computer.') }}</small>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" class="btn btn-primary" id="confirmBackupBtn">
                        <i class="fas fa-check me-2"></i>
                        {{ __('Yes, Download Backup') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-5">
                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">{{ __('Loading...') }}</span>
                    </div>
                    <h5>{{ __('Creating Database Backup...') }}</h5>
                    <p class="text-muted mb-0">{{ __('Please wait, this may take a few moments.') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        // Show confirmation modal when backup button is clicked
        $('#backupBtn').on('click', function() {
            $('#backupConfirmModal').modal('show');
        });

        // Handle backup confirmation
        $('#confirmBackupBtn').on('click', function() {
            // Hide confirmation modal
            $('#backupConfirmModal').modal('hide');
            
            // Show loading modal
            $('#loadingModal').modal('show');
            
            // Create a form to submit
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("business.backup.download") }}';
            
            // Add CSRF token
            var csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);
            
            document.body.appendChild(form);
            form.submit();
            
            // Hide loading modal after a delay (assuming download starts)
            setTimeout(function() {
                $('#loadingModal').modal('hide');
                
                // Show success message using toastr
                toastr.success('{{ __("Your database backup download should start shortly.") }}', '{{ __("Backup Started") }}');
            }, 3000);
        });
    });
</script>
@endpush

@push('css')
<style>
    .card {
        border: none;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        border-radius: 10px;
    }
    
    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #e9ecef;
        padding: 1.5rem;
    }
    
    .btn-lg {
        padding: 12px 30px;
        font-size: 16px;
        border-radius: 8px;
    }
    
    .modal-content {
        border-radius: 10px;
        border: none;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }
    
    .modal-header {
        border-bottom: 1px solid #e9ecef;
        padding: 1.5rem;
    }
    
    .modal-footer {
        border-top: 1px solid #e9ecef;
        padding: 1.5rem;
    }
</style>
@endpush
