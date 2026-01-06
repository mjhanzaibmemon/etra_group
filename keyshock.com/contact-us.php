<?php

include('header.php');



      function rand_string( $length ) {
                $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";  

                $size = strlen( $chars );
                    for( $i = 0; $i < $length; $i++ ) {
                        $str .= $chars[ rand( 0, $size - 1 ) ];
                    }

                    return $str;
                }




if(!empty($_POST['submit'])) {
 



/////////////////////////////////

           //check whether a form was submitted
            if(isset($_FILES['theFile']['name'])){

                //include the S3 class
                if (!class_exists('S3'))require_once('S3.php');
                
        
                //retreive post variables
                $fileName = $_FILES['theFile']['name']; 
                $fileTempName = $_FILES['theFile']['tmp_name'];
                
                $fileName = rand_string(5).str_replace(' ','-',$fileName);
 
                //move the file
                if ($s3->putObjectFile($fileTempName, "keyshock", $fileName, S3::ACL_PUBLIC_READ)) {
                   // echo "<strong>We successfully uploaded your file.</strong>";
                }else{
                    //echo "<strong>Something went wrong while uploading your file... Please notify us.</strong>";
                    $fileName = 'Error File Uploading';
                }
            }

/////////////////////////////////


// EDIT THE 2 LINES BELOW AS REQUIRED
$email_to = "info@keyshock.com";
$email_subject = "Keys Hock Support Form";
     
    // validation expected data exists
    if(!isset($_POST['first_name']) ||
        !isset($_POST['contact_number']) ||
        !isset($_POST['email']) ||
        !isset($_POST['comments'])) {
       // died('We are sorry, but there appears to be a problem with the form you submitted.');       
    }
     
    $first_name = $_POST['first_name']; // required
    $contact_number = $_POST['contact_number']; // required
    $profession = $_POST['profession']; // required
    $email_from = $_POST['email']; // required
    $comments = $_POST['comments']; // required
     
    $error_message = "";

    $email_message = "Form details below.\n\n";

    function clean_string($string) {
      $bad = array("content-type","bcc:","to:","cc:","href");
      return str_replace($bad,"",$string);
    }
     
    $email_message .= "First Name: ".clean_string($first_name)."\n\n";
    $email_message .= "Profession: ".clean_string($profession)."\n\n";
    $email_message .= "Contact Number: ".clean_string($contact_number)."\n\n";
    $email_message .= "Email address: ".clean_string($email_from)."\n\n";
    $email_message .= "CV filename: ".clean_string($fileName)."\n\n";
    $email_message .= "Message: ".clean_string($comments)."\n\n";
     
     
// create email headers
$headers = "From: info@keyshock.com\r\n".
'Reply-To: '.$email_from."\r\n" .
'X-Mailer: PHP/' . phpversion();

if($failed!==1){   

$emailsuccess = '<div class="emailsuccess">Thank you for contacting our support team. A member of our team will get back to within 1 working day.</div>';

}

else{

}

}








