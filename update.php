<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$nim = $nama = $email = $no_hp = "";
$nim_err = $nama_err = $email_err = $no_hp_err = "";
 
// Processing form data when form is submitted
if(isset($_POST["id"]) && !empty($_POST["id"])){
    // Get hidden input value
    $id = $_POST["id"];
    
     // Validate nim
     $input_nim = trim($_POST["nim"]);
     if(empty($input_nim)){
         $nim_err = "Please enter an address.";     
     } else{
         $nim = $input_nim;
     }

    // Validate nama
    $input_nama = trim($_POST["nama"]);
    if(empty($input_nama)){
        $nama_err = "Masukan nama anda.";
    } elseif(!filter_var($input_nama, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $nama_err = "Masukan nama yang Valid.";
    } else{
        $nama = $input_nama;
    }
    
    // Validate email
    $input_email = trim($_POST["email"]);
    if(empty($input_email)){
        $email_err = "Masukan E-mail anda.";     
    } else{
        $email = $input_email;
    }
    
    // Validate nomor handphone
    $input_no_hp = trim($_POST["no_hp"]);
    if(empty($input_no_hp)){
        $no_hp_err = "Masukan No HP anda.";     
    } else{
        $no_hp = $input_no_hp;
    }
    
    // Check input errors before inserting in database
    if(empty($nim_err) && empty($nama_err) && empty($email_err) && empty($no_hp_err)){
        // Prepare an update statement
        $sql = "UPDATE utsku2 SET nim=?, nama=?, email=?, no_hp=? WHERE id=?";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssi",$param_nim, $param_nama, $param_email, $param_no_hp, $param_id);
            
            // Set parameters
            $param_nim = $nim;
            $param_nama = $nama;
            $param_email = $email;
            $param_no_hp = $no_hp;
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records updated successfully. Redirect to landing page
                header("location: index.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
} else{
    // Check existence of id parameter before processing further
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        // Get URL parameter
        $id =  trim($_GET["id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM utsku2 WHERE id = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_id);
            
            // Set parameters
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
    
                if(mysqli_num_rows($result) == 1){
                    /* Fetch result row as an associative array. Since the result set
                    contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
                    $nim = $row["nim"];
                    $nama = $row["nama"];
                    $email = $row["email"];
                    $no_hp = $row["no_hp"];
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
        
        // Close connection
        mysqli_close($link);
    }  else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Update Record</h2>
                    <p>Please edit the input values and submit to update the employee record.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group">
                            <label>NIM</label>
                            <input type="text" name="nim" class="form-control <?php echo (!empty($nim_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $nim; ?>">
                            <span class="invalid-feedback"><?php echo $nim_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="nama" class="form-control <?php echo (!empty($nama_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $nama; ?>">
                            <span class="invalid-feedback"><?php echo $nama_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <textarea name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>"><?php echo $email; ?></textarea>
                            <span class="invalid-feedback"><?php echo $email_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Nomor Handphone</label>
                            <input type="text" name="no_hp" class="form-control <?php echo (!empty($no_hp_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $no_hp; ?>">
                            <span class="invalid-feedback"><?php echo $no_hp_err;?></span>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-secondary ml-2">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>