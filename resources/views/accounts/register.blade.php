@extends('layouts.base-fullscreen')

@section('title', 'Register')

@section('stylesheets')
@endsection

@section('content')
    <div class="content">
        <div class="container">
            <div class="row pt-5">
                <div class="col-md-6 mt-5 offset-md-3 pt-5 mt-5">
                    <div class="card">
                        <div class="card-header text-center py-4">
                            <h4 class="title">Sign UP</h4>
                            <h6 class="card-category">
                                @if ($msg)
                                    <span class="text-danger">{!! $msg !!}</span>
                                @else
                                    <span>Add your credentials</span>
                                @endif
                            </h6>
                        </div>

                        @if ($success)
                            <div class="card-footer text-center">
                                <a href="{{ route('login') }}" class="btn btn-fill btn-primary">Login</a>
                            </div>
                        @else
                            <form role="form" method="post" action="{{ route('register.submit') }}">
                                @csrf

                                <div class="card-body px-5 py-3">
                                    <div class="row">
                                        <div class="col-md-12 px-md-1">
                                            <div class="form-group">
                                                <label>Username</label>
                                                {!! $form->username->input !!}
                                                @if ($form->username->errors)
                                                    <span class="text-danger">{{ $form->username->errors[0] }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-12 px-md-1">
                                            <div class="form-group">
                                                <label>Email</label>
                                                {!! $form->email->input !!}
                                                @if ($form->email->errors)
                                                    <span class="text-danger">{{ $form->email->errors[0] }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-12 px-md-1">
                                            <div class="form-group">
                                                <label>Password</label>
                                                {!! $form->password1->input !!}
                                                @if ($form->password1->errors)
                                                    <span class="text-danger">{{ $form->password1->errors[0] }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-12 px-md-1">
                                            <div class="form-group">
                                                <label>Password Check</label>
                                                {!! $form->password2->input !!}
                                                @if ($form->password2->errors)
                                                    <span class="text-danger">{{ $form->password2->errors[0] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer text-center">
                                    <button type="submit" name="register" class="btn btn-fill btn-primary">Register</button>

                                    <br /><br />

                                    <p>
                                        Have an account? <a href="{{ route('login') }}" class="text-primary">Login</a>
                                    </p>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascripts')
@endsection
