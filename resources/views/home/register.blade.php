@extends('layouts.base-fullscreen')

@section('title', 'Register')

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
                            <h4 class="title">
                                Sign UP
                            </h4>
                        </div>
                        <div class="card-body px-5 py-3">
                            <form>
                                <div class="row">
                                    <div class="col-md-12 px-md-1">
                                        <div class="form-group">
                                            <label>Username</label>
                                            <input type="text" class="form-control" placeholder="Username" value="michael23">
                                        </div>
                                    </div>

                                    <div class="col-md-12 px-md-1">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" class="form-control" placeholder="email" value="example@gmail.com">
                                        </div>
                                    </div>

                                    <div class="col-md-12 px-md-1">
                                        <div class="form-group">
                                            <label>Password</label>
                                            <input type="password" class="form-control" placeholder="password" value="example@gmail.com">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer text-center">
                            <button type="submit" class="btn btn-fill btn-primary">Register</button>

                            <br /><br />
                            
                            <p>
                                Have an account? <a href="/login.html" class="text-primary">Login</a>
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