git/**
 * Chunked Photo Upload Handler
 * Handles uploading multiple photos one by one to avoid POST size limits
 */

class ChunkedPhotoUploader {
    constructor(options = {}) {
        this.uploadUrl = options.uploadUrl || '/homes/photos/upload-chunk';
        this.maxConcurrent = options.maxConcurrent || 5; // Allow multiple uploads for faster processing
        this.onProgress = options.onProgress || (() => {});
        this.onComplete = options.onComplete || (() => {});
        this.onError = options.onError || (() => {});
        
        this.uploadQueue = [];
        this.activeUploads = 0;
        this.completedUploads = [];
        this.failedUploads = [];
    }

    /**
     * Add files to upload queue
     */
    async addFiles(files) {
        for (let file of files) {
            if (this.isValidImage(file)) {
                // Convert HEIC files to JPEG before uploading
                const processedFile = await this.processFile(file);
                this.uploadQueue.push({
                    file: processedFile,
                    originalFile: file,
                    id: Date.now() + Math.random(),
                    status: 'pending'
                });
            }
        }
        this.processQueue();
    }

    /**
     * Process file (convert HEIC to JPEG if needed)
     */
    async processFile(file) {
        const fileName = file.name.toLowerCase();
        const isHeic = fileName.endsWith('.heic') || fileName.endsWith('.heif');
        
        if (isHeic && window.heic2any) {
            try {
                console.log('Converting HEIC file:', file.name);
                const convertedBlob = await heic2any({
                    blob: file,
                    toType: "image/jpeg",
                    quality: 0.8
                });
                
                // Create a new File object from the converted blob
                const convertedFile = new File(
                    [convertedBlob], 
                    file.name.replace(/\.(heic|heif)$/i, '.jpg'),
                    { type: 'image/jpeg' }
                );
                
                console.log('HEIC conversion successful:', convertedFile.name);
                return convertedFile;
            } catch (error) {
                console.warn('HEIC conversion failed, uploading original:', error);
                return file;
            }
        }
        
        return file;
    }

    /**
     * Validate image file
     */
    isValidImage(file) {
        // Expanded list of valid image types including modern formats
        const validTypes = [
            // Standard web formats
            'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
            // Modern formats
            'image/heic', 'image/heif', 'image/avif',
            // Traditional formats
            'image/bmp', 'image/tiff', 'image/tif', 'image/ico',
            // RAW formats (some browsers may support)
            'image/x-canon-cr2', 'image/x-canon-crw', 'image/x-nikon-nef', 
            'image/x-sony-arw', 'image/x-adobe-dng', 'image/x-panasonic-raw',
            // Additional formats
            'image/x-ms-bmp', 'image/x-icon'
        ];
        
        // Also check by file extension for formats that might not have proper MIME types
        const validExtensions = [
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'heic', 'heif', 'avif',
            'bmp', 'tiff', 'tif', 'ico', 'cr2', 'crw', 'nef', 'arw', 'dng',
            'raw', 'orf', 'rw2', 'pef', 'sr2', 'raf'
        ];
        
        const maxSize = 50 * 1024 * 1024; // 50MB

        // Check MIME type
        const mimeValid = validTypes.includes(file.type.toLowerCase());
        
        // Check file extension
        const fileName = file.name.toLowerCase();
        const fileExtension = fileName.split('.').pop();
        const extensionValid = validExtensions.includes(fileExtension);

        if (!mimeValid && !extensionValid) {
            this.onError(`Invalid file type: ${file.name}. Supported formats: JPG, PNG, GIF, WebP, HEIC, AVIF, BMP, TIFF, SVG, and RAW formats.`);
            return false;
        }

        if (file.size > maxSize) {
            this.onError(`File too large: ${file.name}. Maximum 50MB allowed.`);
            return false;
        }

        return true;
    }

    /**
     * Process upload queue
     */
    async processQueue() {
        while (this.uploadQueue.length > 0 && this.activeUploads < this.maxConcurrent) {
            const item = this.uploadQueue.shift();
            this.uploadFile(item);
        }
    }

