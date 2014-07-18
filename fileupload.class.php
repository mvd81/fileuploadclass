<?php
/**
 * Created by PhpStorm.
 * User: Marcel van Doornen
 * Date: 18-7-14
 * Time: 11:41
 * Version: 1.0
 */

class fileupload {

  private $file;                                         // The uploaded file (information) (same as $_FILES['field_name'][x])
  private $file_name;                                    // The file name without the extension
  private $file_size;                                    // File size
  private $file_extension;                               // File extension
  private $file_type;                                    // File type
  private $allowed_extensions = array();                 // Allowed file extension(s)
  private $allowed_types = array();                      // Allowed file type(s)
  private $max_file_size;                                // Max file size
  private $new_file_name;                                // Change the filename for this upload
  private $upload_dir;                                   // Upload location
  private $uploaded_file;                                // Full path, set after uploaded the file
  public $return_message;                                // Return message for more info


  /**
   * Set file and set some file information
   * @param $file_upload_item
   */
  public function __construct($file_upload_item) {
    // Set some properties
    $this->file = $file_upload_item;
    $info = pathinfo($file_upload_item['name']);
    $this->file_name = $info['filename'];
    $this->file_extension = $info['extension'];
    $this->file_type = $file_upload_item['type'];
    $this->file_size = $file_upload_item['size'];
  }

  /**
   * Function check if file with this name already exists
   * @param $file
   * @return bool
   */
  private function is_file_exists($file) {
    if (file_exists($this->upload_dir . '\\' . $file . '.' . $this->file_extension)) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Create unique file name
   * @param $file_name
   * @return string
   */
  private function make_unique_file_name($file_name) {
    // Create a unique file name
    $new_file_name = uniqid($file_name);

    // Check ik file already exist
    if (!self::is_file_exists($new_file_name)) {
      return $new_file_name;
    }

    // Try again
    self::make_unique_file_name($new_file_name);
  }

  /**
   * Set valid file extensions
   * @param array $extensions
   */
  public function set_allowed_extensions($extensions) {
    $this->allowed_extensions = $extensions;
  }

  /**
   * Set allow file types
   * @param array $types
   */
  public function set_allowed_types($types) {
    $this->allowed_types = $types;
  }

  /**
   * Set new filename for this upload/file
   * @param $name
   */
  public function set_new_file_name($name) {
    $this->new_file_name = $name;
  }

  /**
   * Set max file size
   * @param $size
   */
  public function set_max_file_size($size) {
    $this->max_file_size = $size;
  }

  /**
   * Set upload dir for this file
   * @param $dir
   */
  public function set_upload_dir($dir) {
    $this->upload_dir = $dir . '\\';
  }

  /**
   * Get file extension
   * @return string
   */
  public function get_file_extension() {
    return $this->file_extension;
  }

  /**
   * Get file size
   * @return mixed
   */
  public function get_file_size() {
    return $this->file_size;
  }

  /**
   * Get file type
   * @return mixed
   */
  public function get_file_type() {
    return $this->file_type;
  }

  /**
   * Get the filename without the extension
   * @return mixed
   */
  public function get_file_name() {
    $this->file_name;
  }

  /**
   * Check if this file extension is allowed to upload
   */
  public function is_valid_allowed_extension() {
    // Check if allowed extension are set, else all extensions are valid
    if (!empty($this->allowed_extensions)) {
      if (!in_array($this->file_extension, $this->allowed_extensions)) {
        return FALSE;
      }
    }
    return TRUE;
  }

  /**
   * Check if max file size is valid
   * @return bool
   */
  public function is_valid_max_file_size() {
    // Check if max file size is set, else all sizes are valid
    if (!empty($this->max_file_size)) {
      if ($this->file_size > $this->max_file_size) {
        return FALSE;
      }
    }
    return TRUE;
  }

  /**
   * Check if file type is valid
   * @return bool
   */
  public function is_valid_file_type() {
    // If allow file type not set, all types are allowed
    if (!empty($this->allowed_type)) {
      if (!array($this->file_type, $this->allowed_types)) {
        return FALSE;
      }
    }
    return TRUE;
  }

  /**
   * Upload the file here
   *
   * @param bool $do_checks
   * @param bool $unique_name
   * @param bool $override
   * @return bool
   */
  public function upload_file($do_checks = TRUE, $unique_name = TRUE, $override = FALSE) {

    // First do some valid checks before upload
    if ($do_checks) {

      // Check extension
      if (!self::is_valid_allowed_extension()) {
        $this->return_message = $this->file_extension . ' is not a valid file extension.';
        return FALSE;
      }

      // Check max file size
      if (!self::is_valid_max_file_size()) {
        $this->return_message = 'File ' . ' ' . $this->file_name . '.' . $this->file_extension . ' size is ' . $this->file_size . ' and is greater than the max file size(' . $this->max_file_size . ').';
        return FALSE;
      }

      // Check file type
      if (!self::is_valid_file_type()) {
        $this->return_message = $this->file_type . ' is not a valid file type.';
        return FALSE;
      }
    }

    // Set file name
    $file_name = !empty($this->new_file_name) ? $this->new_file_name : $this->file_name;
    // Replace spaces
    $file_name = str_replace(' ', '_', $file_name);

    // Do some checks/replaces for the filename
    if (self::is_file_exists($file_name)) {

      if (!$unique_name && !$override) {
        $this->return_message = 'File with name: ' . $file_name . '.' . $this->file_extension . ' already exists.';
        return FALSE;
      }
      elseif ($unique_name && !$override) {
        // Create a new file name, with a prefix from the original
        $file_name = self::make_unique_file_name($file_name);
      }
      // Name is ok here to override the existing file with the same name
    }

    // Set new filename
    $this->file_name = $file_name;

    // File name + extension
    $upload_file = $file_name . '.' . $this->file_extension;

    // Try to upload the file
    if (move_uploaded_file($this->file['tmp_name'], $this->upload_dir . $upload_file)) {
      $this->uploaded_file = $this->upload_dir . $upload_file;
      $this->return_message = 'File: ' . $upload_file . ' successfully uploaded.';
      return TRUE;
    }
    else {
      // Upload failed
      $this->return_message = "Can't upload file: " . $upload_file . ' to this location: ' . $this->upload_dir . $upload_file . '.';
      return FALSE;
    }
  }

  /**
   * Delete a file
   *
   * @return bool
   */
  public function delete_file() {
    // First check if the file exists
    if (file_exists($this->uploaded_file)) {

      // Try to delete the file
      if (!unlink($this->uploaded_file)) {
        $this->return_message = 'Error on deleting file: ' . $this->uploaded_file . '.';
        return FALSE;
      }
      return TRUE;
    }

    // File does not exists here
    $this->return_message = 'File: ' . $this->uploaded_file . 'does not exists.';
    return FALSE;
  }
}