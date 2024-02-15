<style>
    body {
        padding: 0 !important;
    }
    .dataTables_processing{
        color:#fff !important;
        background: #353d47ff !important;
        padding: 10px !important;
    }
</style>
@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing') . ' ' . $dataType->getTranslatedAttribute('display_name_plural'))

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="{{ $dataType->icon }}"></i> {{ $dataType->getTranslatedAttribute('display_name_plural') }}
        </h1>
        {{-- @can('add', app($dataType->model_name))
            <a href="{{ route('voyager.' . $dataType->slug . '.create') }}" class="btn btn-success btn-add-new">
                <i class="voyager-plus"></i> <span>{{ __('voyager::generic.add_new') }}</span>
            </a>
        @endcan
        @can('delete', app($dataType->model_name))
            @include('voyager::partials.bulk-delete')
        @endcan --}}
        @can('edit', app($dataType->model_name))
            @if (!empty($dataType->order_column) && !empty($dataType->order_display_column))
                <a href="{{ route('voyager.' . $dataType->slug . '.order') }}" class="btn btn-primary btn-add-new">
                    <i class="voyager-list"></i> <span>{{ __('voyager::bread.order') }}</span>
                </a>
            @endif
        @endcan
        @include('voyager::multilingual.language-selector')
    </div>
@stop


@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        @if ($isServerSide)
                            <form method="get" class="form-search">
                                <div id="search-input">
                                    <div class="input-group col-md-12">
                                        <input type="text" class="form-control"
                                            placeholder="{{ __('voyager::generic.search') }}" name="s"
                                            value="{{ $search->value }}">
                                        <span class="input-group-btn">
                                            <button class="btn btn-info btn-lg" type="submit">
                                                <i class="voyager-search"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                                @if (Request::has('sort_order') && Request::has('order_by'))
                                    <input type="hidden" name="sort_order" value="{{ Request::get('sort_order') }}">
                                    <input type="hidden" name="order_by" value="{{ Request::get('order_by') }}">
                                @endif
                            </form>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-bordered data-table" width="100%">
                                <thead>
                                    <tr>
                                        <th>Box Name</th>
                                        <th>User Name</th>
                                        <th>QR</th>
                                        <th width="100px">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Single view modal --}}
    <div class="modal modal-primary fade" tabindex="-1" id="view_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                        aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Items</h4>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered view_table" width="100%">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Category</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>


            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@stop

@section('css')
    @if (!$dataType->server_side && config('dashboard.data_tables.responsive'))
        <link rel="stylesheet" href="{{ voyager_asset('lib/css/responsive.dataTables.min.css') }}">
    @endif
@stop
@section('javascript')
    <script></script>
    <script type="text/javascript">
        $(function() {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('voyager.box.index') }}",
                columns: [{
                        data: 'box_name',
                        name: 'name'
                    },
                    {
                        data: 'user_name',
                        name: 'user'
                    },
                    {
                        data: 'QR',
                        name: 'QR'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
            table.on('click', '#view-btn', function() {
                var id = $(this).data('id');
                var url = '{{ route('item_view', ':id') }}';
                url = url.replace(':id', id);
                var view = $('.view_table').DataTable({
                    destroy: true,
                    searching: false,
                    processing: true,
                    serverSide: true,
                    ajax: url,
                    columns: [
                        {
                            data: 'item_name',
                            name: 'item_name'
                        },
                        {
                            data: 'item_category.category_name',
                            name: 'item_category.category_name',
                            render: function(data, type, row) {
                                return data ? data : '';
                            }
                        }
                    ]
                });
                $('#view_modal').modal('show');
            })

        });
    </script>

@stop
