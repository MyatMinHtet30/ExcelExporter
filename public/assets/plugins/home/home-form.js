/* -------------------------------------------------------
 * Home Form – unified logic for Create + Edit pages
 * - Row add/remove (with soft-delete on Edit)
 * - Reindexing details[i][...]
 * - Totals + summary
 * - Preview-on-Edit (POST without _method=PUT)
 * - Speech-to-text (Thai)
 * - Number formatting
 * -----------------------------------------------------*/

(function () {
  // ====== Shortcuts ======
  const $  = (s, r=document) => r.querySelector(s);
  const $$ = (s, r=document) => Array.from(r.querySelectorAll(s));
  const to2 = n => (isFinite(n) ? Number(n) : 0).toFixed(2);

  const rowsContainer = $('#rows-container');
  if (!rowsContainer) return;

  const homeForm = document.getElementById('home-form');
  if (homeForm && typeof window.validateHomeForm === 'function') {
    homeForm.addEventListener('submit', function (e) {
      const ok = window.validateHomeForm(homeForm);
      if (!ok) {
        e.preventDefault();
        homeForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  }

  const addBtns = [
    $('#add-row-btn'),       // Create page
    $('#add-row-btn-edit'),  // Edit page
  ].filter(Boolean);

  const rowTemplate = $('#row-template'); // Present on Edit page
  const previewBtn  = $('#preview-btn');  // Present on Edit page

  // ====== Speech-to-text (Thai) ======
  function startDictation(btn){
    const input = btn.closest('.input-group')?.querySelector('input');
    if(!input) return;
    if(!('webkitSpeechRecognition' in window)){
      alert('เบราว์เซอร์นี้ไม่รองรับการจดจำเสียง (Speech Recognition).');
      return;
    }
    const r = new webkitSpeechRecognition();
    r.lang = 'th-TH'; r.interimResults = false; r.maxAlternatives = 1;
    r.onresult = e => {
      input.value = e.results[0][0].transcript;
      input.dispatchEvent(new Event('input',{bubbles:true}));
    };
    r.start();
  }
  // expose for inline buttons
  window.startDictation = startDictation;

  // ====== Helpers about soft-delete (Edit page) ======
  const isSoftRow  = (row) => !!row.querySelector('.js-delete-flag');
  const isDeleted  = (row) => {
    const f = row.querySelector('.js-delete-flag');
    return f ? f.value === '1' : false;
  };
  const setDeleted = (row, val) => {
    const f = row.querySelector('.js-delete-flag');
    if (!f) return;
    f.value = val ? '1' : '0';
    row.classList.toggle('row-deleted', !!val);
  };

  const getRows = () => $$('.item-row', rowsContainer);

  // ====== Per-row totals ======
  function calcRow(row){
    const val = sel => parseFloat($(sel,row)?.value) || 0;

    const amount = val('input[name*="[amount]"]');
    const mc     = val('input[name*="[mc_price]"]');
    const lc     = val('input[name*="[lc_price]"]');

    const mTotal = amount * mc;
    const lTotal = amount * lc;
    const gTotal = mTotal + lTotal;

    const set = (cls, v) => { const el = $(cls, row); if(el) el.value = to2(v); };
    set('.js-mat-total',   mTotal);
    set('.js-lab-total',   lTotal);
    set('.js-grand-total', gTotal);
  }

function recalcSummary(){
    // Sum only ACTIVE (non-deleted) rows' grand totals
    const sum = getRows()
   .map(r => $('.js-grand-total', r))
   .reduce((a,el) => a + (parseFloat(el?.value) || 0), 0);

    const operating = sum * 0.15;
    const ab        = sum + operating;
    const vat       = ab * 0.07;
    const finalT    = ab + vat;

    // Update DESKTOP displays
    const setTxt=id=>val=>{ const el=$('#'+id); if(el) el.textContent = to2(val); };
    setTxt('miscDisplay')(sum);
    setTxt('operatingDisplay')(operating);
    setTxt('abDisplay')(ab);
    setTxt('vatDisplay')(vat);
    setTxt('finalDisplay')(finalT);

    // Update MOBILE displays
    setTxt('miscDisplayMobile')(sum);
    setTxt('operatingDisplayMobile')(operating);
    setTxt('abDisplayMobile')(ab);
    setTxt('vatDisplayMobile')(vat);
    setTxt('finalDisplayMobile')(finalT);

    const setVal=id=>val=>{ const el=$('#'+id); if(el) el.value      = to2(val); };
    setVal('misc_total')(sum);
    setVal('operating_expenses')(operating);
    setVal('category_ab_total')(ab);
    setVal('vat_total')(vat);
    setVal('final_total')(finalT);
}
  function recalcAll(){
    getRows().forEach(calcRow);
    recalcSummary();
  }
  // Keep old name for compatibility if you used window.recomputeSummary elsewhere
  window.recomputeSummary = recalcSummary;

  // ====== Reindex + serials ======
  function reindexRows(){
    const rows = getRows();

    // Update name indices
    rows.forEach((row, idx) => {
      $$('input, select, textarea', row).forEach(el => {
        if (!el.name) return;
        el.name = el.name.replace(/details\[\d+\]/, `details[${idx}]`);
      });
    });

    // Serial numbers for ACTIVE rows only
    let serialCounter = 1;
    rows.forEach(row => {
      const serial = $('.serial', row);
      if (!serial) return;
      if (!isDeleted(row)) {
        serial.value = serialCounter++;
      } else {
        // keep its shown number or blank—choose your UX
        // serial.value = '';
      }
    });

    updateRemoveButtons();
    recalcAll();
  }

  // ====== Hide delete when only one ACTIVE row ======
  function updateRemoveButtons() {
    const rows = getRows();
    const onlyOne = rows.length === 1;
    rows.forEach(row => {
        const btn = row.querySelector('.remove-row');
        if (!btn) return;
        btn.classList.toggle('d-none', onlyOne);
        btn.disabled = onlyOne;
    });
    }

  // ====== Add row ======
  function addRow(){
    let newRow;

    if (rowTemplate) {
      // Edit page: create from template
      const nextIndex = getRows().length;
      const ser = nextIndex + 1;
      const html = rowTemplate.innerHTML
        .replaceAll('__INDEX__', nextIndex)
        .replaceAll('__SER__', ser);
      const wrap = document.createElement('div');
      wrap.innerHTML = html.trim();
      newRow = wrap.firstElementChild;
    } else {
      // Create page: clone first row structure
      const first = $('.item-row', rowsContainer);
      if (!first) return;
      newRow = first.cloneNode(true);

      // Clear inputs except serial & readonly totals (reset totals to 0.00)
      $$('input', newRow).forEach(inp => {
        if (inp.classList.contains('serial')) return;
        if (inp.classList.contains('js-mat-total') ||
            inp.classList.contains('js-lab-total') ||
            inp.classList.contains('js-grand-total')) {
          inp.value = '0.00';
        } else if (inp.type === 'hidden' && inp.classList.contains('js-delete-flag')) {
          inp.value = '0';
        } else {
          inp.value = '';
        }
      });
      // Make sure it's not visually deleted
      newRow.classList.remove('row-deleted');
    }

    rowsContainer.appendChild(newRow);
    reindexRows();
    // Scroll to the new row
    setTimeout(() => {
      newRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }, 100);
  }
  window.addRow = addRow; // optional global

  // ====== Remove / Soft-delete (delegated) ======
    // put this near the top with other refs
const deletedBin = document.getElementById('deleted-bin');

// ====== Remove / Delete (delegated) ======
document.addEventListener('click', function (e) {
  const btn = e.target.closest('.remove-row');
  if (!btn) return;
  const row = btn.closest('.item-row');
  if (!row || !rowsContainer.contains(row)) return;

  // keep at least one visible row
  const visibleCount = getRows().length;
  if (visibleCount <= 1) {
    alert('ต้องมีอย่างน้อย 1 แถว'); // At least one row required
    return;
  }

  // existing DB row?
  const idInput = row.querySelector('input[name*="[id]"]');
  const existingId = idInput && idInput.value;

  if (existingId && deletedBin) {
    // add a hidden field to request
    const h = document.createElement('input');
    h.type  = 'hidden';
    h.name  = 'deleted_detail_ids[]';
    h.value = existingId;
    deletedBin.appendChild(h);
  }

  // remove visually
  row.remove();

  // reindex and recompute
  reindexRows();
});

  // ====== Totals recalc on inputs ======
    // ====== Totals recalc on inputs + no-negative enforcement ======
  rowsContainer.addEventListener('input', function (e) {
    const target = e.target;

    // 1) Enforce no negative numbers
    if (target.matches('input.no-negative')) {
      let val = target.value || '';

      // Strip any "-" that got in (paste, etc.)
      if (val.includes('-')) {
        val = val.replace(/-/g, '');
        target.value = val;
        target.classList.add('is-invalid');
      } else if (val !== '' && parseFloat(val) < 0) {
        target.value = Math.abs(parseFloat(val)).toString();
        target.classList.add('is-invalid');
      } else {
        // OK value => clear error
        target.classList.remove('is-invalid');
      }
    }

    // 2) Existing totals logic
    if (target.matches('input[name*="[amount]"], input[name*="[mc_price]"], input[name*="[lc_price]"]')) {
      const row = target.closest('.item-row');
      if (row) {
        calcRow(row);
        recalcSummary();
      }
    }
  });

  // ====== Observe DOM changes (rows injected elsewhere) ======
  const mo = new MutationObserver(() => {
    reindexRows();
  });
  mo.observe(rowsContainer, { childList: true });

    // ====== Block negative values on .no-negative inputs ======
  // ====== Block negative values on .no-negative inputs ======
document.addEventListener('keydown', function (e) {
  const target = e.target;
  if (!target.matches('input.no-negative')) return;

  // Block "-", "+", and "e" (scientific notation)
  if (e.key === '-' || e.key === '+' || e.key.toLowerCase() === 'e') {
    e.preventDefault();
    target.classList.add('is-invalid');

    // 🔹 also add has-error to this row so spacing appears immediately
    const row = target.closest('.item-row');
    if (row) {
      row.classList.add('has-error');
    }
  }
});

  // ====== Number formatting (.number-input) ======
  function formatNumberInput(input) {
    let value = input.value.replace(/,/g, '');
    if (value === '' || isNaN(value)) { input.value = ''; return; }
    let parts = value.split('.');
    let intPart = parts[0];
    let decPart = parts[1] ? parts[1].slice(0, 3) : '';
    intPart = parseInt(intPart, 10).toLocaleString('en-US');
    input.value = decPart.length > 0 ? (intPart + '.' + decPart) : intPart;
  }
  document.addEventListener('input', function(e){
    if(e.target.classList.contains('number-input')){
      formatNumberInput(e.target);
    }
  });
  document.addEventListener('blur', function(e){
    if(e.target.classList.contains('number-input')){
      let value = e.target.value.replace(/,/g, '');
      if (value && !isNaN(value)) {
        e.target.value = parseFloat(value).toLocaleString('en-US', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        });
      }
    }
  }, true);

  // ====== Preview button (Edit page) – submit as POST to preview ======
  if (previewBtn) {
    previewBtn.addEventListener('click', function (e) {
      e.preventDefault();

      const form = this.form || document.getElementById('home-form');
      if (!form) return;

      if (typeof window.validateHomeForm === 'function') {
      const ok = window.validateHomeForm(form);
      if (!ok) {
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        return; // ⬅️ stop here, do NOT preview
      }
    }

      // Ensure all restored photos are included in the form
      const restoredPhotoInputs = form.querySelectorAll('input[name="restored_photos[]"]');
      restoredPhotoInputs.forEach(input => {
        if (!input.value) {
          input.remove(); // Remove empty inputs
        }
      });

      const spoof = form.querySelector('input[name="_method"]');
      let spoofWasDisabled = false;
      if (spoof) { spoof.disabled = true; spoofWasDisabled = true; }

      const originalAction = form.getAttribute('action');
      const originalMethod = form.getAttribute('method');

      form.setAttribute('action', form.dataset.previewAction || form.getAttribute('data-preview-action') || form.dataset.preview || (typeof HOME_PREVIEW_URL !== 'undefined' ? HOME_PREVIEW_URL : ''));
      if (!form.getAttribute('action')) {
        // fallback for Blade: set data attr in template (see instructions)
        console.warn('Preview action missing: set data-preview-action on the form.');
      }
      form.setAttribute('method', 'POST');
      form.submit();

      // Restore
      form.setAttribute('action', originalAction);
      form.setAttribute('method', originalMethod || 'POST');
      if (spoof && spoofWasDisabled) spoof.disabled = false;
    });
  }

  document.addEventListener('input', function (e) {
    if (!e.target.closest('.item-row')) return;

    const row = e.target.closest('.item-row');
    const hasError = row.querySelector('.form-control.is-invalid');

    if (hasError) {
        row.classList.add('has-error');
    } else {
        row.classList.remove('has-error');
    }
});

  // ====== Add buttons ======
  // ====== Add buttons ======
// Handle desktop button
const desktopAddBtn = document.getElementById('add-row-btn');
if (desktopAddBtn) {
    desktopAddBtn.addEventListener('click', addRow);
}

// Handle mobile button
const mobileAddBtn = document.getElementById('add-row-btn-mobile');
if (mobileAddBtn) {
    mobileAddBtn.addEventListener('click', addRow);
}

// Legacy support for edit page button
const editAddBtn = document.getElementById('add-row-btn-edit');
if (editAddBtn) {
    editAddBtn.addEventListener('click', addRow);
}


// If you still want to use forEach
addBtns.forEach(btn => {
    if (btn) btn.addEventListener('click', addRow);
});

  // ====== Initial ======
  // compute any prefilled rows (Edit), lock delete if only one active, update summary
  reindexRows();
})();

// Mobile generate button triggers desktop button
const mobileGenerateBtn = document.getElementById('generate-btn-mobile');
const desktopGenerateBtn = document.getElementById('generate-btn');
const mobilePreviewBtn = document.getElementById('preview-btn-mobile');
const desktopPreviewBtn = document.getElementById('preview-btn');

if (mobileGenerateBtn && desktopGenerateBtn) {
    mobileGenerateBtn.addEventListener('click', function(e) {
        e.preventDefault();
        desktopGenerateBtn.click();
    });
}

if (mobilePreviewBtn && desktopPreviewBtn) {
    mobilePreviewBtn.addEventListener('click', function(e) {
        e.preventDefault();
        desktopPreviewBtn.click();
    });
}

// Photo Upload Functionality for Mobile
document.addEventListener('DOMContentLoaded', function() {
    const photoInput = document.getElementById('photo-input');
    const uploadArea = document.getElementById('photo-upload-area');
    const photoList = document.getElementById('photo-list');
    const photoCountElement = document.getElementById('photo-count');
    const uploadLoading = document.getElementById('upload-loading');
    const progressBar = document.getElementById('upload-progress-bar');
    let uploadedPhotos = [];
    
    // Only initialize for mobile
    if (window.innerWidth <= 768) {
        // Drag and drop functionality
        if (uploadArea) {
            uploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            });
            
            uploadArea.addEventListener('dragleave', () => {
                uploadArea.classList.remove('dragover');
            });
            
            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
                const files = e.dataTransfer.files;
                handlePhotos(files);
            });
        }
        
        // File input change
        if (photoInput) {
            photoInput.addEventListener('change', (e) => {
                handlePhotos(e.target.files);
            });
        }
        
        function handlePhotos(files) {
            const validFiles = Array.from(files).filter(file => {
                const isValidType = file.type.startsWith('image/');
                const isValidSize = file.size <= 5 * 1024 * 1024; // 5MB
                return isValidType && isValidSize;
            });
            
            if (validFiles.length === 0) {
                alert('Please select valid image files (max 5MB each)');
                return;
            }
            
            // Show loading
            if (uploadLoading) {
                uploadLoading.classList.add('active');
                progressBar.style.width = '0%';
            }
            
            // Simulate upload progress
            let loaded = 0;
            const total = validFiles.length;
            const progressInterval = setInterval(() => {
                loaded++;
                if (progressBar) progressBar.style.width = `${(loaded / total) * 100}%`;
                
                if (loaded >= total) {
                    clearInterval(progressInterval);
                    setTimeout(() => {
                        if (uploadLoading) uploadLoading.classList.remove('active');
                        processPhotos(validFiles);
                    }, 500);
                }
            }, 200);
        }
        
        function processPhotos(files) {
            files.forEach((file, index) => {
                const photoData = {
                    id: 'photo-' + Date.now() + '-' + index,
                    name: file.name,
                    size: (file.size / (1024*1024)).toFixed(2) + ' MB',
                    file: file
                };
                
                uploadedPhotos.push(photoData);
                renderPhotoItem(photoData);
            });
            
            // Update photo counter
            updatePhotoCounter();
            
            // Update hidden field
            updatePhotosInput();
        }
        
        function renderPhotoItem(photo) {
            const photoItem = document.createElement('div');
            photoItem.className = 'photo-list-item';
            photoItem.dataset.id = photo.id;
            
            photoItem.innerHTML = `
                <div class="photo-name" title="${photo.name}">
                    <i class="fas fa-image me-1 text-primary"></i>
                    ${truncateFileName(photo.name)}
                </div>
                <div class="photo-size">${photo.size}</div>
                <button type="button" class="photo-remove" onclick="removePhoto('${photo.id}')">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            if (photoList) photoList.appendChild(photoItem);
        }
        
        function truncateFileName(name) {
            if (name.length > 30) {
                return name.substring(0, 27) + '...';
            }
            return name;
        }
        
        window.removePhoto = function(photoId) {
            // Remove from array
            uploadedPhotos = uploadedPhotos.filter(photo => photo.id !== photoId);
            
            // Remove from DOM
            const photoElement = document.querySelector(`[data-id="${photoId}"]`);
            if (photoElement) {
                photoElement.remove();
            }
            
            // Update photo counter
            updatePhotoCounter();
            
            // Update hidden field
            updatePhotosInput();
        };
        
        function updatePhotoCounter() {
            if (photoCountElement) {
                // Count existing photos that haven't been removed
                const existingPhotosCount = document.querySelectorAll('.existing-photo').length;
                // Count restored photos that haven't been removed
                const restoredPhotosCount = document.querySelectorAll('.restored-photo').length;
                // Count new uploaded photos
                const newPhotosCount = uploadedPhotos.length;
                // Total count
                const totalCount = existingPhotosCount + restoredPhotosCount + newPhotosCount;
                
                photoCountElement.textContent = totalCount;
            }
        }

        // Global function to remove existing photos
        window.removeExistingPhoto = function(photoId) {
            const photoElement = document.querySelector(`[data-photo-id="${photoId}"]`);
            if (photoElement) {
                // Add animation
                photoElement.style.transition = 'opacity 0.3s, transform 0.3s';
                photoElement.style.opacity = '0';
                photoElement.style.transform = 'scale(0.8)';
                
                setTimeout(() => {
                    photoElement.remove();
                    updatePhotoCounter();
                    
                    // Add hidden input to mark for deletion
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'delete_photos[]';
                    hiddenInput.value = photoId;
                    document.getElementById('home-form').appendChild(hiddenInput);
                }, 300);
            }
        };
        
        function updatePhotosInput() {
            // Clear existing file input
            if (photoInput) photoInput.value = '';
            
            // Create new DataTransfer object
            const dataTransfer = new DataTransfer();
            
            // Add all photos as files
            uploadedPhotos.forEach(photo => {
                if (photo.file) {
                    dataTransfer.items.add(photo.file);
                }
            });
            
            // Update file input
            if (photoInput) photoInput.files = dataTransfer.files;
            
            // Ensure restored photos are still in the form
            const form = document.getElementById('home-form');
            if (form) {
                // Remove any existing restored photo inputs that might be duplicated
                const existingRestoredInputs = form.querySelectorAll('input[name="restored_photos[]"]');
                const restoredPhotoPaths = Array.from(document.querySelectorAll('.restored-photo')).map(el => el.dataset.photoPath);
                
                existingRestoredInputs.forEach(input => {
                    if (!restoredPhotoPaths.includes(input.value)) {
                        input.remove();
                    }
                });
                
                // Add any missing restored photo inputs
                restoredPhotoPaths.forEach(photoPath => {
                    const existingInput = form.querySelector(`input[name="restored_photos[]"][value="${photoPath}"]`);
                    if (!existingInput) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'restored_photos[]';
                        hiddenInput.value = photoPath;
                        hiddenInput.className = 'restored-photo-input';
                        form.appendChild(hiddenInput);
                    }
                });
            }
        }
    }
    
        // ====== Floating Action Button (Scroll Up/Down) ======
    const fabBtn = document.getElementById('fab-btn');
    const fabIcon = document.getElementById('fab-icon');
    
    if (fabBtn && fabIcon) {
        let lastScrollTop = 0;
        let isScrolling;
        
        // Show FAB only on mobile
        const isMobile = window.innerWidth <= 768;
        if (isMobile) {
            fabBtn.classList.add('show');
        }
        
        // Function to check scroll position
        function checkScrollPosition() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollHeight = document.documentElement.scrollHeight;
            const clientHeight = document.documentElement.clientHeight;
            
            // Clear our timeout throughout the scroll
            window.clearTimeout(isScrolling);
            
            // Determine scroll direction
            const scrollingDown = scrollTop > lastScrollTop;
            lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
            
            // Check if at top (within 50px from top)
            const atTop = scrollTop < 50;
            
            // Check if at bottom (within 50px from bottom)
            const atBottom = scrollHeight - (scrollTop + clientHeight) < 50;
            
            // Set a timeout to run after scrolling ends
            isScrolling = setTimeout(function() {
                if (atTop) {
                    // At top, show down arrow (go to bottom)
                    fabIcon.className = 'fas fa-arrow-down';
                } else if (atBottom) {
                    // At bottom, show up arrow (go to top)
                    fabIcon.className = 'fas fa-arrow-up';
                } else {
                    // In middle, show arrow based on scroll direction
                    fabIcon.className = scrollingDown ? 'fas fa-arrow-up' : 'fas fa-arrow-down';
                }
            }, 66); // Run every 66ms for smooth transition
        }
        
        // FAB click handler
        fabBtn.addEventListener('click', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollHeight = document.documentElement.scrollHeight;
            const clientHeight = document.documentElement.clientHeight;
            
            // Check current icon
            const isUpArrow = fabIcon.classList.contains('fa-arrow-up');
            
            if (isUpArrow) {
                // Currently showing up arrow, scroll to top
                window.scrollTo({ 
                    top: 0, 
                    behavior: 'smooth' 
                });
            } else {
                // Currently showing down arrow, scroll to bottom
                window.scrollTo({ 
                    top: scrollHeight,
                    behavior: 'smooth' 
                });
            }
        });
        
        // Listen for scroll events
        window.addEventListener('scroll', checkScrollPosition);
        
        // Handle window resize
        window.addEventListener('resize', function() {
            const isMobileNow = window.innerWidth <= 768;
            if (isMobileNow) {
                fabBtn.classList.add('show');
            } else {
                fabBtn.classList.remove('show');
            }
            checkScrollPosition(); // Re-check on resize
        });
        
        // Initial check
        checkScrollPosition();
        
        // Add a small delay to ensure DOM is fully loaded
        setTimeout(checkScrollPosition, 500);
    }
});