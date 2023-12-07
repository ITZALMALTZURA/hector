<html>

<head>
    <title>Login</title>
    <style>
        .active {
            text-decoration: none;
            color: green;
        }

        .error {
            color: red;
            font-size: 14px;
        }
    </style>

</head>


<body style="background-color:  #171D23">
    <br /><br />
    <div align="center">
        <div class="col-sm-4">
            <div class="card">
                <div class="card-header" style="background-color:  rgba(79,97,117);">
                </div>
                <div class="container"><br/>
                    <center><br/><br/><br/>
                        <div class="col-sm-10">
                            <form method="POST" action="login">
                                {!! csrf_field() !!}
                                {!! $errors->first('email', '<br/><span class=error>:message</span>') !!}
                                <input type="email" name="email" value="{{ old('email') }}" placeholder="Correo"
                                    class="form-control">
                                <br />
                                {!! $errors->first('password', '<br/><span class=error>:message</span>') !!}
                                <input type="password" name="password" value="{{ old('password') }}"
                                    placeholder="Password" class="form-control">

                                <br /><br />
                                <button class="btn form-control"
                                    style="background-color: rgba(79,97,117); color: #FFFFFF; font-size: 18px">Ingresar</button>
                                <br /><br /><br />
                            </form>
                        </div>
                    </center>
                </div>
            </div>
</body>

</html>