?>
<!DOCTYPE html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">	
	<title>Contact our Team - Keyshock</title>
	<link href="https://fonts.googleapis.com/css?family=PT+Sans" rel="stylesheet">
	<link href="stylenew.css" rel="stylesheet">
	<style>
		.layoutboth {
			width: 50%;
			box-sizing: border-box;
			-moz-box-sizing: border-box;
			float: left;
			margin-top: 50px;
			margin-bottom: 40px;
			text-align: left;
			overflow: hidden;
		}

		.contactusform {
			margin-top: 30px;
			width: 100%;
		}

		.contactusform input,
		textarea {
			padding: 10px;
			width: 100%;
			border-radius: 3px;
			outline: 0;
			border: 1px solid grey;
			font-family: 'PT sans';
			box-sizing: border-box;
			-moz-box-sizing: border-box;
		}

		.contactusform input:focus,
		textarea:focus {
			border-color: #003d83;
		}

		.contactusform tr td {
			padding-bottom: 15px;
		}

		.greenbtn {
			font-family: 'PT SANS';
			height: 44px;
			line-height: 39px;
			display: inline-block;
			border-radius: 3px;
			border: 1px solid #aeaeae;
			font-weight: bold;
			margin-top: 20px;
			text-shadow: none;
			background: #ffffff;
			background: -moz-linear-gradient(top, #ffffff 0%, #ededed 100%);
			background: -webkit-linear-gradient(top, #ffffff 0%, #ededed 100%);
			background: linear-gradient(to bottom, #ffffff 0%, #ededed 100%);
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#ededed', GradientType=0);
			text-decoration: none;
			text-align: center;
			margin-bottom: 40px;
			border: 1px solid #438625;
			color: white;
			background: #68c43e;
			background: -moz-linear-gradient(top, #68c43e 0%, #408223 100%);
			background: -webkit-linear-gradient(top, #68c43e 0%, #408223 100%);
			background: linear-gradient(to bottom, #68c43e 0%, #408223 100%);
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#68c43e', endColorstr='#408223', GradientType=0);
			color: white;
			font-size: 18px;
			width: 206px;
		}

		.greenbtn:hover {
			cursor: pointer;
		}

		.cnwidth {
			display: table;
		}

		.emailsuccess {
			padding: 10px;
			border: 1px solid green;
			margin-top: 20px;
			background-color: #d8ffd8;
		}
		
		.mail{color:#003d83;}

		.emailfailed{background-color: #ffd8d8;border: 1px solid maroon;}
		@media screen and (max-width:850px){.layoutboth{width:100%;}}
	</style>
</head>

<body>



	<?=$header ?>


		<div style="padding: 15px 0 35px 0" align="center">


			<div class="cnwidth">

				<h1>Contact Our CV Experts Today</h1>

				<?=$emailsuccess ?>

					<div class="layoutboth" style="padding-right:75px;"><b>Our expert's Confidentiality Guarantee:</b><br> We take privacy very seriously and guarantee total non-disclosure of your information. Enquiries made and CVs/documents sent to us are kept in 100% confidentiality. We will never share client information to a third party without the consent of the client or a clear legal reason. You will never receive spam or marketing emails from us.

						<br><br> 
						Support Team: <font class="mail">support-team@keyshock.com</font><br>
						Free CV Evaluation Team: <font class="mail">evaluation-team@keyshock.com</font><br>
						Payments Team: <font class="mail">payments@keyshock.com</font><br>
						Our expert writers: <font class="mail">writer@keyshock.com</font>
					</div>


					<div class="layoutboth"><b>Confidential Quick Reply Form</b><br><i>Hear back from us within one working day</i>

						<form action="" method="POST" enctype="multipart/form-data" name="form1" id="form1">
							<table class="contactusform">
								<tr>
									<td>Name:*<br><input name="first_name" value="<?=$first_name?>"></td>
								</tr>
								<tr>
									<td>Profession:*<br><input name="profession" value="<?=$profession?>"></td>
								</tr>
								<tr>
									<td>Email:*<br><input name="email" value="<?=$email_from?>"></td>
								</tr>
								<tr>
									<td>Contact Number:<br><input name="contact_number" value="<?=$contact_number?>"></td>
								</tr>
								<tr>
									<td>Question or outline of target role:*<br><textarea name="comments" style="height:200px;"><?=$comments?></textarea></td>
								</tr>
								<tr>
									<td>Upload CV:<br><input name="theFile" type="file" /></tr>
								<tr>
								<tr>
									<td>We know you're not a robot, but sometimes robots try sending us emails!
										<br>Are you a robot?<div class="g-recaptcha" data-sitekey="6LdZobgUAAAAAKR5-sKoFZigeOzz7ADu_tZUk2o-"></div></td>
								</tr>
									<td><input type="submit" class="greenbtn" style="padding: 0;width: 206px;border: 1px solid #438625;" name="submit" value="Send Message"></tr>
							</table>
						</form>
					</div>


			</div>

		</div>





		<?=$footer ?>


<script src="https://www.google.com/recaptcha/api.js" async defer></script>


</body>

</html>
