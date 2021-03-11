<!DOCTYPE html>
<html>
<head>
	<title>Multiple Image Upload</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
<div class="container">
<div class="row">
<div class="col-12">
	<h4>Multiple Image Upload</h4>
	<hr>
	<form method="post" enctype="multipart/form-data" action="file-upload.php">
		<div class="form-group">
			<label>Upload Image Here</label>
			<input type="file" name="image[]" class="form-control" multiple />
		</div>
		
		<input type="submit" name="submit" value="Submit" class="btn btn-primary">
	</form>
</div>
</div>
</div>

</body>
</html>