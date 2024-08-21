@extends('layouts.base-fullscreen')

@section('title', 'Error 403')

{{-- Specific Page CSS goes HERE --}}
@section('stylesheets')
@endsection

@section('content')

    <div class="content">
        <div class="container">
            <div class="row pt-5">
                <div class="col-md-6 mt-5 offset-md-3 pt-5 mt-5">
                    <div class="card">
                        <div class="card-header text-center py-4">
                            <h3 class="title">
                                Error 403
                            </h3>
                        </div>
                        <div class="card-body px-2 py-2">
                            <p class="text-center">
                                Access denied - Please contact support.
                            </p>
                        </div>
                        <div class="card-footer text-center">
                            <a href="/" class="btn btn-fill btn-primary">HOME</a>
                            
                            <br /><br />

                            <p>
                                Get <a href="#" class="text-primary">Support</a>                            
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

{{-- Specific Page JS goes HERE --}}
@section('javascripts')
@endsection