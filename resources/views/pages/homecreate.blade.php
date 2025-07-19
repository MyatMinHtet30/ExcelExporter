@extends('layouts.master')

@section('title', 'New Home Data Entry')

@push('styles')
<style>
    .remove-row-btn {
        cursor: pointer;
        color: red;
        padding: 0.25rem 0.5rem;
        font-size: 0.9rem;
    }

    /* Flex container for input + mic icon */
    .input-icon-wrapper {
        display: flex;
        align-items: center;
        position: relative;
    }

    /* Make input fill available width */
    .input-icon-wrapper input.form-control {
        flex-grow: 1;
        padding-right: 0.5rem;
    }

    /* Mic icon style */
    .speech-icon {
        width: 20px;
        height: 20px;
        cursor: pointer;
        margin-left: 0.5rem;
        user-select: none;
        pointer-events: none; /* so clicks go to input */
    }

    /* Default min width for the table */
    #items-table {
        min-width: 900px;
    }

    /* Adjust min width for tablets */
    @media (max-width: 768px) {
        #items-table {
            min-width: 700px;
        }
    }

    /* Column widths */
    #items-table th:nth-child(1),
    #items-table td.row-no {
        width: 50px;
        text-align: center;
        vertical-align: middle;
    }
    #items-table th:last-child,
    #items-table td:last-child {
        width: 70px;
        text-align: center;
        vertical-align: middle;
        padding: 0.25rem;
    }
    #items-table th:nth-child(2),
    #items-table td.details-column {
        width: 50%;
    }
    #items-table th:nth-child(3),
    #items-table td.amount-column {
        width: 15%;
    }
    #items-table th:nth-child(4),
    #items-table td.input-column:nth-child(4) {
        width: 10%;
    }
    #items-table th:nth-child(5),
    #items-table td.input-column:nth-child(5) {
        width: 10%;
    }
    #items-table th:nth-child(6),
    #items-table td.input-column:nth-child(6) {
        width: 10%;
    }
    /* Inputs fill entire cell width */
    #items-table input.form-control {
        width: 100%;
        max-width: none;
    }
</style>
@endpush

@section('content')
<div class="xp-contentbar">
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30">
                <div class="card-header bg-white">
                    <h5 class="card-title text-black">New Home Data Entry</h5>
                    <h6 class="card-subtitle">Fill the form below to add new home data.</h6>
                </div>
                <div class="card-body">

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('home.store') }}" method="POST">
                        @csrf

                        {{-- Main form fields with mic icon and placeholder --}}
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="project_name">Project Name:</label>
                                <x-input-with-mic
                                    name="project_name"
                                    id="project_name"
                                    placeholder="Enter project name"
                                    :value="old('project_name')"
                                    required
                                />
                            </div>
                            <div class="form-group col-md-6">
                                <label for="dear">Dear:</label>
                                <x-input-with-mic
                                    name="dear"
                                    id="dear"
                                    placeholder="Enter recipient's name"
                                    :value="old('dear')"
                                    required
                                />
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="date">Date:</label>
                                <x-input-with-mic
                                    type="date"
                                    name="date"
                                    id="date"
                                    placeholder="Select date"
                                    :value="old('date')"
                                    required
                                />
                            </div>
                            <div class="form-group col-md-6">
                                <label for="house_no">House No:</label>
                                <x-input-with-mic
                                    name="house_no"
                                    id="house_no"
                                    placeholder="Enter house number"
                                    :value="old('house_no')"
                                    required
                                />
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="list_name">List Name:</label>
                                <x-input-with-mic
                                    name="list_name"
                                    id="list_name"
                                    placeholder="Enter list name"
                                    :value="old('list_name')"
                                    required
                                />
                            </div>
                            <div class="form-group col-md-6">
                                <label for="status">Status:</label>
                                <select id="status" name="status" class="form-control">
                                    <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        {{-- Dynamic Rows --}}
                        <div class="form-group">
                            <label>Items</label>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="items-table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Details</th>
                                            <th>Amount</th>
                                            <th>Units</th>
                                            <th>Material Cost</th>
                                            <th>Labor Price</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="row-no">1</td>
                                            <td class="details-column">
                                                <x-input-with-mic
                                                    name="items[0][details]"
                                                    placeholder="Enter details"
                                                    required
                                                />
                                            </td>
                                            <td class="amount-column">
                                                <x-input-with-mic
                                                    type="number"
                                                    name="items[0][amount]"
                                                    placeholder="0"
                                                    required
                                                />
                                            </td>
                                            <td class="input-column">
                                                <x-input-with-mic
                                                    name="items[0][units]"
                                                    placeholder="Unit"
                                                    required
                                                />
                                            </td>
                                            <td class="input-column">
                                                <x-input-with-mic
                                                    type="number"
                                                    name="items[0][material_cost]"
                                                    placeholder="0"
                                                    required
                                                />
                                            </td>
                                            <td class="input-column">
                                                <x-input-with-mic
                                                    type="number"
                                                    name="items[0][labor_price]"
                                                    placeholder="0"
                                                    required
                                                />
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm remove-row-btn" disabled>&times;</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" id="add-row-btn" class="btn btn-primary mt-2">+ Add Row</button>
                        </div>

                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let rowIndex = 1;

    function updateRowNumbers() {
        const rows = document.querySelectorAll('#items-table tbody tr');
        rows.forEach((row, index) => {
            row.querySelector('.row-no').textContent = index + 1;

            const inputs = row.querySelectorAll('input');
            inputs.forEach(input => {
                const name = input.name;
                const newName = name.replace(/items\[\d+\]/, `items[${index}]`);
                input.name = newName;
            });
        });
    }

    document.getElementById('add-row-btn').addEventListener('click', function() {
        const tbody = document.querySelector('#items-table tbody');
        const newRow = document.createElement('tr');

        newRow.innerHTML = `
            <td class="row-no"></td>
            <td class="details-column">
                <x-input-with-mic
                    name="items[${rowIndex}][details]"
                    placeholder="Enter details"
                    required
                />
            </td>
            <td class="amount-column">
                <x-input-with-mic
                    type="number"
                    name="items[${rowIndex}][amount]"
                    placeholder="0"
                    required
                />
            </td>
            <td class="input-column">
                <x-input-with-mic
                    name="items[${rowIndex}][units]"
                    placeholder="Unit"
                    required
                />
            </td>
            <td class="input-column">
                <x-input-with-mic
                    type="number"
                    name="items[${rowIndex}][material_cost]"
                    placeholder="0"
                    required
                />
            </td>
            <td class="input-column">
                <x-input-with-mic
                    type="number"
                    name="items[${rowIndex}][labor_price]"
                    placeholder="0"
                    required
                />
            </td>
            <td><button type="button" class="btn btn-danger btn-sm remove-row-btn">&times;</button></td>
        `;

        tbody.appendChild(newRow);

        newRow.querySelector('.remove-row-btn').addEventListener('click', () => {
            newRow.remove();
            updateRowNumbers();
        });

        rowIndex++;
        updateRowNumbers();
    });

    document.addEventListener('DOMContentLoaded', () => {
        updateRowNumbers();
    });
</script>
@endpush
