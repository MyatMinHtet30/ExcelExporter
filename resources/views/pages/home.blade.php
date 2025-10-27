@extends('layouts.master')

@section('title', __('Home'))

@push('styles')
<!-- DataTables CSS -->
<link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive Datatable CSS -->
<link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    @media (max-width: 576px) {
        .card-header .d-flex.justify-content-end {
            margin-top: 0 !important;
            margin-bottom: 1rem !important;
        }

        .custom-table-wrapper{
            margin-left:  -15px;   /* extend wrapper beyond card padding */
            margin-right: -15px;
            padding-left: 15px;    /* bring content back in with equal space */
            padding-right:15px;
            box-sizing: border-box;
            background: #fff;      /* match card so the gap is white */
        }
        #xp-default-datatable_wrapper .row:last-child > .col-sm-12.col-md-5,
        #xp-default-datatable_wrapper .row:last-child > .col-sm-12.col-md-7 {
            margin-left: 175px; /* adjust value as needed */
        }
    }

    @media (max-width: 768px) {
        .card-header .d-flex.justify-content-end {
        margin-top: 0 !important;       /* remove the negative top margin */
        margin-bottom: 1rem !important; /* add spacing like mb-3 */
        justify-content: flex-end !important; /* keep it aligned right */
      }
    }
</style>
@endpush

@section('content')
    <div class="xp-contentbar">
        <div class="row">
            <div class="col-lg-12">
                <div class="card m-b-30">
                    <div class="card-header bg-white">
                        <h5 class="card-title text-black">{{ __('Home Data Table') }}</h5>
                        <h6 class="card-subtitle">
                            {{ __('With DataTables you can alter the ordering characteristics of the table at initialisation time.') }}
                        </h6>
                        <div class="d-flex justify-content-end px-1.5 mt-n4 mb-1.5">
                            {{-- Go to /createhome --}}
                            <a href="{{ route('home.create') }}" class="btn btn-primary">
                                {{ __('+ Create Home') }}
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <table id="xp-default-datatable" class="display table table-striped table-bordered align-middle">
      <thead>
        <tr>
          <th>{{ __('Project Name') }}</th>
          <th>{{ __('Dear') }}</th>
          <th>{{ __('Trooper') }}</th>
          <th>{{ __('House No.') }}</th>
          <th>{{ __('List Name') }}</th>
          <th>{{ __('Total Price') }}</th>
          <th class="text-center" style="width:120px">{{ __('Actions') }}</th>
        </tr>
      </thead>

      <tbody>
        @forelse ($homes as $home)
              <tr>
                <td>{{ $home->project_name }}</td>
                <td>{{ $home->dear }}</td>
                <td>{{ $home->trooper }}</td>
                <td>{{ $home->house_no }}</td>
                <td>{{ $home->list_name }}</td>
                <td>{{ number_format($home->total_price, 2) }}</td>
                <td class="text-center">
                  {{-- Edit (hide for now; remove d-none when you’re ready)
                {{-- <a href="{{ route('home.edit', $home) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('Edit') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="16" height="16" fill="currentColor"
                        aria-hidden="true">
                        <path
                            d="M17.414 2.586a2 2 0 0 0-2.828 0L6.5 10.672 6 13.5l2.828-.5 8.086-8.086a2 2 0 0 0 0-2.828Zm-3.535 1.414 2.121 2.121-6.95 6.95-2.12-2.122 6.949-6.95ZM4 16.5h12a.5.5 0 0 1 0 1H4a.5.5 0 0 1 0-1Z" />
                    </svg>
                </a> --}}

                <!-- Delete -->
                <form action="{{ route('home.destroy', $home) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('{{ __('Delete this record? This cannot be undone.') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"
                            aria-hidden="true">
                            <path
                                d="M9 3h6a1 1 0 0 1 1 1v1h4a1 1 0 1 1 0 2h-1l-1 13a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 7H4a1 1 0 1 1 0-2h4V4a1 1 0 0 1 1-1Zm2 0v1h2V3h-2ZM8 7l1 13h6l1-13H8Zm2 3a1 1 0 1 1 2 0v7a1 1 0 1 1-2 0v-7Zm4 0a1 1 0 1 1 2 0v7a1 1 0 1 1-2 0v-7Z" />
                        </svg>
                    </button>
                </form>
                </td>
              </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-4">{{ __('No homes found.') }}</td>
          </tr>
        @endforelse
      </tbody>
    </table>
                    </div>

                </div>
            </div>
        </div> <!-- /row -->
    </div> <!-- /xp-contentbar -->
@endsection

@push('scripts')
    <!-- Required Datatable JS -->
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

    <!-- Responsive Examples -->
    <script src="{{ asset('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

    <!-- Set DataTables language based on locale BEFORE init -->
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

    <script src="{{ asset('assets/js/init/table-datatable-init.js') }}"></script>

@endpush
