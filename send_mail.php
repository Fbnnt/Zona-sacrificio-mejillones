<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = "fabian.vargas.molinaa@gmail.com";
    $from = $_POST['email'];
    $message = $_POST['message'];
    $subject = "Solicitud de Contacto";
    
    $headers = "From: " . $from . "\r\n";
    $headers .= "Reply-To: " . $from . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    $body = "<p><strong>Correo electrónico:</strong> $from</p>";
    $body .= "<p><strong>Mensaje:</strong></p>";
    $body .= "<p>$message</p>";
    
    // Handling file attachment
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_name = $_FILES['file']['name'];
        $file_size = $_FILES['file']['size'];
        $file_type = $_FILES['file']['type'];
        $file_error = $_FILES['file']['error'];
        
        $handle = fopen($file_tmp, "r");
        $content = fread($handle, $file_size);
        fclose($handle);
        $encoded_content = chunk_split(base64_encode($content));
        
        $boundary = md5("random"); // define boundary with a md5 hashed value
        
        $headers .= "MIME-Version: 1.0\r\n"; 
        $headers .= "Content-Type: multipart/mixed; boundary = $boundary\r\n\r\n"; 
        
        // plain text version of message
        $body = "--$boundary\r\n" . 
                "Content-Type: text/html; charset=UTF-8\r\n" . 
                "Content-Transfer-Encoding: base64\r\n\r\n" . 
                chunk_split(base64_encode($body)); 
        
        // attachment
        $body .= "--$boundary\r\n" .
                 "Content-Type: $file_type; name=\"$file_name\"\r\n" .
                 "Content-Disposition: attachment; filename=\"$file_name\"\r\n" .
                 "Content-Transfer-Encoding: base64\r\n" .
                 "X-Attachment-Id: " . rand(1000, 99999) . "\r\n\r\n" . 
                 $encoded_content . "\r\n\r\n" .
                 "--$boundary--";
    }
    
    if (mail($to, $subject, $body, $headers)) {
        echo "Correo enviado con éxito.";
    } else {
        echo "Error al enviar el correo.";
    }
}
?>
