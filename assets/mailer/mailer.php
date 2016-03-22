<?php
			
require 'PHPMailerAutoload.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {

		
		$name = trim($_POST['name']);
    	$email = trim($_POST['email']);
		$message = trim($_POST['message']);
		$phone = trim($_POST['phone']);
		
			//Honey-Pot
		    // if "" or false this will be ignored don't need != "" for this.
		    if ($_POST["address"]){
		       throw new Exception("Oops! Nice try spamming.");
		       exit;
		     }

			// Check that data was sent to the mailer.
			if ( empty($name) || empty($email) || empty($phone) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
	            // Set a 400 (bad request) response code and exit.
	            http_response_code(400);
	            echo "Oops! There was a problem with your submission. Please complete the form and try again.";
	            exit;
	        }


		$email_content = "Name: $name\n";
        $email_content .= "Email: $email\n";
        $email_content .= "Phone: $phone\n\n";
        $email_content .= "Message: $message\n\n";

		$mail = new PHPMailer;

		//$mail->SMTPDebug = 3;  
		// Enable verbose debug output

		date_default_timezone_set('America/Denver');	

		// Set mailer to use SMTP
		$mail->isSMTP();            
		// Specify main and backup SMTP servers
		$mail->Host = 'smtp.gmail.com';  
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = 'hidden';          // SMTP username
		$mail->Password = 'hidden';					  // SMTP password
		$mail->Port = 465;	                           	      // SMTP port
		$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		
		// TCP port to connect to
		$mail->Port = 587;                                    

		// Add recipients
		$mail->addAddress('hidden');     
		//$mail->addCC('inquires@misterstitch.com');


		// Set email format to HTML		
		$mail->isHTML(false);  

		// Constructing email contet	
		$mail->Subject = "New message from MSE website.";
		$mail->Body    = $email_content;
			
			// Adding attachments with validation
			if (isset($_FILES['file']) && 
			    $_FILES['file']['error'] == UPLOAD_ERR_OK) {

				$file = $_FILES['file']['name'];
				$allowedext = array('jpg', 'png', 'gif', 'svg', 'pdf', 'doc', 'docx');
				$ext = pathinfo($file, PATHINFO_EXTENSION);

				if(in_array($ext, $allowedext) && $_FILES['file']['size'] <= 3145728){

					$mail->AddAttachment($_FILES['file']['tmp_name'],
                         $_FILES['file']['name']);			
				}
				else {
					http_response_code(403);
				    	echo " File type/size not accepted. Please attach a a file with the proper requirements.";
				    	return;
				}
			}	

	
		if(!$mail->send()) {
			http_response_code(500);
			   echo "Oops! Something went wrong, please try again";
		} else {
			http_response_code(200);
			   echo "Thanks! We'll give you a shout soon!";
		}

	}	

?>