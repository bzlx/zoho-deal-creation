@extends('app')

@section('content')

    <!-- Bootstrap Boilerplate... -->

    <div class="panel-body">
        {{-- zoho api response --}}
        {{ dd($jsonResponse)}}

    </div>
@endsection