<a href="index.php">Home</a>
<br>
<?php
require 'vendor/autoload.php';
$args = [
    'credentials' => [
        'key' => 'AKIHA4MNYJAK2KTGWURLDVE',
        'secret' => 'ERjTPHGN1jcsfnlwd9zM7DbydDwnxD04kD8r'
    ],
    'region' => 'ap-south-1',
    'version' => 'latest'
];
$client = new Aws\Rekognition\RekognitionClient($args);
//This function separates the extension from the rest of the file name and returns it
function findexts ($filename)
{
    $filename = strtolower($filename) ;
    $exts = explode(".", $filename) ;
    $n = count($exts)-1;
    $exts = $exts[$n];
    return $exts;
}
//This applies the function to our file
$ext = findexts ($_FILES['fileToUpload']['name']) ; 
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        // echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
}
// Check if file already exists
if (file_exists($target_file)) {
    echo "Sorry, file already exists.";
    $uploadOk = 0;
}
// Check file size
if ($_FILES["fileToUpload"]["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
$uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
} else {
    //This line assigns a random number to a variable. You could also use a timestamp here if you prefer.
    $ran = rand () ;
    //This takes the random number (or timestamp) you generated and adds a . on the end, so it is ready for the file extension to be appended.
    $ran2 = $ran.".";
    //This assigns the subdirectory you want to save into... make sure it exists!
    $target = "uploads/";
    //This combines the directory, the random file name and the extension 
    $target = $target . $ran2.$ext;
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target)) {
        // echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
        echo "<img src='". $target ."' style='width: 100px;'>";
        $result = $client->detectFaces([
            'Image' => [  
                'Bytes' => file_get_contents($target),
            ],
            'Attributes' => ['ALL']
        ]);
        echo "<pre>";
        print_r ($result);
        echo "</pre>";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>

