@extends('layouts.master')

@section('title', __('Condo'))

@push('styles')
    <!-- DataTables CSS -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Project-specific tweaks -->
    <link href="{{ asset('assets/plugins/condo/condo-table.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="xp-contentbar">
        <div class="row">
            <div class="col-lg-12">
                <div class="card m-b-30">
                    <div class="card-header bg-white">
                        <h5 class="card-title text-black">{{ __('Condo Data Table') }}</h5>
                        <h6 class="card-subtitle">
                            {{ __('With DataTables you can alter the ordering characteristics of the table at initialisation time.') }}
                        </h6>
                        <div class="d-flex justify-content-end px-1.5 mt-n4 mb-1.5">
                            <a href="{{ route('condo.create') }}" class="btn btn-primary">
                                {{ __('+ Create Condo') }}
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive custom-table-wrapper">
                            <table id="xp-default-datatable"
                                   class="display table table-striped table-bordered table-sm align-middle nowrap"
                                   style="width:100%">
                                <thead>
                                    <tr>
                                        <th data-priority="1">{{ __('No.') }}</th>
                                        <th data-priority="3">{{ __('Address') }}</th>
                                        <th data-priority="2">{{ __('Job Name') }}</th>
                                        <th data-priority="4">{{ __('Date') }}</th>
                                        <th data-priority="2">{{ __('Total price') }}</th>
                                        <th data-priority="1" class="actions-col">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($condos as $i => $condo)
                                        <tr>
                                            {{-- Running number across pages --}}
                                            <td>{{ method_exists($condos, 'firstItem') ? $condos->firstItem() + $i : $i + 1 }}</td>

                                            <td>{{ $condo->address ?? '-' }}</td>

                                            <td>{{ $condo->job_name ?? '-' }}</td>

                                            <td>{{ optional($condo->quotation_date)->format('d-m-Y') ?: '-' }}</td>

                                            {{-- Show computed grand total (adjust accessor/field name if needed) --}}
                                            <td>{{ number_format($condo->computed_grand_total ?? ($condo->final_total ?? 0), 2) }}</td>

                                            <td class="actions-col">
                                                <div class="action-buttons">
                                                    {{-- Edit --}}
                                                    <a href="{{ route('condo.edit', $condo) }}" class="btn btn-action btn-edit" title="{{ __('Edit') }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M17.414 2.586a2 2 0 0 0-2.828 0L6.5 10.672 6 13.5l2.828-.5 8.086-8.086a2 2 0 0 0 0-2.828Zm-3.535 1.414 2.121 2.121-6.95 6.95-2.12-2.122 6.949-6.95ZM4 16.5h12a.5.5 0 0 1 0 1H4a.5.5 0 0 1 0-1Z"/>
                                                        </svg>
                                                    </a>

                                                    {{-- Delete --}}
                                                    <form action="{{ route('condo.destroy', $condo) }}" method="POST" class="d-inline"
                                                          onsubmit="return confirm('{{ __('Delete this record? This cannot be undone.') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-action btn-delete" title="{{ __('Delete') }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                                                <path d="M9 3h6a1 1 0 0 1 1 1v1h4a1 1 0 1 1 0 2h-1l-1 13a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 7H4a1 1 0 1 1 0-2h4V4a1 1 0 0 1 1-1Zm2 0v1h2V3h-2ZM8 7l1 13h6l1-13H8Zm2 3a1 1 0 1 1 2 0v7a1 1 0 1 1-2 0v-7Zm4 0a1 1 0 1 1 2 0v7a1 1 0 1 1-2 0v-7Z"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">{{ __('No condos found.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Laravel pagination links (optional if you keep client paging only) --}}
                        @if(method_exists($condos, 'links'))
                            <div class="mt-3">
                                {{ $condos->links() }}
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div> <!-- /row -->
    </div> <!-- /xp-contentbar -->
@endsection

@push('scripts')
    <!-- DataTables JS -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Buttons Examples -->
    <script src="{{ asset('assets/plugins/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/buttons.colVis.min.js') }}"></script>

    <!-- Responsive -->
    <script src="{{ asset('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

    <!-- Optional: language auto-load (like senior) -->
    <script>
      (function(){
        var lang = document.documentElement.lang || '{{ app()->getLocale() }}' || 'en';
        var urls = {
          th: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/th.json',
          en: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/en-GB.json'
        };
        if (window.jQuery && jQuery.fn && jQuery.fn.dataTable) {
          jQuery.extend(true, jQuery.fn.dataTable.defaults, {
            language: { url: urls[lang] || urls.en }
          });
        }
      })();
    </script>

    <!-- Your init -->
    <script src="{{ asset('assets/js/init/table-datatable-init.js') }}"></script>
@endpush
