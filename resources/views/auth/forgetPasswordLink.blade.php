@extends('layout')

@section('content')

<main class="login-form">

    <div class="cotainer">

        <div class="row justify-content-center">

            <div class="col-md-8">

                <div class="card">

                    <div class="card-header">Reset Password</div>

                    <div class="card-body">



                        <form action="{{ route('submit.forgotpassword.post') }}" method="POST">

                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">



                            <div class="form-group row">

                                <label for="email_address" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>

                                <div class="col-md-6">

                                    <input type="text" id="email_address" class="form-control" name="email" required autofocus>



                                </div>

                            </div>



                            <div class="form-group row">

                                <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>

                                <div class="col-md-6">

                                    <input type="password" id="password" class="form-control" name="password" required autofocus>


                                </div>

                            </div>



                            <div class="form-group row">

                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Confirm Password</label>

                                <div class="col-md-6">

                                    <input type="password" id="password-confirm" class="form-control" name="password_confirmation" required autofocus>


                                </div>

                            </div>



                            <div class="col-md-6 offset-md-4">

                                <button type="submit" class="btn btn-primary">

                                    Reset Password

                                </button>

                            </div>

                        </form>



                    </div>

                </div>

            </div>

        </div>

    </div>

</main>

@endsection