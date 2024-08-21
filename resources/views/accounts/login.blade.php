@extends('layouts.base-fullscreen')

@section('title') Login @endsection

@section('content')

<div class="content">
    <div class="container">
        <div class="row pt-5">
            <div class="col-md-6 mt-5 offset-md-3 pt-5 mt-5">
                <div class="card">

                    <div class="card-header text-center py-4">
                        <h4 class="title">
                            Sign IN
                        </h4>

                        <h6 class="card-category">
                            @if($msg)
                              <span class="text-danger">{{ $msg }}</span>
                            @else
                            <span>
                              Add your credentials
                            </span>
                            @endif
                        </h6>
                    </div>

                    <!-- Form action updated to route to login.submit -->
                    <form role="form" method="post" action="{{ route('login.submit') }}">
                        @csrf

                        <div class="card-body px-5 py-3">
                            <div class="row">
                                <div class="col-md-12 px-md-1">
                                    <div class="form-group">
                                        <label>Mobile Number</label>
                                        {!! $form->mobile_number !!}
                                    </div>
                                </div>

                                <div class="col-md-12 px-md-1">
                                    <div class="form-group">
                                        <label>Password</label>
                                        {!! $form->password !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            <button type="submit" name="login" class="btn btn-fill btn-primary">Login</button>

                            <br /><br />

                            <p>
                                Don't have an account? <a href="{{ route('register.form') }}" class="text-primary">Register</a>
                            </p>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>

</div>

@endsection
