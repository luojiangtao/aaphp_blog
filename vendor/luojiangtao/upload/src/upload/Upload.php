<?php

namespace luojiangtao\upload;

/**
 * 文件上传
 * Class Upload
 * @package luojiangtao\upload
 */
class Upload
{
    //上传文件保存的路径
    private $uploadPath = './upload';

    /**
     * 构造方法
     * Upload constructor.
     * @param string $uploadPath [文件存放位置]
     */
    public function __construct($uploadPath = '')
    {
        $this->uploadPath = $uploadPath == '' ? $this->uploadPath : $uploadPath;
    }

    /**
     * 上传方法
     * @param $inputName [上传文件input中的name字段]
     * @return array|bool [返回文件信息]
     */
    public function upload($inputName)
    {
        if (empty($_FILES[$inputName]['name'])) {
            return false;
        }
        $message = '';
        switch ($_FILES[$inputName]['error']) {
            case 4:
                $message .= '没有文件被上传';
                break;
            case 3:
                $message .= '文件只有部分被上传';
                break;
            case 2:
                $message .= '上传文件的大小超过了HTML表单中MAX_FILE_SIZE选项指定的值';
                break;
            case 1:
                $message .= '上传的文件超过了php.ini中upload_max_filesize选项限制的值';
                break;
            case 0:
                $message .= '上传成功';
                break;
            default:
                $message .= '未知错误';
        }
        // 文件后缀名
        $ext = strchr($_FILES[$inputName]['name'], '.');
        // 文件名
        $filename = time() . rand(1, 100) . $ext;

        // 没有文件夹则创建
        is_dir($this->uploadPath) || mkdir($this->uploadPath, 0777, true);

        // 检测上传文件是否合法
        if (!is_uploaded_file($_FILES[$inputName]['tmp_name'])) {
            die('上传文件不合法 not a file');
        }

        // 补全文件名
        $full_name = $this->uploadPath . '/' . $filename;
        $fileInfo = array(
            'filename' => $filename,
            'tmp_name' => $_FILES[$inputName]['tmp_name'],
            'type' => $_FILES[$inputName]['type'],
            'error' => $_FILES[$inputName]['error'],
            'size' => $_FILES[$inputName]['size'],
            'message' => $message,
            'full_name' => $full_name,
        );

        // 把文件从临时目录移动到上传目录
        move_uploaded_file($_FILES[$inputName]['tmp_name'], $full_name);
        // 返回文件信息
        return $fileInfo;
    }
}