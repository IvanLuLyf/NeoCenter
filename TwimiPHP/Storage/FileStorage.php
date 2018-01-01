<?php
/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2018/1/1
 * Time: 15:10
 */
class FileStorage extends Storage
{

    protected $uploadPath;

    public function __construct()
    {
        $this->uploadPath = APP_PATH . 'upload/';
    }

    public function read($filename)
    {
        return file_get_contents($this->uploadPath.$filename);
    }

    public function write($filename, $content)
    {
        file_put_contents($this->uploadPath.$filename,$content);
    }

    public function upload($filename, $filepath)
    {
        move_uploaded_file($filepath,$this->uploadPath.$filename);
    }

    public function remove($filename)
    {
        unlink($this->uploadPath.$filename);
    }
}