    /**
     * Upload single file
     */
    async uploadFile(item) {
        this.activeUploads++;
        item.status = 'uploading';

        const formData = new FormData();
        formData.append('photo', item.file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');

        try {
            const response = await fetch(this.uploadUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                item.status = 'completed';
                item.tempPath = result.temp_path;
                item.filename = result.filename;
                item.size = result.size;
                this.completedUploads.push(item);
                
                this.onProgress({
                    completed: this.completedUploads.length,
                    total: this.completedUploads.length + this.failedUploads.length + this.uploadQueue.length + this.activeUploads - 1,
                    item: item
                });
            } else {
                throw new Error(result.message || 'Upload failed');
            }
        } catch (error) {
            item.status = 'failed';
            item.error = error.message;
            this.failedUploads.push(item);
            this.onError(`Failed to upload ${item.file.name}: ${error.message}`);
        }

        this.activeUploads--;

        // Continue processing queue
        if (this.uploadQueue.length > 0) {
            this.processQueue();
        } else if (this.activeUploads === 0) {
            // All uploads completed
            this.onComplete({
                completed: this.completedUploads,
                failed: this.failedUploads
            });
        }
    }

    /**
     * Get completed upload paths for form submission
     */
    getCompletedPaths() {
        return this.completedUploads.map(item => item.tempPath);
    }

    /**
     * Clear all uploads
     */
    clear() {
        this.uploadQueue = [];
        this.completedUploads = [];
        this.failedUploads = [];
        this.activeUploads = 0;
    }
}

// Global uploader instance
window.photoUploader = null;

/**
 * Initialize chunked photo upload for home forms
 */
function initializeChunkedUpload() {
    const photoInput = document.getElementById('photo-input');
    const photoUploadArea = document.getElementById('photo-upload-area');
    const photoList = document.getElementById('photo-list');
    const photoCounter = document.getElementById('photo-count');
    const uploadLoading = document.getElementById('upload-loading');
    const uploadProgressBar = document.getElementById('upload-progress-bar');

    if (!photoInput || !photoUploadArea) return;

    // Initialize uploader with higher concurrency for faster uploads
    window.photoUploader = new ChunkedPhotoUploader({
        uploadUrl: '/homes/photos/upload-chunk',
        maxConcurrent: 5, // Upload 5 photos simultaneously for faster processing
        onProgress: function(progress) {
            // Update progress bar
            const percentage = Math.round((progress.completed / progress.total) * 100);
            if (uploadProgressBar) {
                uploadProgressBar.style.width = percentage + '%';
            }

            // Add photo to list
            if (progress.item && progress.item.status === 'completed') {
                addPhotoToList(progress.item);
                updatePhotoCounter();
            }
        },
        onComplete: function(result) {
            // Hide loading
            if (uploadLoading) {
                uploadLoading.style.display = 'none';
            }

            // Add hidden inputs for completed uploads
            addHiddenInputsForPhotos(result.completed);

            console.log(`Upload complete: ${result.completed.length} successful, ${result.failed.length} failed`);
            
            if (result.failed.length > 0) {
                alert(`${result.failed.length} photos failed to upload. Please try uploading them individually.`);
            }
        },
        onError: function(message) {
            console.error('Upload error:', message);
            // Show user-friendly error message
            if (message.includes('too large') || message.includes('POST') || message.includes('8388608')) {
                alert('File too large for server. Please try uploading photos individually or use smaller photos.');
            }
        }
    });

    // Handle file input change
    photoInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            handlePhotoUpload(Array.from(e.target.files));
            // Clear the input so the same files can be selected again if needed
            e.target.value = '';
        }
    });

    // Handle drag and drop
    photoUploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        photoUploadArea.classList.add('drag-over');
    });

    photoUploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        photoUploadArea.classList.remove('drag-over');
    });

    photoUploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        photoUploadArea.classList.remove('drag-over');
        
        const files = Array.from(e.dataTransfer.files).filter(file => {
            // Accept any file that looks like an image by extension or MIME type
            const fileName = file.name.toLowerCase();
            const fileExtension = fileName.split('.').pop();
            const imageExtensions = [
                'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'heic', 'heif', 'avif',
                'bmp', 'tiff', 'tif', 'ico', 'cr2', 'crw', 'nef', 'arw', 'dng',
                'raw', 'orf', 'rw2', 'pef', 'sr2', 'raf'
            ];
            
            return file.type.startsWith('image/') || imageExtensions.includes(fileExtension);
        });
        if (files.length > 0) {
            handlePhotoUpload(files);
        }
    });

    function handlePhotoUpload(files) {
        if (files.length === 0) return;

        // Show loading
        if (uploadLoading) {
            uploadLoading.style.display = 'block';
        }

        // Reset progress bar
        if (uploadProgressBar) {
            uploadProgressBar.style.width = '0%';
        }

        // Show conversion message for HEIC files
        const heicFiles = files.filter(file => {
            const name = file.name.toLowerCase();
            return name.endsWith('.heic') || name.endsWith('.heif');
        });
        
        if (heicFiles.length > 0) {
            console.log(`Converting ${heicFiles.length} HEIC file(s) to JPEG for better compatibility...`);
        }

        // Start chunked upload
        window.photoUploader.addFiles(files);
    }

    function addPhotoToList(item) {
        if (!photoList) return;

        // Show the new photos section and photo list
        const newPhotosSection = document.getElementById('new-photos-section');
        if (newPhotosSection) {
            newPhotosSection.style.display = 'block';
        }
        if (photoList) {
            photoList.style.display = 'block';
        }

        // Determine format type for better display
        const fileName = item.filename.toLowerCase();
        const extension = fileName.split('.').pop();
        const formatInfo = getFormatInfo(extension);

        const photoItem = document.createElement('div');
        photoItem.className = 'photo-list-item uploaded-photo';
        photoItem.dataset.tempPath = item.tempPath;
        photoItem.innerHTML = `
            <div class="photo-name" title="${item.filename}">
                <i class="fas fa-image me-1 ${formatInfo.iconClass}"></i>
                ${item.filename}
                ${formatInfo.badge ? `<span class="format-badge ${formatInfo.badgeClass}">${formatInfo.badge}</span>` : ''}
            </div>
            <button type="button" class="btn btn-sm btn-danger delete-photo-btn" data-temp-path="${item.tempPath}" title="Delete photo" style="padding: 4px 8px; font-size: 0.75rem;">
                <i class="fas fa-trash"></i>
            </button>
        `;
        
        // Add delete button event listener
        const deleteBtn = photoItem.querySelector('.delete-photo-btn');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                deleteIndividualPhoto(item.tempPath, photoItem);
            });
        }
        
        photoList.appendChild(photoItem);
    }

    function getFormatInfo(extension) {
        const formatMap = {
            // Standard formats
            'jpg': { iconClass: 'text-success', badge: null, badgeClass: '' },
            'jpeg': { iconClass: 'text-success', badge: null, badgeClass: '' },
            'png': { iconClass: 'text-success', badge: null, badgeClass: '' },
            'gif': { iconClass: 'text-success', badge: 'GIF', badgeClass: 'badge-gif' },
            
            // Modern formats
            'webp': { iconClass: 'text-info', badge: 'WebP', badgeClass: 'badge-modern' },
            'avif': { iconClass: 'text-info', badge: 'AVIF', badgeClass: 'badge-modern' },
            'heic': { iconClass: 'text-warning', badge: 'HEIC', badgeClass: 'badge-apple' },
            'heif': { iconClass: 'text-warning', badge: 'HEIF', badgeClass: 'badge-apple' },
            
            // Professional formats
            'tiff': { iconClass: 'text-primary', badge: 'TIFF', badgeClass: 'badge-pro' },
            'tif': { iconClass: 'text-primary', badge: 'TIFF', badgeClass: 'badge-pro' },
            'bmp': { iconClass: 'text-secondary', badge: 'BMP', badgeClass: 'badge-legacy' },
            
            // RAW formats
            'cr2': { iconClass: 'text-danger', badge: 'RAW', badgeClass: 'badge-raw' },
            'nef': { iconClass: 'text-danger', badge: 'RAW', badgeClass: 'badge-raw' },
            'arw': { iconClass: 'text-danger', badge: 'RAW', badgeClass: 'badge-raw' },
            'dng': { iconClass: 'text-danger', badge: 'RAW', badgeClass: 'badge-raw' },
            'raw': { iconClass: 'text-danger', badge: 'RAW', badgeClass: 'badge-raw' },
            'orf': { iconClass: 'text-danger', badge: 'RAW', badgeClass: 'badge-raw' },
            'rw2': { iconClass: 'text-danger', badge: 'RAW', badgeClass: 'badge-raw' },
            'pef': { iconClass: 'text-danger', badge: 'RAW', badgeClass: 'badge-raw' },
            'sr2': { iconClass: 'text-danger', badge: 'RAW', badgeClass: 'badge-raw' },
            'raf': { iconClass: 'text-danger', badge: 'RAW', badgeClass: 'badge-raw' },
            
            // Vector
            'svg': { iconClass: 'text-purple', badge: 'SVG', badgeClass: 'badge-vector' }
        };

        return formatMap[extension] || { iconClass: 'text-muted', badge: extension.toUpperCase(), badgeClass: 'badge-unknown' };
    }

    function updatePhotoCounter() {
        if (!photoCounter) return;
        
        const existingCount = document.querySelectorAll('.existing-photo').length;
        const uploadedCount = document.querySelectorAll('.uploaded-photo').length;
        const restoredCount = document.querySelectorAll('.restored-photo').length;
        
        photoCounter.textContent = existingCount + uploadedCount + restoredCount;
    }

    function addHiddenInputsForPhotos(completedUploads) {
        const form = document.getElementById('home-form');
        if (!form) return;

        // Remove old uploaded photo inputs
        const oldInputs = form.querySelectorAll('input[name="restored_photos[]"].uploaded-photo-input');
        oldInputs.forEach(input => input.remove());

        // Add new inputs
        completedUploads.forEach(item => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'restored_photos[]';
            hiddenInput.value = item.tempPath;
            hiddenInput.className = 'uploaded-photo-input restored-photo-input';
            form.appendChild(hiddenInput);
        });
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Global function to remove uploaded photos
    // Global function to remove uploaded photos - now removes ALL uploaded photos
    window.removeUploadedPhoto = function(button) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'Delete all uploaded photos? This cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete all!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const allUploadedPhotos = document.querySelectorAll('.photo-list-item.uploaded-photo, .photo-list-item.restored-photo');
                
                allUploadedPhotos.forEach(photoItem => {
                    // Handle both uploaded photos (tempPath) and restored photos (photoPath)
                    const tempPath = photoItem.dataset.tempPath || photoItem.dataset.photoPath;
                    
                    // Remove from display
                    photoItem.remove();
                    
                    // Remove corresponding hidden input
                    const hiddenInput = document.querySelector(`input[name="restored_photos[]"][value="${tempPath}"]`);
                    if (hiddenInput) {
                        hiddenInput.remove();
                    }
                });
                
                // Update counter
                updatePhotoCounter();
                
                // Hide new photos section if no photos left
                checkNewPhotosSection();

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: 'All uploaded photos have been deleted.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    };
    
    // Function to delete individual photo
    function deleteIndividualPhoto(tempPath, photoItem) {
        // Remove from display
        if (photoItem) {
            photoItem.remove();
        }
        
        // Remove corresponding hidden input
        const hiddenInput = document.querySelector(`input[name="restored_photos[]"][value="${tempPath}"]`);
        if (hiddenInput) {
            hiddenInput.remove();
        }
        
        // Update counter
        updatePhotoCounter();
        
        // Hide new photos section if no photos left
        checkNewPhotosSection();
    }

    // Global function to delete all new photos - works like individual delete but for all
    window.deleteAllNewPhotosAndClearSession = function() {
        Swal.fire({
            title: 'Are you sure?',
            text: 'Delete all photos? This cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete all!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Get all uploaded and restored photos
                const allPhotos = document.querySelectorAll('.photo-list-item.uploaded-photo, .photo-list-item.restored-photo');
                
                // Remove each photo one by one (like individual delete)
                allPhotos.forEach(photoItem => {
                    // Handle both uploaded photos (tempPath) and restored photos (photoPath)
                    const tempPath = photoItem.dataset.tempPath || photoItem.dataset.photoPath;
                    
                    // Remove from display
                    photoItem.remove();
                    
                    // Remove corresponding hidden input
                    const hiddenInput = document.querySelector(`input[name="restored_photos[]"][value="${tempPath}"]`);
                    if (hiddenInput) {
                        hiddenInput.remove();
                    }
                });
                
                // Clear the uploader queue
                if (window.photoUploader) {
                    window.photoUploader.clear();
                }
                
                // Update counter
                updatePhotoCounter();
                
                // Hide new photos section
                checkNewPhotosSection();

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: 'All photos have been deleted.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    };

    // Check if new photos section should be visible
    function checkNewPhotosSection() {
        const newPhotosSection = document.getElementById('new-photos-section');
        const photoList = document.getElementById('photo-list');
        
        if (newPhotosSection && photoList) {
            const hasPhotos = photoList.querySelectorAll('.photo-list-item').length > 0;
            newPhotosSection.style.display = hasPhotos ? 'block' : 'none';
            photoList.style.display = hasPhotos ? 'block' : 'none';
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', initializeChunkedUpload);