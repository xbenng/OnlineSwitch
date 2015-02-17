<html>
<title>Arduino LCD</title>

<?php

$message = "put message here";

//initialize serial port
error_reporting(E_ALL);
ini_set('display_errors', '1');
include "php_serial.class.php";

$serial = new phpSerial;
$serial->deviceSet("/dev/ttyUSB0");
$serial->confBaudRate(115200);
$serial->confParity("none");
$serial->confCharacterLength(8);
$serial->confStopBits(1);

//initialize log file
$log = fopen("lcdmessagelog.txt", "a");

//if submit button pressed
if(isset($_POST['Submit'])){
$serial->deviceOpen();
$serial->sendMessage("AT+display");
$response = $serial->readPort();
echo "LCD Was Displaying: $response <br />";
usleep(10000);  //10 milliseconds, in microseconds
//$tosend = $_POST['message'];
$message = "AT+message=".$_POST['message'];
$serial->sendMessage($message);
usleep(10000);  //10 milliseconds, in microseconds
$serial->sendMessage("AT+display");
$response = $serial->readPort();
$serial->deviceClose();
echo "LCD Changed to: $response <br />";
fwrite($log, time().", ".$_SERVER['REMOTE_ADDR'].", $message\n");
}
if(isset($_POST['ledOn'])){
$serial->deviceOpen();
$serial->sendMessage("AT+led=?");
$ledstate = $serial->readPort();
usleep(10000);  //10 milliseconds, in microseconds
  if($ledstate!=1){
    $serial->sendMessage("AT+led=13H");
  } else {
     $serial->sendMessage("AT+led=13L ");
  }
$serial->deviceClose();
}
if(isset($_POST['display'])){
$serial->deviceOpen();
$serial->sendMessage("AT+display");
$response = $serial->readPort();
$serial->deviceClose();
echo "LCD Is Displaying: $response <br />";
}

//request ledstate
$serial->deviceOpen();
$serial->sendMessage("AT+led=?");
$ledstate = $serial->readPort();
$serial->deviceClose();
if($ledstate==0){echo "Lights Are On";}else{echo "Lights Are Off";}
fclose($log);
?>

<form action="lcd.php" method="POST">
<INPUT TYPE = "Text" NAME = "message" VALUE = "<?php echo $_POST['message']; ?>">
<INPUT TYPE = "Submit" NAME = "Submit" VALUE = "Submit Text">
<INPUT TYPE = "Submit" NAME = "display" VALUE = "Check Display">
<INPUT TYPE = "Submit" NAME = "ledOn" VALUE = "<?php if($ledstate==0){echo "Turn Lights Off";}else{echo "Turn Lights On";}?>">
</form>
<img src="https://i.groupme.com/640x854.jpeg.b96df9d12b5045cfa2a63adc3ebf6912.large" alt="a picture of harrison's dick">

</html>
