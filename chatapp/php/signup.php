<?php
    session_start();
    include_once "config.php";
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    // required to fill all
    if(!empty($fname) && !empty($lname) && !empty($email) && !empty($password)){
           // check for a valid mail
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){  //if email is valid
              // check if email already exist in database or not
            $sql = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
            if(mysqli_num_rows($sql) > 0){  //if email already exist
                echo "$email - This email already exist!";
            }else{// check user uploaded image or not?!
                if(isset($_FILES['image'])){  // image is uploaded
                    $img_name = $_FILES['image']['name']; // to get user uploaded image name
                    $img_type = $_FILES['image']['type']; // to get user uploaded image type
                    $tmp_name = $_FILES['image']['tmp_name']; // this temporary name is used to save file in our folder
                    //exploding image and get the last extension type like jpd png
                    $img_explode = explode('.',$img_name);
                    $img_ext = end($img_explode); // here we get the extension of anuser uploaded image file
    
                    $extensions = ["jpeg", "png", "jpg"]; // these are the valid img extension stored in array
                    if(in_array($img_ext, $extensions) === true){ // if user uploaded img ext is matched with any valid ext
                        $types = ["image/jpeg", "image/jpg", "image/png"];
                        if(in_array($img_type, $types) === true){
                            $time = time(); //this will return us current time..
                                     // we need this tie beacuse when u uploading user img in our folder we rename user file
                                     // with current time so all the img file will have a unique name
                            $new_img_name = $time.$img_name;
                            // move uploaded image to our folder
                            if(move_uploaded_file($tmp_name,"images/".$new_img_name)){//if img transferre succesfully
                                $ran_id = rand(time(), 100000000); //  creating random id for user
                                $status = "Active now"; // once a user signed up then his stauts will be active
                                $encrypt_pass = md5($password);
                                // insert all user data into table
                                $insert_query = mysqli_query($conn, "INSERT INTO users (unique_id, fname, lname, email, password, img, status)
                                VALUES ({$ran_id}, '{$fname}','{$lname}', '{$email}', '{$encrypt_pass}', '{$new_img_name}', '{$status}')");
                                if($insert_query){//if required data inserted
                                    $select_sql2 = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
                                    if(mysqli_num_rows($select_sql2) > 0){
                                        $result = mysqli_fetch_assoc($select_sql2);
                                        $_SESSION['unique_id'] = $result['unique_id'];//using this session we need user unique id in other php file
                                        echo "success";
                                    }else{
                                        echo "This email address not Exist!";
                                    }
                                }else{
                                    echo "Something went wrong. Please try again!";
                                }
                            }
                        }else{
                            echo "Please upload an image file - jpeg, png, jpg";
                        }
                    }else{
                        echo "Please upload an image file - jpeg, png, jpg";
                    }
                }
            }
        }else{
            echo "$email is not a valid email!";
        }
    }else{
        echo "All input fields are required!";
    }
?>