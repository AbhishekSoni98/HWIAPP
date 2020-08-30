<?php
$url = 'https://e2dc2bb84d4c.ngrok.io/App/';

session_start();
require_once "pdo.php";

function getfaceid($url){
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://faceverify2.cognitiveservices.azure.com/face/v1.0/detect?returnFaceId=true&returnFaceLandmarks=false&recognitionModel=recognition_01&returnRecognitionModel=false&detectionModel=detection_01",
  CURLOPT_RETURNTRANSFER => 1,
  CURLOPT_ENCODING => "",
  CURLOPT_TIMEOUT => 100,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS =>"{\n    \"url\": \"$url\"\n}",
  CURLOPT_HTTPHEADER => array(
    "Ocp-Apim-Subscription-Key: 8adae7df503446028b2d092d6bcb0c26",
    "Content-Type: application/json"
  ),
));
try {
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
$response = curl_exec($curl);
}
catch(Exception $e) {
	var_dump('HERERERE');
	var_dump($e);
}
curl_close($curl);
var_dump($url);
var_dump($response);
return $response;
}


function verifyid($fid,$vid)
{
	$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://faceverify2.cognitiveservices.azure.com/face/v1.0/verify",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 100,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS =>"{\n    \"faceId1\": \"$fid\",\n    \"faceId2\": \"$vid\"\n}",
  CURLOPT_HTTPHEADER => array(
    "Ocp-Apim-Subscription-Key: 8adae7df503446028b2d092d6bcb0c26",
    "Content-Type: application/json"
  ),
));
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
$response = curl_exec($curl);

curl_close($curl);
return $response;
}



$pid = $_SESSION['pid'];
unset($_SESSION['pid']);
$imgData = str_replace(' ','+',$_POST['image']);
$imgData =  substr($imgData,strpos($imgData,",")+1);
$imgData = base64_decode($imgData);
// Path where the image is going to be saved
$filePath = "verification/$pid.png";
// Write $imgData into the image file
$file = fopen($filePath, 'w');
fwrite($file, $imgData);
fclose($file);
$stmt = $pdo->prepare('update prescription set ver_id=:vid where ID=:pid');
$stmt->execute(array(':pid' => $pid, ':vid' => $filePath));
if (isset($_SESSION['name']) && isset($_SESSION['user_id']) )
  {
$stmt = $pdo->prepare('select face_id from user_info where user_id=:uid');
$stmt->execute(array(':uid' => $_SESSION['user_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row !== false)
	{
		$fid = $row['face_id'];
		$url1 = $url.$fid;
		$url2 = $url.$filePath;
		$face_id = json_decode(getfaceid($url1), true);
		$ver_id = json_decode(getfaceid($url2), true);
		var_dump($url1,$url2);
		var_dump($face_id);
		var_dump($ver_id);
		try 
		{
		$fid = $face_id[0]['faceId'];
		$vid = $ver_id[0]['faceId'];
		if (!($fid and $vid))
		{
			throw new Exception("Face Missing");
		}
		$finalresult = json_decode(verifyid($fid,$vid), true);
		var_dump($finalresult);
		if ($finalresult['isIdentical'] === true)
		{
			$conf = $finalresult['confidence'];
			$status = 'Approved';
		$stmt = $pdo->prepare('update prescription set status=:st,conf=:conf where ID=:pid');
		$stmt->execute(array(':pid' => $pid, ':st' => $status,':conf' => $conf));
		$_SESSION['success'] = 'Prescription Confirmed';
		}
		else
		{
			$conf = $finalresult['confidence'];
			$status = 'Rejected';
		$stmt = $pdo->prepare('update prescription set status=:st,conf=:conf where ID=:pid');
		$stmt->execute(array(':pid' => $pid, ':st' => $status,':conf' => $conf));
		$_SESSION['error'] = 'Prescription Rejected';
		}
		header('Location: index.php');
		return;
		}
		catch (Exception $e)
		{
			$conf = '0';
			$status = 'Rejected';
		$stmt = $pdo->prepare('update prescription set status=:st,conf=:conf where ID=:pid');
		$stmt->execute(array(':pid' => $pid, ':st' => $status,':conf' => $conf));
			$_SESSION['error'] = 'Clear Face Not Found';
			header('Location: index.php');
			return;
		}
	}
else
{
	$stmt = $pdo->prepare('update prescription set status=:st where ID=:pid');
$stmt->execute(array(':pid' => $pid, ':st' => 'Rejected'));
header('Location: index.php');
			return;
}
  }
  else
  {
	$stmt = $pdo->prepare('update prescription set status=:st where ID=:pid');
$stmt->execute(array(':pid' => $pid, ':st' => 'Rejected'));  
header('Location: index.php');
			return;
  }
?>