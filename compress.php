<?php 

require('vendor/autoload.php');

$esc = list($usec, $sec) = explode(" ", microtime());
$script_start = (float) $sec + (float) $usec;

// https://github.com/Chumper/Zipper

// Tempo máximo de execução em segundos antes de lançar uma exception
set_time_limit(3600);

$zipper = new \Chumper\Zipper\Zipper;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Url
$path = 'img';

// Nome que será dado ao zip ex: arquivo.zip
$name = "dicom.zip";

// Nome da pasta dentro do zip
$name_file = 'dicom';

 // Function that calculates the size of a file/folder in bytes
function disk_usage($path) {
    if (!$path) return "";

    $size = 0;
    if (!is_dir($path))
        $size = filesize($path);
    else {
        $dir = opendir($path);
        while (false !== ($file = readdir($dir))) {
            if ($file != "." && $file != ".." && $file != ".htaccess") {
                $size += disk_usage($path."/".$file);
                unset($file);
            }
        }
        closedir($dir);
        unset($dir);
    }
   return $size;
 }

 // Function to calculate the size of a file/folder in the Computer Unit
 function fsize($path) {
    $size = disk_usage($path);
    if ($size == "") return "";

    $unit = array("B", "KB", "MB", "GB", "TB", "PB");
    $pos = 0;

    while ($size >= 1024) {
        $size /= 1024;
        $pos++;
    }

    $dd = strtoupper(str_replace('../', '', $path));
    return $dd . "  que contém " . round($size, 2)." ".$unit[$pos];
 }


function check($esc, $script_start) {
	$name = 'dicom.zip';
	list($usec, $sec) = explode(" ", microtime());
	$script_end = (float) $sec + (float) $usec;
	$elapsed_time = round($script_end - $script_start, 5);
	$hour = floor($elapsed_time / 3600);
	$minutes = floor(($elapsed_time - ($hour * 3600)) / 60);
	$seconds = floor($elapsed_time % 60);

	switch (true) {
		case ($seconds < 60):
			return  "<p class='success text-center'>" . $name . " Criado com sucesso em " . $seconds . " segundos";
		break;

		case ($minutes > 1):
			return  "<p class='success text-center'>" . $name . " Criado com sucesso em " . $minutes . " minutos e ". $seconds . " segundos";
		break;

		case ($hour > 1):
			return  "<p class='success text-center'>" . $name . " Criado com sucesso em " . $hour . " horas " . $minutes . " minutos e ". $seconds . " segundos";
		break;
	}
}


function sendMail() {
	$mail = new PHPMailer(true);
	try {
	    //Server settings
	    $mail->SMTPDebug = 0;                                 // Enable verbose debug output
	    $mail->CharSet = 'UTF-8';
	    $mail->isSMTP();                                      // Set mailer to use SMTP
	    $mail->Host = 'smtp.gmail.com';                 // Specify main and backup SMTP servers
	    $mail->SMTPAuth = true;                               // Enable SMTP authentication
	    $mail->Username = 'email@dominio.com';        // SMTP username
	    $mail->Password = 'senha';                         // SMTP password
	    $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
	    $mail->Port = 465;  


	    //Recipients
	    $mail->setFrom('email@dominio.com');
	    $mail->addAddress('recebo@recebi.com');     // Add a recipient

	    //Content
	    $mail->isHTML(true);                                  // Set email format to HTML
	    $mail->Subject = 'Título do e-mail';
	    $mail->Body    = "

	    Conteúdo do e-mail, pode utilizar html.

	    ";

	    //$mail->send();
	} catch (Exception $e) {
	    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
	}

}

try {

	switch(true){
				// Verifica se existe um zip
		 case (!file_exists($name) && isset($_POST['create'])):
		 	$zipper->zip($name)->folder($name_file)->add($path)->close();
			//echo " status: " .  $zipper->zip($name)->folder('')->getStatus() . "<br>";
			echo check($esc, $script_start	);
			//echo sendMail();
			echo "<script>alert('Zip concluído')</script>";
		 break;

		 	// Deleta um zip caso exista
		  case (file_exists($name) && isset($_POST['del'])):
		 	$zipper->zip($name)->folder('')->delete($name);
			echo "<p class='success text-center'> Deletado com sucesso " . $name . "</p>";
		 break;

		 	// Exibe um erro ao tentar fazer download de um arquivo que não existe
		  case (!file_exists($name) && isset($_POST['download'])):
		 	$zipper->zip($name)->folder('')->delete($name);
			echo "<p class='error text-center'> Nenhum arquivo encontrado. Por favor, crie um zip antes de tentar efetuar o download </p> ";
		 break;

		 	// Exibe um erro ao tentar excluir um arquivo que não existe
		 case (!file_exists($name) && isset($_POST['del'])):
		 	$zipper->zip($name)->folder('')->delete($name);
			echo "<p class='error text-center'> Não existe zip para ser excluído </p> ";
		 break;

		 		// Faz o download do zip caso exista
		 case (file_exists($name) && isset($_POST['download'])):
		 	ob_clean();
			ob_end_flush();
			header('Content-type: application/zip');
			header('Content-Disposition: attachment; filename="'.$name.'"');
			readfile($name);
			// Remove o arquivo após o download
			// unlink($name);
			echo "<p class='success text-center'> Arquivo deletado com sucesso </p>";
		 break;

		 case (file_exists($name)):
			echo "<p class='error text-center'> Já existe um diretório compactado. Faça download ou delete </p> ";
		 break;

		 case  (file_exists($path)):
		 	echo "<p class='alert text-center'> Você está prestes a compactar o diretório <strong>" . fsize($path) . "</strong> este processo pode demorar alguns minutos!</p>";
		break;

		case  (!file_exists($path)):
		 	echo "<p class='error text-center'> O diretório especificado como <strong> '$path' </strong> não existe. Por favor, verifique</p>";
		break;
}

} catch (Exception $e) {
	echo 'Ocorreu um erro ao compactar o diretório. Mensagem: <br><br>' . $e->getMessage();
}