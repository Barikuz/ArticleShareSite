<!doctype html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Spatie</title>

    <!--CSRF-->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!--Bootstrap Links-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!--JQuery Links-->
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

                <div class="btn btn-success btn-lg mb-3 me w-100" id="userRoles" style="display: none"></div>

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


                <div id="Users">

                </div>
            </div>
        </div>

    </div>

    <script>
        const user = @json($user);
        let permission;
        const giveRoleButton = $("#give_Role");

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        if(user){
            $("#account_operations").show();
            $("#write").show();
        }else{
            $("#auth_operations").show();
        }

        function getUserRolesAJAX(){
            $.ajax({
                type:"get",
                url:"/getRoles",
                success: writeCurrentUserRoles,
            })
        }

        getUserRolesAJAX();

        function writeCurrentUserRoles(data){
            const roleSection = $("#userRoles");
            roleSection.empty();
            if(data.length === 0){
                roleSection.hide();
            }
            else{
                data.forEach(function (role){
                    roleSection.append(role + "<br>")
                })
                roleSection.show();
            }
        }

        function getUserPermissionsAJAX(){
            $.ajax({
                type:"get",
                url:"/getPermissions",
                success: showRoleAssignButton,
            })
        }

        getUserPermissionsAJAX();

        function showRoleAssignButton(data){
            permission = data;
            if(permission.includes("Assign Roles")){
                giveRoleButton.show();
            }
            else{
                modal.hide();
                giveRoleButton.hide();
            }
        }

        $.ajax({
            type:"get",
            url:"/getUsers",
            success: writeUsers,
        })

        function writeUsers(users){
            Object.entries(users).forEach(function (user){
                $("#Users").append(
                    "<div class='card shadow p-4 mt-3 d-flex flex-row justify-content-between'>"
                        + user[1]
                        + "<div>"
                            + "<div class ='btn btn-primary btn-md me-3' id='Admin' onclick='controlAdminRole("+ user[0] + ")'> Admin </div>"
                            + "<div class ='btn btn-secondary btn-md' id='User' onclick='controlUserRole(" + user[0] + ")'> Kullanıcı </div>"
                        +"</div>"
                    + "</div>"
                );
            })
        }

        function controlAdminRole (id){
            $.ajax({
                type:"post",
                url:"/manageRole",
                data:{id:id,type:"Admin"},
                success:function (data){
                    if(data === "Added"){
                        Swal.fire(
                            {
                                icon: "success",
                                title: "Rol Başarıyla Eklendi",
                            }
                        )
                    }else{
                        Swal.fire(
                            {
                                icon: "success",
                                title: "Rol Başarıyla Silindi",
                            }
                        )
                    }
                    getUserRolesAJAX();
                    getUserPermissionsAJAX();
                }
            })
        }

        function controlUserRole (id){
            $.ajax({
                type:"post",
                url:"/manageRole",
                data:{id:id,type:"Kullanıcı"},
                success:function (data){
                    if(data === "Added"){
                        Swal.fire(
                            {
                                icon: "success",
                                title: "Rol Başarıyla Eklendi",
                            }
                        )
                    }else{
                        Swal.fire(
                            {
                                icon: "success",
                                title: "Rol Başarıyla Silindi",
                            }
                        )
                    }
                    getUserRolesAJAX()
                    getUserPermissionsAJAX();
                }
            })
        }
    </script>

    <script>
        // Get the modal
        const modal = $("#myModal");

        const writeContent = $("#write_content");
        const roleContent = $("#role_content");

        // Get the button that opens the modal
        const btn = $("#write");
        const btn2 = $("#give_Role");

        // Get the <span> element that closes the modal
        const span = $(".close");

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
