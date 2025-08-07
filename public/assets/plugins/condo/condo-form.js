document.addEventListener('DOMContentLoaded', () => {
  const rowsContainer = document.getElementById('rows-container');
  const addRowBtn = document.getElementById('add-row-btn');

  // Format number with commas and 2 decimals
  function formatNumber(value) {
    const number = parseFloat(value.toString().replace(/,/g, ''));
    return isNaN(number) ? '' : number.toLocaleString('en-US', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    });
  }

  // Remove commas and convert to float
  function unformatNumber(value) {
    return parseFloat(value.toString().replace(/,/g, '')) || 0;
  }

  // Update serial numbers (No column)
  function updateSerialNumbers() {
    const rows = rowsContainer.querySelectorAll('.item-row');
    rows.forEach((row, i) => {
      const serialInput = row.querySelector('.serial');
      if (serialInput) serialInput.value = i + 1;
    });
  }

  // Calculate subtotal & price per unit for one row
  function calculateRow(row) {
    const materialCost = unformatNumber(row.querySelector('.material')?.value || '0');
    const laborCost = unformatNumber(row.querySelector('.labor')?.value || '0');
    const amount = unformatNumber(row.querySelector('.amount')?.value || '0');
    const pricePerUnitInput = row.querySelector('.price-per-unit');
    const subtotalInput = row.querySelector('.subtotal');

    // If material or labor cost filled, set price per unit = material + labor
    if ((materialCost > 0 || laborCost > 0) && pricePerUnitInput) {
      pricePerUnitInput.value = formatNumber(materialCost + laborCost);
    }

    const pricePerUnit = unformatNumber(pricePerUnitInput?.value || '0');
    if (subtotalInput) {
      subtotalInput.value = formatNumber(pricePerUnit * amount);
    }
  }

  // Calculate total, tax, and total price of all rows
  function calculateTotals() {
    let total = 0;
    rowsContainer.querySelectorAll('.subtotal').forEach(input => {
      total += unformatNumber(input.value);
    });

    const tax = total * 0.07;
    const finalTotal = total + tax;

    document.getElementById('totalDisplay').textContent = formatNumber(total);
    document.getElementById('taxDisplay').textContent = formatNumber(tax);
    document.getElementById('totalPriceDisplay').textContent = formatNumber(finalTotal);
  }
  

  // Listen to input changes on rows container (event delegation)
  rowsContainer.addEventListener('input', (e) => {
    const input = e.target;
    const row = input.closest('.item-row');
    if (!row) return;

    // Only process for relevant classes
    if (
      input.classList.contains('amount') ||
      input.classList.contains('material') ||
      input.classList.contains('labor') ||
      input.classList.contains('price-per-unit')
    ) {
      // Clean input to allow only numbers and dot
      input.value = input.value.replace(/[^0-9.]/g, '');

      calculateRow(row);
      calculateTotals();
    }
  });

  // Format numbers on blur event
  rowsContainer.addEventListener('blur', (e) => {
    const input = e.target;
    if (
      input.classList.contains('amount') ||
      input.classList.contains('material') ||
      input.classList.contains('labor') ||
      input.classList.contains('price-per-unit')
    ) {
      input.value = formatNumber(input.value);
    }
  }, true);

  // Handle remove row button click
  rowsContainer.addEventListener('click', (e) => {
    if (e.target.classList.contains('remove-row')) {
      const row = e.target.closest('.item-row');
      if (rowsContainer.querySelectorAll('.item-row').length > 1) {
        // Remove the entire parent .col-lg-12 container to keep consistent layout
        const colContainer = row.closest('.col-lg-12');
        if (colContainer) {
          colContainer.remove();
          updateSerialNumbers();
          calculateTotals();
        }
      } else {
        alert('At least one row is required.');
      }
    }
  });

  // Add new row button handler
addRowBtn.addEventListener('click', () => {
  const firstCol = rowsContainer.querySelector('.col-lg-12');
  if (!firstCol) return;

  const newCol = firstCol.cloneNode(true);

  // Clear all input fields except serial number
  newCol.querySelectorAll('input').forEach(input => {
    if (!input.classList.contains('serial')) {
      input.value = '';
    }
  });

  rowsContainer.appendChild(newCol);
  updateSerialNumbers();
});


  // Initial calculation on page load
  rowsContainer.querySelectorAll('.item-row').forEach(row => {
    calculateRow(row);
  });
  calculateTotals();
});
