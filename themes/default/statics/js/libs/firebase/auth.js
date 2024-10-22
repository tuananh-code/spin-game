$(document).ready(function () {

    // Make Popup window
    // Get the modal
    var modal = document.getElementById("myModal");
    var date = new Date();
    var words = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
    // var randomData = [];
    var randomKey = Math.floor(Math.random() * words.length)
    var randomWords = words[randomKey];
    var randomNumbers = Math.floor(Math.random() * date);
    var randomString = randomNumbers.toString();
    var randomIndex = Math.floor(Math.random() * randomString.length);
    var newString = randomString.slice(0, randomIndex) + randomWords + randomString.slice(randomIndex) + randomWords;
    console.log(newString);
    //delete_cookie('now');
    // Get the button that opens the modal
    // var btn = document.getElementById("myBtn");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // Login All by Js
    /**
     * @return {!Object} The FirebaseUI config.
     */


    function getUiConfig() {
        return {
            callbacks: {
                // Called when the user has been successfully signed in.
                'signInSuccess': function (user, credential, redirectUrl) {
                    handleSignedInUser(user); // Fix save.php run 2 time 
                    // Do not redirect.
                    // Put ajax in function return callback to add 1 data in in mysql
                    // console.log(newString);
                    var addData = {
                        name: user.displayName,
                        mail: user.email,
                        image: user.photoURL,
                        phone: user.phoneNumber,
                        uid: user['uid'],
                    }
                    $.ajax({
                        // How to add many data in Ajax
                        data: {
                            'add': addData,
                        },
                        type: "post",
                        url: "login.php",
                        // Add dataType to run or dont need
                        dataType: 'text',
                        success: function (dataResult) {
                            // First or second
                            showServer(user);
                            // var dataResult = JSON.parse(dataResult);
                            // Set dataResult == 200 instead of dataResult.statusCode == 200
                            // if (dataResult.statusCode == 200) {
                            if (dataResult == 200) {
                                // If put below code here when reload page login button will appear
                                // This code should be outside the Ajax and put below when you login success
                                // $('.login').html(`<div class="d-flex align-items-center img-cir">
                                //     <div><img class="circle me-2" src=" ${user.photoURL} "></div>
                                //     <div>
                                //     <p value="more" id="more" name="more"> ${user.displayName}</p>
                                //     </div>
                                //     </div>`);
                                var close = document.getElementById('close');
                                close.click();
                                // Hide Modal after Login
                                var hideModal = document.getElementById("myModal");
                                hideModal.style.display = "none";
                                if (getCookie("more") == "") {
                                    uuid = user.displayName;
                                    document.cookie = "more=" + uuid;
                                    document.getElementById("more").value = uuid;
                                } else {
                                    document.getElementById("more").value = getCookie("more");
                                }
                            } else if (dataResult == 201) {
                                alert(dataResult);
                            }
                        }
                    });
                    return false;
                }

            },

            // Opens IDP Providers sign-in flow in a popup.
            signInFlow: 'popup',
            signInOptions: [
                // The Provider you need for your app. We need the Phone Auth

                firebase.auth.FacebookAuthProvider.PROVIDER_ID,
                firebase.auth.GoogleAuthProvider.PROVIDER_ID,
                {
                    provider: firebase.auth.PhoneAuthProvider.PROVIDER_ID,
                    recaptchaParameters: {
                        //size: getRecaptchaMode()
                        type: 'image',
                        size: 'invisible',
                        badge: 'bottomleft'
                    }
                }
            ],
            // Terms of service url.
            'tosUrl': 'https://www.google.com'
        };
    }
    // var none = document.getElementsByClassName('fade');
    // none.style.display = 'none';
    function showServer(user) {
        var showUser = `
                        <button type="button" class=" btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalDatabase" >
                            <p>Show User as Guest</p>
                        </button>`
        console.log(user['uid']);
        if (user['uid'] === 'ynKhhrCK2GStzdXUwWSmdMSsbQk1') {
            $('#showUser').append(showUser);
        } else {
            console.log('guest');
        }
    }
    // Initialize the FirebaseUI Widget using Firebase.
    var ui = new firebaseui.auth.AuthUI(firebase.auth());

    /**
     * Displays the UI for a signed in user.
     * @param {!firebase.User} user
     */
    // Add data to MySQL by Ajax
    var handleSignedInUser = function (user) {
        document.getElementById('user-signed-in').style.display = 'block';
        document.getElementById('user-signed-out').style.display = 'none';
        document.getElementById('name').textContent = user.displayName;
        document.getElementById('mail').textContent = user.email;
        document.getElementById('phone').textContent = user.phoneNumber;
        // document.getElementById('dates').textContent = user.
        if (user.photoURL) {
            document.getElementById('image').src = user.photoURL;
            document.getElementById('image').style.display = 'block';
        } else {
            document.getElementById('image').style.display = 'none';
        }

        var logintoall = [
            user.displayName,
            user.email,
            user.photoURL,
            user.phoneNumber,
            // Add a value array to save many people
            'saveperson'
        ];
        //     // console.log(logintoall)
        //     // console.log('da vao')

    };


    /**
     * Displays the UI for a signed out user.
     */
    var handleSignedOutUser = function () {
        document.getElementById('user-signed-in').style.display = 'none';
        document.getElementById('user-signed-out').style.display = 'block';
        ui.start('#firebaseui-container', getUiConfig());
    };

    // Listen to change in auth state so it displays the correct UI for when
    // the user is signed in or not.
    firebase.auth().onAuthStateChanged(function (user) {
        document.getElementById('loading').style.display = 'none';
        document.getElementById('loaded').style.display = 'block';
        user ? handleSignedInUser(user) : handleSignedOutUser();
        // Show data in HTML, Code in Ajax should appear here
    //     $('.login').html(`<div class="d-flex align-items-center img-cir hov">
    // <div><img class="circle me-2" src=" ${user.photoURL} "></div>
    // <div><p value="more" id="more" name="more"> ${user.displayName}</p></div>
    // </div>`);
    });

    /**
     * Deletes the user's account.
     */
    var deleteAccount = function () {
        firebase.auth().currentUser.delete().catch(function (error) {
            if (error.code == 'auth/requires-recent-login') {
                // The user's credential is too old. She needs to sign in again.
                firebase.auth().signOut().then(function () {
                    // The timeout allows the message to be displayed after the UI has
                    // changed to the signed out state.
                    setTimeout(function () {
                        alert('Please sign in again to delete your account.');
                    }, 1);
                });
            }
        });
    };
    /**
     * Initializes the app.
     */
    var initApp = function () {
        // console.log(initApp);
        document.getElementById('sign-out').addEventListener('click', function () {
            firebase.auth().signOut();
            // Delele cookie in js
            var delete_cookie = function (name) {
                document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            };
            // console.log(delete_cookie);
            delete_cookie('more');

        });
        document.getElementById('delete-account').addEventListener(
            'click',
            function () {
                deleteAccount();
            });
    };

    window.addEventListener('load', initApp);

    /**
     * Edit Role
     */

    $('.editRole').click(function () {
        var id = $(this).attr('data-id');
        var idRole = $(this).attr('data-role');
        var editRole = $(this);
        var role = $(this).closest('tr').find('.role');
        var save = $(this).closest('div');
        var pRole = role.find('p');
        var textRole = pRole.text();
        var selectRole = role.find('select');
        var saveButton = `<button class="saveRole btn btn-success" type="button" data-id="${id}">Lưu</button>`;
        if (selectRole.length > 0) {
            selectRole.val(textRole);
        } else {
            if (textRole == 'member') {
                var changeToMember =
                    `<select class="form-select" aria-label="Default select example">
            <option selected value='2'>${textRole}</option>
            <option value='3'>guest</option>
            </select>`;
                role.append(changeToMember);
            } if (textRole == 'guest') {
                var changeToGuest =
                    `<select class="form-select" aria-label="Default select example">
        <option selected value='3'>${textRole}</option>
        <option value='2'>member</option>
        </select>`;
                role.append(changeToGuest);
            }
            $(save).append(saveButton);
            $(this).css({
                'display': 'none',
            });
            $(pRole).remove();
        };
        // Stop .saveRole event click become multiple
        $('.saveRole').off('click');

        $('.saveRole').click(function () {
            var valueRole = $(this).closest('tr').find('.form-select').val();
            var roleForm = $(this).closest('tr').find('.form-select');
            var saveRole = $(this);
            console.log(textRole);
            console.log(valueRole);
            $.ajax({
                url: 'login.php',
                type: 'post',
                // dataType: 'text'
                data: {
                    id: id,
                    'update': valueRole,
                },
                success: function (response) {
                    console.log(response);
                    if (valueRole == 2) {
                        var member = `<p> member </p>`;
                        role.append(member);
                    } if (valueRole == 3) {
                        var guest = `<p> guest </p>`;
                        role.append(guest);
                    }
                    console.log(response);
                    var updateRole = `<p> ${response} </p>`
                    $(saveRole).css({
                        'display': 'none',
                    })
                    $(editRole).css({
                        'display': 'unset'
                    })
                    $(roleForm).css({
                        'display': 'none',
                    })

                }
            });
        });

        $('#saveRole').click(function () {
            location.reload();
            // Reload only an table on pages
            // 1st way
            // $("#userServer").load("http://localhost:8888/ChatGPT/ #userServer");
            //2nd way
            // $(".userServer").load(location.href + " .userServer")
            // console.log('davao');
        })

        // $('#saveRole').click(function () {
        //     var roles = {};
        //     // Loop through each row in the table
        //     $('tbody tr').each(function () {
        //         var selectRoleOption = $(this).find('select');
        //         // If a <select> element exists in the row, add its value to the roles object
        //         if (selectRoleOption.length > 0) {
        //             roles[id] = selectRoleOption.val();
        //         }
        //     });
        // Print the roles object (for testing purposes)
        // console.log(roles[id]);

        // });
    });
});