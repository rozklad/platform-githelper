@extends('layouts/default')

{{-- Page title --}}
@section('title')
    @parent
    {{ trans('sanatorium/githelper::common.title') }}
@stop

{{-- Queue assets --}}
{{ Asset::queue('moment', 'moment/js/moment.js', 'jquery') }}

{{-- Inline scripts --}}
@section('scripts')
    @parent
@stop

{{-- Inline styles --}}
@section('styles')
    @parent
@stop

{{-- Page content --}}
@section('page')

        {{-- Grid --}}
        <section class="panel panel-default panel-grid">

            {{-- Grid: Header --}}
            <header class="panel-heading">

                <nav class="navbar navbar-default navbar-actions">

                    <div class="container-fluid">

                        <div class="navbar-header">

                            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#actions">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>

                            <span class="navbar-brand">{{ trans('sanatorium/githelper::common.title') }}</span>

                        </div>

                        <div class="collapse navbar-collapse" id="actions">

                            <ul class="nav navbar-nav navbar-right">

                                <li>№ {{ count($repos) }}</li>

                            </ul>

                        </div>

                    </div>

                </nav>

            </header>

            <table class="table table-responsive">
                <thead>
                    <th>Basename</th>
                    <th>Tag</th>
                    <th>Changed files</th>
                    <th></th>
                </thead>
                <tbody>
                @foreach( $repos as $repo )
                    <tr class="{{ ($repo['changed_files'] ? 'success' : '') }}">
                        <td>
                            <strong>{{ $repo['basename'] }}</strong><br>
                            {{ $repo['name'] }}
                        </td>
                        <td>
                            {{ $repo['last_tag'] }}
                        </td>
                        <td>
                            @if ( $repo['changed_files'] )
                                <strong>{{ $repo['changed_files'] }}</strong>
                            @else
                                <span class="text-muted">{{ $repo['changed_files'] }}</span>
                            @endif
                        </td>
                        <td>
                            <small>
                                <ul class="no-list">
                                    <li class="text-{{ $repo['has_readme'] ? 'success' : 'danger' }}">
                                        <i class="fa fa-{{ $repo['has_readme'] ? 'check' : 'times' }}" aria-hidden="true"></i>
                                        README.md
                                    </li>
                                    @if ( $repo['type'] == 'platform-extension' )
                                        <li class="text-{{ $repo['langs']['en'] ? 'success' : 'danger' }}">
                                            <i class="fa fa-{{ $repo['langs']['en'] ? 'check' : 'times' }}" aria-hidden="true"></i>
                                            Language - english
                                        </li>
                                        <li class="text-{{ $repo['langs']['cs'] ? 'success' : 'danger' }}">
                                            <i class="fa fa-{{ $repo['langs']['cs'] ? 'check' : 'times' }}" aria-hidden="true"></i>
                                            Language - czech
                                        </li>
                                    @endif
                                </ul>
                            </small>
                        </td>
                        <td>
                            <small>
                                <ul class="no-list">
                                    @foreach( $repo['authors'] as $author )
                                        @if ( isset($author['name']) )
                                            <li>{{ $author['name'] }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            </small>
                        </td>
                        <td class="text-right">
                            <a href="{{ route('sanatorium.githelper.readme', ['type' => 'patch']) }}?dir={{ $repo['dir'] }}" class="btn btn-default btn-sm" data-toggle="tooltip" data-title="{{ trans('sanatorium/githelper::common.buttons.readme') }}">
                                <i class="fa fa-file-text" aria-hidden="true"></i>
                            </a>

                            <a href="{{ route('sanatorium.githelper.tagpush', ['type' => 'minor']) }}?dir={{ $repo['dir'] }}" class="btn btn-default btn-sm" data-toggle="tooltip" data-title="{{ trans('sanatorium/githelper::common.buttons.tagpush.minor') }}">
                                <i class="fa fa-arrow-circle-o-up" aria-hidden="true"></i>
                            </a>

                            <a href="{{ route('sanatorium.githelper.tagpush') }}?dir={{ $repo['dir'] }}" class="btn btn-default btn-sm" data-toggle="tooltip" data-title="{{ trans('sanatorium/githelper::common.buttons.tagpush.default') }}">
                                <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </section>

@stop

