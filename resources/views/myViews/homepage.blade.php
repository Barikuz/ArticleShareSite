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

        <div class="row mb-5 justify-content-center" id="account_operations_and_info" style="display: none">
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

        <div class="row mb-5">
            <div class="col-12 d-flex flex-row justify-content-center">
                <div class="btn btn-success btn-lg" id="write" style="display: none">Yazı Yaz</div>
                <div class="btn btn-primary btn-lg ms-5" id="give_Role" style="display: none">Rol Ata</div>
            </div>
        </div>

        <div class="row" id="texts_header_section" style="display: none">
            <h3 class="text-center">Yazılar</h3>
            <div class="col-12 d-flex flex-row justify-content-center">
                <div class="btn btn-secondary btn-lg my-3" onclick="getUserText()" id="filter_User_Texts">
                    Sadece Benim
                    <br>
                    Yazılarım
                </div>
            </div>
        </div>

        <div class="row mb-5 justify-content-center" id="texts_section">
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
                    <textarea class="form-control" style="height: 360px;resize: none" placeholder="Leave a comment here" id="floatingTextarea" ></textarea>
                    <label for="floatingTextarea">Yaz</label>
                </div>
                <div class="mt-4 d-flex justify-content-end">
                    <div class="btn btn-success btn-lg" id="write_modal" onclick="saveText()">Gönder</div>
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

            <div id="edit_content" style="display: none">
                <div class="d-flex justify-content-between mb-4">
                    <h3>Yazıyı Düzenle</h3>
                    <span class="close">&times;</span>
                </div>

                <div class="form-floating">
                    <textarea class="form-control" style="height: 360px;resize: none" placeholder="Leave a comment here" id="floatingEditTextarea" ></textarea>
                    <label for="floatingEditTextarea">Düzenle</label>
                </div>
                <div class="mt-4 d-flex justify-content-end">
                    <div class="btn btn-success btn-lg" id="edit_button" >Düzenle</div>
                </div>

            </div>
        </div>

    </div>

    <script>
        const user = @json($user);
        let permissions;
        const textHeaderSection = $("#texts_header_section");
        let userTexts = "";
        let isOnlyMyTextsVisible = false;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        if(user){
            $("#account_operations_and_info").show();
            $("#write").show();
            $("#give_Role").show();
            getAuthorizedUserRolesAJAX();
            getAuthorizedUserPermissionsAJAX();
            getAllUsersAJAX();
            getTextsAJAX(false);
        }else{
            $("#auth_operations").show();
        }

        function controlTextsHeaderSection(){
            if(permissions.includes("See Texts")){
                textHeaderSection.show();
            }else{
                textHeaderSection.hide();
            }
        }

        function writeAdminActions(textOperations,index,text,id){
            textOperations.append(
                "<div class ='btn btn-primary me-3' onclick='editTextModal(\"" + text + "\"" + "," + "\"" + id +  "\")' id='edit_text" + index + "'> Düzenle </div>"
            )

            textOperations.append(
                "<div class ='btn btn-danger' onclick='deleteText(\"" + id +  "\")' id='delete_text" + index + "'> Sil </div>"
            )
        }

        function controlCanTextsSee(data,isFromManageUserRole){
            if(permissions.includes("See Texts")){
                data.forEach(function (text,index){
                    const userText =
                        "<div class='card shadow p-4 mt-3 d-flex flex-row justify-content-between w-75'>"
                            +"<div>"
                                +"<h5>" + text.get_user.name + "</h5>"
                                +"<p class='mb-0'>"+ text.text + "</p>"
                            +"</div>"

                            +"<div class='d-flex align-items-center' id='text_Operations" + index + "'>"
                            +"</div>"
                        +"</div>"

                    if(!userTexts.includes(userText)){
                        $("#texts_section").append(userText)
                        userTexts += userText;

                        if(!isFromManageUserRole){
                            const textOperations = $("#text_Operations" + index);

                            writeAdminActions(textOperations,index,text.text,text.id);
                        }
                    }
                })
            }
            else{
                $("#texts_section").empty();
            }
        }

        function getAuthorizedUserRolesAJAX(){
            $.ajax({
                type:"get",
                url:"/getAuthorizedUserRoles",
                success: writeAuthorizedUserRoles,
            })
        }

        function writeAuthorizedUserRoles(data){
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

        function getAuthorizedUserPermissionsAJAX(){
            $.ajax({
                type:"get",
                url:"/getAuthorizedUserPermissions",
                success: checkPermissions,
            })
        }

        function checkPermissions(data){
            permissions = data;

            controlTextsHeaderSection();
        }

        function getAllUsersAJAX(){
            $.ajax({
                type:"get",
                url:"/getUsers",
                data: {type:"getUsers"},
                success: writeAllUsers,
            })
        }

        function writeAllUsers(users){
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
                url:"/manageRoles",
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
                    getAuthorizedUserRolesAJAX();
                    getAuthorizedUserPermissionsAJAX();
                    getTextsAJAX(false);
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: xhr.responseJSON?.message,
                    });
                }
            })
        }

        function controlUserRole (id){
            $.ajax({
                type:"post",
                url:"/manageRoles",
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
                    getAuthorizedUserRolesAJAX()
                    getAuthorizedUserPermissionsAJAX();
                    getTextsAJAX(true);
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: xhr.responseJSON?.message,
                    });
                }
            })
        }

        function saveText(){
            let data = $("#floatingTextarea").val();
            $.ajax({
                type:"post",
                url:"/saveTexts",
                data:{id:user.id,text:data,type:"saveText"},
                success:function (){
                    Swal.fire(
                        {
                            icon: "success",
                            title: "İşlem Başarılı!",
                        }
                    )
                    setTimeout(function (){
                        modal.hide()
                    },1200)

                    getTextsAJAX(false);
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: xhr.responseJSON?.message,
                    });
                }
            })
        }

        function getTextsAJAX(isFromManageUserRole){
            $.ajax({
                type:"get",
                url:"/getTexts",
                data:{type:"getTexts"},
                success:function (data){
                    controlCanTextsSee(data,isFromManageUserRole);
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: xhr.responseJSON?.message,
                    });
                }
            })
        }

        function getUserText(){
            $.ajax({
                type:"get",
                url:"/getUserTexts",
                data:{type:"getUserTexts"},
                success:function (data){
                    userTexts = "";
                    $("#texts_section").empty();
                    if(!isOnlyMyTextsVisible){
                        controlCanTextsSee(data,false)
                        isOnlyMyTextsVisible = true;
                    }else{
                        getTextsAJAX(false);
                        isOnlyMyTextsVisible = false;
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: xhr.responseJSON?.message,
                    });
                }
            })
        }

        function editText(text,id){
            $.ajax({
                type: "post",
                url: "/editTexts",
                data: {id:id,text:text,type:"editText"},
                success: function (){
                    Swal.fire({
                        icon: "success",
                        title: "Düzenleme Başarılı"
                    })

                    modal.hide();
                    $("#texts_section").empty();
                    userTexts = "";
                    getTextsAJAX(false);
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: xhr.responseJSON?.message,
                    });
                }
            })
        }

        function deleteText(id){
            Swal.fire({
                icon: "warning",
                title: "Bu yazıyı silmek istediğinize emin misiniz?",
                showCancelButton: true,
                confirmButtonText: 'Evet, devam et',
                cancelButtonText: 'Hayır, iptal et'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "post",
                        url: "/deleteTexts",
                        data: {id: id,type:"deleteText"},
                        success: function () {
                            Swal.fire({
                                icon: "success",
                                title: "Silme Başarılı"
                            })

                            modal.hide();
                            $("#texts_section").empty();
                            userTexts = "";
                            getTextsAJAX(false);
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Hata!',
                                text: xhr.responseJSON?.message,
                            });
                        }
                    })
                }
            })
        }
    </script>

    <script>
        // Get the modal
        const modal = $("#myModal");

        const writeContent = $("#write_content");
        const giveRoleContent = $("#role_content");
        const editContent = $("#edit_content");
        const editContentTextArea = $("#floatingEditTextarea");

        // Get the button that opens the modal
        const btnWrite = $("#write");
        const btnGiveRole = $("#give_Role");
        const btnModalEdit = $("#edit_button");
        // Get the <span> element that closes the modal
        const close = $(".close");

        // When the user clicks on the button, open the modal
        btnWrite.click(function() {
            modal.show();
            giveRoleContent.hide();
            editContent.hide();
            writeContent.show();
        })

        btnGiveRole.click(function() {
            modal.show();
            writeContent.hide();
            editContent.hide();
            giveRoleContent.show();
        })

        function editTextModal(text,id){
            modal.show();
            writeContent.hide();
            giveRoleContent.hide();
            editContentTextArea.val(text);
            editContent.show();
            btnModalEdit.off("click").on("click",(function (){
                    editText(editContentTextArea.val(),id);
                })
            )
        }

        // When the user clicks on <span> (x), close the modal
        close.click(function() {
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
