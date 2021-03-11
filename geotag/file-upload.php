<?php
include 'database.php';


/*------Function for getting the GPS data---------*/

function get_image_location($image = ''){
    $exif = exif_read_data($image, 0, true);
    if($exif && isset($exif['GPS'])){
        $GPSLatitudeRef = $exif['GPS']['GPSLatitudeRef'];
        $GPSLatitude    = $exif['GPS']['GPSLatitude'];
        $GPSLongitudeRef= $exif['GPS']['GPSLongitudeRef'];
        $GPSLongitude   = $exif['GPS']['GPSLongitude'];
        
        $lat_degrees = count($GPSLatitude) > 0 ? gps2Num($GPSLatitude[0]) : 0;
        $lat_minutes = count($GPSLatitude) > 1 ? gps2Num($GPSLatitude[1]) : 0;
        $lat_seconds = count($GPSLatitude) > 2 ? gps2Num($GPSLatitude[2]) : 0;
        
        $lon_degrees = count($GPSLongitude) > 0 ? gps2Num($GPSLongitude[0]) : 0;
        $lon_minutes = count($GPSLongitude) > 1 ? gps2Num($GPSLongitude[1]) : 0;
        $lon_seconds = count($GPSLongitude) > 2 ? gps2Num($GPSLongitude[2]) : 0;
        
        $lat_direction = ($GPSLatitudeRef == 'W' or $GPSLatitudeRef == 'S') ? -1 : 1;
        $lon_direction = ($GPSLongitudeRef == 'W' or $GPSLongitudeRef == 'S') ? -1 : 1;
        
        $latitude = $lat_direction * ($lat_degrees + ($lat_minutes / 60) + ($lat_seconds / (60*60)));
        $longitude = $lon_direction * ($lon_degrees + ($lon_minutes / 60) + ($lon_seconds / (60*60)));

        return array('latitude'=>$latitude, 'longitude'=>$longitude);
    }else{
        return false;
    }
}

/*----------Function for Converting GPS data into suitable format---------------*/


function gps2Num($coordPart){
    $parts = explode('/', $coordPart);
    if(count($parts) <= 0)
    return 0;
    if(count($parts) == 1)
    return $parts[0];
    return floatval($parts[0]) / floatval($parts[1]);
}



/*-------- This portion of code executes after hitting Submit Button -----------*/

if(isset($_POST['submit']))
{
	$extension=array('jpeg','jpg','png','gif');

	foreach ($_FILES['image']['tmp_name'] as $key => $value) {
		$filename=$_FILES['image']['name'][$key];
		$filename_tmp=$_FILES['image']['tmp_name'][$key];
		echo '<br>';
		$ext=pathinfo($filename,PATHINFO_EXTENSION);

		$finalimg='';
		if(in_array($ext,$extension))
		{
			if(!file_exists('images/'.$filename))      //Testing if the file exists in the directory
			{
			move_uploaded_file($filename_tmp, 'images/'.$filename);
			$finalimg=$filename;
			}else
			{
				 $filename=str_replace('.','-',basename($filename,$ext));
				 $newfilename=$filename.time().".".$ext;
				 move_uploaded_file($filename_tmp, 'images/'.$newfilename);
				 $finalimg=$newfilename;
			}
			$creattime=date('Y-m-d h:i:s');


			// Getting the location information from the image file

			$imageURL = fopen('images/'.$filename, 'r+');

			$imgLocation = get_image_location($imageURL);


			//latitude & longitude
			if(!empty($imgLocation)){
			$imgLat = $imgLocation['latitude'];
			$imgLng = $imgLocation['longitude'];
			echo '<p>Latitude : ' . $imgLat . '|| Lognitude: ' .$imgLng. '</p>';

			}else{ echo "Geotag Not found";}
			
			
			// Passing the Geo data to Mysql server
			
			//insert
			$insertqry="INSERT INTO `multiple-images`(`image_name`, `image_createtime`,'lat','lon') VALUES ('$finalimg','$creattime','$imgLat','$imgLng')";
			mysqli_query($con,$insertqry);

			if (!$mysqli -> query){
				echo ("Error description: " . mysqli_error($con));
			}
			

			#header('Location: index.php');
		}else
		{
			echo ("Error : " . mysqli_error($con));
		}


	}
}
?>


<!-- Interface part for the map. 

#This can only show on location at a time. 
#Couldn't fix the thumbnail for the location pin point

-- >


<!DOCTYPE html>
<html lang="en-US">
<head>
	<title>Geolocation Tags on Google MAP</title>
	<meta charset="utf-8">

<style>
	
#map{
    width: 100%;
    height: 400px;
}
</style>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCy6sWjyjbM4LB4OZj4vyurqs8kW3IUDvM"></script>



<script>
var myCenter = new google.maps.LatLng(<?php echo $imgLat; ?>, <?php echo $imgLng; ?>);
function initialize(){
    var mapProp = {
        center:myCenter,
        zoom:10,
        mapTypeId:google.maps.MapTypeId.ROADMAP
    };

    var map = new google.maps.Map(document.getElementById("map"),mapProp);

    var marker = new google.maps.Marker({
        position:myCenter,
        animation:google.maps.Animation.BOUNCE
    });

    marker.setMap(map);
}
google.maps.event.addDomListener(window, 'load', initialize);
</script>

</head>
<body>
	<div id="map"></div>
</body>
</html>



