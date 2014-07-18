<h1>File upload example</h1>
<form method="post" action="" enctype="multipart/form-data">
  <input type="file" name="upload" class="file" />
  <input type="submit" value="Upload" />
</form>


<?php

/**
 * Created by PhpStorm.
 * User: Marcel van Doornen
 * Date: 18-7-14
 * Time: 14:04
 * Version 1.0
 */

// Check if file exists
if (!empty($_FILES)) {

  // Include the file upload class
  include_once('fileupload.class.php');

  // Init class with tha file as param (required)
  $upload = new fileupload($_FILES['upload']);

  // Set allowed file extension(s), if not set all extensions are allowed
  $upload->set_allowed_extensions(array('csv', 'png'));

  // Set allowed file type(s), if not set all types are allowed
  $upload->set_allowed_types(array(''));

  // Set max file size
  $upload->set_max_file_size(2000);

  // Set another file name, rename the original file. (set without the extension)
  //$upload->set_new_file_name('file_b');

  // Set upload directory
  $upload->set_upload_dir(__DIR__ );


  // upload_file($do_checks = TRUE, $unique_name = TRUE, $override = FALSE)
  // $do_checks =  allowed file extension, file type and file size check (RECOMMENDED!!) (but also possible to do this before this file upload function, see file info on the bottom)
  // $unique_name = if true file with the same name already exsist, create a new filename with a prefix from the original file
  //                if false, don't upload the file (returns false if $override = false)
  // $override = if $unique = false and $override = true, override the already existing file
  $upload->upload_file();

  // Return message for more info
  print $upload->return_message;

  // Delete the file
  //$upload->delete_file();

  // Get file info, example before uploading (upload_file)
  //$upload->get_file_extension();
  //$upload->get_file_size();
  //$upload->get_file_type();
  //$upload->get_file_name(); // without the extension, example as title in a database
}
