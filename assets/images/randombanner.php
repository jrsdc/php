<?php
/*
	Open the banners directory and find all files inside it

*/
$files = [];
foreach (new DirectoryIterator('./banners') as $file) {
	/*
		ignore:
			.   - current directory
			..  - directory above
	*/
	if ($file->isDot()) {
		continue;
	}

	/*
		If a file doesn't end .jpg, it's not an image
		so ignore and move on to the next file
	*/
	if (!strpos($file->getFileName(), '.jpg')) {
		continue;
	}


	/*
		If the script got this far, add the name of
		the image to the $files array
	*/
	$files[] = $file->getFileName();
}



/*

	Generate a random number between 0 and the number of files
	found in the directory
*/
$randomNumber = rand(0,count($files)-1);

/*
	Read the contents of the array based on
	the randomly generated number.
	This will pick one element out of the array at random
*/

$file = $files[$randomNumber];


/*
	Before sending the image data to the browser, send some header
	Firstly: Tell the browser we're going to be sending it an image:
*/

header('content-type: image/jpeg');

/*
	Tell the browser todisable caching,
	otherwise the browser will cache the image
	and the same (cached) image will be displayed every time
*/

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

/*
	Load the image data into the $contents variable
	so it can be printed later on
*/
$contents = load_file('./banners/' . $file);

/*
	Tell the browser how big (in bytes) the image is
*/
header('content-length: ' . strlen($contents));

/*
	Finally, send the image data to the browser
*/
echo $contents;

/*
	Function to load the contents of a file into a variable
	@param $name 	the name of the file to load
*/
function load_file($name) {
	/*
		Open the buffer to save the contents to
	*/
	ob_start();

	/*
		Load the file's contents
	*/
	echo file_get_contents($name);
	/*
		Read the buffer
	*/
	$contents = ob_get_clean();
	/*
		return the conents of the buffer
	*/
	return $contents;
}
