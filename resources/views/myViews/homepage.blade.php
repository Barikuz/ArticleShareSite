<!doctype html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Spatie</title>

    <!--Bootstrap Links-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!--JQuery Links-->
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

    <style>
        /* The Modal (background) */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }

        /* Modal Content/Box */
        .modal-content {
            background-color: #fefefe;
            margin: 8% auto; /* 8% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
        }

        /* The Close Button */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container" style="margin-top: 5rem">
        <div class="row g-5 mb-5" id="auth_operations" style="display: none">
            <div class="col-6 d-flex justify-content-end">
                <a class="btn btn-success btn-lg" href="{{route('login')}}">Giriş Yap</a>
            </div>
            <div class="col-6">
                <a class="btn btn-warning btn-lg" href="{{route('register')}}">Kayıt Ol</a>
            </div>
        </div>

        <div class="row mb-5 justify-content-center" id="account_operations" style="display: none">
            <div class="col-2 d-flex justify-content-center flex-column">
                @if($user)
                    <div class="btn btn-secondary btn-lg mb-3">{{$user->name}}</div>
                @endif
                @if($roles)
                    @foreach($roles as $role)
                        <div class="btn btn-success btn-lg mb-3 me w-100">{{$role}}</div>
                    @endforeach
                @endif

                <form action="{{route('logout')}}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-lg w-100">Çıkış Yap</button>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-12 d-flex flex-row justify-content-center">
                <div class="btn btn-success btn-lg" id="write" style="display: none">Yazı Yaz</div>
                <div class="btn btn-primary btn-lg ms-5" id="give_Role" style="display: none">Rol Ata</div>
            </div>

        </div>
    </div>

    <!-- The Modal -->
    <div id="myModal" class="modal">

        <!-- Modal content -->
        <div class="modal-content">
            <div id="write_content" style="display: none">
                <div class="d-flex justify-content-between mb-4">
                    <h3>Yazı Yaz</h3>
                    <span class="close">&times;</span>
                </div>

                <div class="form-floating">
                    <textarea class="form-control" style="height: 360px" placeholder="Leave a comment here" id="floatingTextarea" ></textarea>
                    <label for="floatingTextarea">Yaz</label>
                </div>
            </div>

            <div id="role_content" style="display: none">
                <div class="d-flex justify-content-between mb-4">
                    <h3>Rol Ver</h3>
                    <span class="close ">&times;</span>
                </div>
            </div>
        </div>

    </div>

    <script>
        const user = @json($user);
        const permissions = @json($permissions);

        if(user){
            $("#account_operations").show();
            $("#write").show();
        }else{
            $("#auth_operations").show();
        }


        if(permissions.includes("Assign Roles")){
            $("#give_Role").show();
        }
    </script>

    <script>
        // Get the modal
        var modal = $("#myModal");

        var writeContent = $("#write_content");
        var roleContent = $("#role_content");

        // Get the button that opens the modal
        var btn = $("#write");
        var btn2 = $("#give_Role");

        // Get the <span> element that closes the modal
        var span = $(".close");

        // When the user clicks on the button, open the modal
        btn.click(function() {
            modal.show();
            roleContent.hide();
            writeContent.show();
        })

        btn2.click(function() {
            modal.show();
            writeContent.hide();
            roleContent.show();
        })

        // When the user clicks on <span> (x), close the modal
        span.click(function() {
            modal.hide();
        })

        // When the user clicks anywhere outside of the modal, close it
        $(window).click(function(event) {
            if ($(event.target).is(modal)) {
                $(modal).hide();
            }
        });
    </script>
</body>
</html>
