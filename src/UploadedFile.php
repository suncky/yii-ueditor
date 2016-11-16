<?php
namespace suncky\yii\widgets\ueditor;

/**
 * Class UploadedFile
 * @package suncky\yii\ueditor
 */
class UploadedFile extends \yii\web\UploadedFile
{
    private $_totalChunk;
    private $_currentChunk;
    private $_sign;

    /**
     * 是否启动断点续传
     *
     * @return bool
     */
    public function isChunkUpload() {
        return (bool)$this->_totalChunk;
    }

    public function saveAs($file, $deleteTempFile = true) {
        if ($this->isChunkUpload() && $this->saveChunk() && !($this->isCompleteFile() && $this->mergeChunk())) {
            return false;
        }

        if ($this->isChunkUpload()) {
            if ($this->error == UPLOAD_ERR_OK) {
                if ($deleteTempFile) {
                    return rename($this->tempName, $file);
                } else {
                    return copy($this->tempName, $file);
                }
            }
        } else {
            return parent::saveAs($file, $deleteTempFile);
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getCurrentChunk() {
        return $this->_currentChunk;
    }

    /**
     * @param mixed $currentChunk
     */
    public function setCurrentChunk($currentChunk) {
        $this->_currentChunk = $currentChunk;
    }

    /**
     * @return mixed
     */
    public function getTotalChunk() {
        return $this->_totalChunk;
    }

    /**
     * @param mixed $totalChunk
     */
    public function setTotalChunk($totalChunk) {
        $this->_totalChunk = $totalChunk;
    }

    /**
     * @return mixed
     */
    public function getSign() {
        return $this->_sign;
    }

    /**
     * @param mixed $sign
     */
    public function setSign($sign) {
        $this->_sign = $sign;
    }

    /**
     * 判断是否完整的文件
     *
     * @return bool
     */
    public function isCompleteFile() {
        $result = true;
        for ($i = 0; $i < $this->getTotalChunk(); $i++) {
            if (!is_file($this->getChunkFilePath($i))) {
                $result = false;

                break;
            }
        }

        return $result;
    }

    /**
     * 获取或创建临时文件夹
     *
     * @return string
     */
    protected function getTempPath() {
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'upload_chunks' . DIRECTORY_SEPARATOR;
        is_dir($dir) || @mkdir($dir, 0777);

        return $dir;
    }

    /**
     * 获取分块文件路径
     *
     * @param $num
     *
     * @return string
     */
    protected function getChunkFilePath($num) {
        return $this->getTempPath() . $this->name . $this->getSign() . $num . '.part';
    }

    /**
     * 保存分块文件
     *
     * @return bool
     */
    protected function saveChunk() {
        return parent::saveAs($this->getChunkFilePath($this->getCurrentChunk()));
    }

    /**
     * 合并分块文件
     *
     * @return bool
     */
    protected function mergeChunk() {
        $hasError = false;
        $tempName = $this->getTempPath() . $this->name;
        if (($file = fopen($tempName, 'wb')) && flock($file, LOCK_EX)) {
            for ($i = 0; $i < $this->getTotalChunk(); $i++) {
                if (!($in = fopen($this->getChunkFilePath($i), 'rb'))) {
                    $hasError = true;
                    break;
                }
                while ($buff = fread($in, 4096)) {
                    fwrite($file, $buff);
                }
                @fclose($in);
                @unlink($this->getChunkFilePath($i));
            }
            flock($file, LOCK_UN);
            fclose($file);
            if ($hasError) {
                $this->error = UPLOAD_ERR_PARTIAL;
            }

            $this->tempName = $tempName;

            return true;
        }

        return false;
    }
}