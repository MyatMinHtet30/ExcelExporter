/**
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
            try {
                // Update progress bar
                const total = progress.total || (this.completedUploads.length + this.failedUploads.length + this.uploadQueue.length + this.activeUploads);
                const percentage = total > 0 ? Math.round((progress.completed / total) * 100) : 0;
                if (uploadProgressBar) {
                    uploadProgressBar.style.width = percentage + '%';
                }

                // Add photo to list and create hidden input immediately when each photo completes
                if (progress.item && progress.item.status === 'completed') {
                    addPhotoToList(progress.item);
                    
                    // Create hidden input immediately for this photo (don't wait for all uploads to complete)
                    const form = document.getElementById('home-form');
                    if (form && progress.item.tempPath) {
                        // Check if input already exists
                        const existingInput = form.querySelector('input[name="restored_photos[]"][value="' + progress.item.tempPath + '"]');
                        if (!existingInput) {
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'restored_photos[]';
                            hiddenInput.value = progress.item.tempPath;
                            hiddenInput.className = 'uploaded-photo-input restored-photo-input';
                            form.appendChild(hiddenInput);
                        }
                    }
                    
                    if (typeof updatePhotoCounter === 'function') {
                        updatePhotoCounter();
                    }
                }
            } catch (error) {
                console.error('Error in onProgress:', error);
            }
        },
        onComplete: function(result) {
            try {
                // Hide loading
                if (uploadLoading) {
                    uploadLoading.style.display = 'none';
                }

                // Add hidden inputs for completed uploads (only for any that might be missing)
                // Note: Hidden inputs are already created in onProgress for each photo, 
                // but we'll double-check here to ensure none are missing
                if (typeof addHiddenInputsForPhotos === 'function') {
                    addHiddenInputsForPhotos(result.completed);
                }
                
                // Verify all completed photos have hidden inputs
                const form = document.getElementById('home-form');
                if (form && result.completed) {
                    result.completed.forEach(function(item) {
                        if (item.tempPath) {
                            const input = form.querySelector('input[name="restored_photos[]"][value="' + item.tempPath + '"]');
                            if (!input) {
                                // Create it now
                                const hiddenInput = document.createElement('input');
                                hiddenInput.type = 'hidden';
                                hiddenInput.name = 'restored_photos[]';
                                hiddenInput.value = item.tempPath;
                                hiddenInput.className = 'uploaded-photo-input restored-photo-input';
                                form.appendChild(hiddenInput);
                            }
                        }
                    });
                }
                
                if (result.failed && result.failed.length > 0) {
                    alert(result.failed.length + ' photos failed to upload. Please try uploading them individually.');
                }
            } catch (error) {
                console.error('Error in onComplete:', error);
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

        // Check if this photo is already in the list (avoid duplicates)
        const existingItem = photoList.querySelector(`[data-temp-path="${item.tempPath}"]`);
        if (existingItem) {
            console.log('Photo already in list, skipping:', item.filename);
            return;
        }

        // Show the new photos section
        const newPhotosSection = document.getElementById('new-photos-section');
        if (newPhotosSection) {
            // Keep section hidden - UI modification to hide photo list
            // newPhotosSection.style.display = 'block';
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
            <div class="photo-size">${formatFileSize(item.size)}</div>
            <button type="button" class="photo-remove" onclick="removeUploadedPhoto(this)" title="Delete All Uploaded Photos">
                <i class="fas fa-times"></i>
            </button>
        `;
        photoList.appendChild(photoItem);
        
        console.log('Photo added to list:', item.filename, 'Total photos in list:', photoList.querySelectorAll('.photo-list-item').length);
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
        const existingCount = document.querySelectorAll('.existing-photo:not(.deleted)').length;
        const uploadedCount = document.querySelectorAll('.uploaded-photo:not(.deleted)').length;
        const restoredCount = document.querySelectorAll('.restored-photo:not(.deleted)').length;
        const totalCount = existingCount + uploadedCount + restoredCount;

        // Sync all counters: phone, iPad, desktop
        ['photo-count', 'photo-count-ipad', 'photo-count-desktop'].forEach(function(id) {
            const el = document.getElementById(id);
            if (el) el.textContent = totalCount;
        });
    }
    
    // Make updatePhotoCounter globally available
    window.updatePhotoCounter = updatePhotoCounter;

    function addHiddenInputsForPhotos(completedUploads) {
        const form = document.getElementById('home-form');
        if (!form) return;

        // Add new inputs (avoid duplicates)
        completedUploads.forEach(item => {
            // Check if input already exists
            const existingInput = form.querySelector(`input[name="restored_photos[]"][value="${item.tempPath}"]`);
            if (existingInput) {
                console.log('Hidden input already exists for:', item.tempPath);
                return; // Skip if already exists
            }
            
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'restored_photos[]';
            hiddenInput.value = item.tempPath;
            hiddenInput.className = 'uploaded-photo-input restored-photo-input';
            form.appendChild(hiddenInput);
            
            console.log('Added hidden input for:', item.tempPath);
        });
        
        // Update counter after adding hidden inputs
        updatePhotoCounter();
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
                if (window.photoUploaderIpad) {
                    window.photoUploaderIpad.clear();
                }
                if (window.photoUploaderDesktop) {
                    window.photoUploaderDesktop.clear();
                }
                
                // Update counter
                updatePhotoCounter();
                
                // Hide new photos section
                checkNewPhotosSection();

                // Clear server-side session so deleted photos don't come back after preview
                fetch('/homes/clear-session', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({ clear_session: true })
                }).catch(function() {}); // fire-and-forget

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
            // Keep section hidden - UI modification to hide photo list
            // newPhotosSection.style.display = hasPhotos ? 'block' : 'none';
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeChunkedUpload();        // phone uploader
    initializeChunkedUploadIpad();    // iPad uploader
    initializeChunkedUploadDesktop(); // desktop uploader
});

// iPad uploader — uses -ipad suffixed IDs, same logic as phone
function initializeChunkedUploadIpad() {
    const photoInput     = document.getElementById('photo-input-ipad');
    const photoUploadArea= document.getElementById('photo-upload-area-ipad');
    const photoList      = document.getElementById('photo-list-ipad');
    const uploadLoading  = document.getElementById('upload-loading-ipad');
    const uploadProgressBar = document.getElementById('upload-progress-bar-ipad');

    if (!photoInput || !photoUploadArea) return;

    window.photoUploaderIpad = new ChunkedPhotoUploader({
        uploadUrl: '/homes/photos/upload-chunk',
        maxConcurrent: 5,
        onProgress: function(progress) {
            try {
                const total = progress.total || 1;
                const pct   = Math.round((progress.completed / total) * 100);
                if (uploadProgressBar) uploadProgressBar.style.width = pct + '%';

                if (progress.item && progress.item.status === 'completed') {
                    // Add to hidden list (same as phone — list stays hidden)
                    if (photoList) {
                        const exists = photoList.querySelector(`[data-temp-path="${progress.item.tempPath}"]`);
                        if (!exists) {
                            const div = document.createElement('div');
                            div.className = 'photo-list-item uploaded-photo';
                            div.dataset.tempPath = progress.item.tempPath;
                            photoList.appendChild(div);
                        }
                    }
                    // Add hidden input to form
                    const form = document.getElementById('home-form');
                    if (form && progress.item.tempPath) {
                        const existing = form.querySelector(`input[name="restored_photos[]"][value="${progress.item.tempPath}"]`);
                        if (!existing) {
                            const inp = document.createElement('input');
                            inp.type = 'hidden';
                            inp.name = 'restored_photos[]';
                            inp.value = progress.item.tempPath;
                            inp.className = 'uploaded-photo-input restored-photo-input';
                            form.appendChild(inp);
                        }
                    }
                    // Sync counter
                    if (typeof window.updatePhotoCounter === 'function') window.updatePhotoCounter();
                }
            } catch(e) { console.error('iPad upload progress error:', e); }
        },
        onComplete: function() {
            if (uploadLoading) uploadLoading.style.display = 'none';
            if (uploadProgressBar) uploadProgressBar.style.width = '0%';
        },
        onError: function(msg) { console.error('iPad upload error:', msg); }
    });

    photoInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            if (uploadLoading) uploadLoading.style.display = 'block';
            if (uploadProgressBar) uploadProgressBar.style.width = '0%';
            window.photoUploaderIpad.addFiles(Array.from(e.target.files));
            e.target.value = '';
        }
    });

    ['dragover','dragleave','drop'].forEach(function(evt) {
        photoUploadArea.addEventListener(evt, function(e) {
            e.preventDefault();
            if (evt === 'dragover') photoUploadArea.classList.add('drag-over');
            if (evt === 'dragleave') photoUploadArea.classList.remove('drag-over');
            if (evt === 'drop') {
                photoUploadArea.classList.remove('drag-over');
                const files = Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/'));
                if (files.length) {
                    if (uploadLoading) uploadLoading.style.display = 'block';
                    window.photoUploaderIpad.addFiles(files);
                }
            }
        });
    });
}

// Desktop uploader — uses -desktop suffixed IDs, same logic as iPad
function initializeChunkedUploadDesktop() {
    const photoInput      = document.getElementById('photo-input-desktop');
    const photoUploadArea = document.getElementById('photo-upload-area-desktop');
    const photoList       = document.getElementById('photo-list-desktop');
    const uploadLoading   = document.getElementById('upload-loading-desktop');
    const uploadProgressBar = document.getElementById('upload-progress-bar-desktop');

    if (!photoInput || !photoUploadArea) return;

    window.photoUploaderDesktop = new ChunkedPhotoUploader({
        uploadUrl: '/homes/photos/upload-chunk',
        maxConcurrent: 5,
        onProgress: function(progress) {
            try {
                const total = progress.total || 1;
                const pct = Math.round((progress.completed / total) * 100);
                if (uploadProgressBar) uploadProgressBar.style.width = pct + '%';

                if (progress.item && progress.item.status === 'completed') {
                    if (photoList) {
                        const exists = photoList.querySelector('[data-temp-path="' + progress.item.tempPath + '"]');
                        if (!exists) {
                            const div = document.createElement('div');
                            div.className = 'photo-list-item uploaded-photo';
                            div.dataset.tempPath = progress.item.tempPath;
                            photoList.appendChild(div);
                        }
                    }
                    const form = document.getElementById('home-form');
                    if (form && progress.item.tempPath) {
                        const existing = form.querySelector('input[name="restored_photos[]"][value="' + progress.item.tempPath + '"]');
                        if (!existing) {
                            const inp = document.createElement('input');
                            inp.type = 'hidden';
                            inp.name = 'restored_photos[]';
                            inp.value = progress.item.tempPath;
                            inp.className = 'uploaded-photo-input restored-photo-input';
                            form.appendChild(inp);
                        }
                    }
                    if (typeof window.updatePhotoCounter === 'function') window.updatePhotoCounter();
                }
            } catch(e) { console.error('Desktop upload error:', e); }
        },
        onComplete: function() {
            if (uploadLoading) uploadLoading.style.display = 'none';
            if (uploadProgressBar) uploadProgressBar.style.width = '0%';
        },
        onError: function(msg) { console.error('Desktop upload error:', msg); }
    });

    photoInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            if (uploadLoading) uploadLoading.style.display = 'block';
            if (uploadProgressBar) uploadProgressBar.style.width = '0%';
            window.photoUploaderDesktop.addFiles(Array.from(e.target.files));
            e.target.value = '';
        }
    });

    ['dragover', 'dragleave', 'drop'].forEach(function(evt) {
        photoUploadArea.addEventListener(evt, function(e) {
            e.preventDefault();
            if (evt === 'dragover') photoUploadArea.classList.add('drag-over');
            if (evt === 'dragleave') photoUploadArea.classList.remove('drag-over');
            if (evt === 'drop') {
                photoUploadArea.classList.remove('drag-over');
                const files = Array.from(e.dataTransfer.files).filter(function(f) {
                    return f.type.startsWith('image/');
                });
                if (files.length) {
                    if (uploadLoading) uploadLoading.style.display = 'block';
                    window.photoUploaderDesktop.addFiles(files);
                }
            }
        });
    });
}
