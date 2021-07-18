@extends('layouts.app')

@push('css-links')

@endpush

@section('content')
    @if(session()->has('success'))
        <div class="alert alert-success my-3">
            <p class="m-0">
                {!! session()->get('success')  !!}
            </p>
        </div>
    @endif
@endsection

@push('js-scripts')

@endpush
