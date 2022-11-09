<?php

declare(strict_types=1);

namespace Demo\MonologMaxSize;

use Monolog\Handler\RotatingFileHandler as Handler;
use Monolog\Level;
use Monolog\LogRecord;

class RotatingAndSizeFileHandler extends Handler
{
    protected int $maxSize;
    // 1024 -> 1 KB
    // 1,048,576 -> 1 MB
    // 1,073,741,824 -> 1GB
    public function __construct(
        string $filename,
        int $maxFiles = 0,
        int $maxSize = 5242880, // 5MB
        int|string|Level $level = Level::Debug,
        bool $bubble = true,
        ?int $filePermission = null,
        bool $useLocking = false
    ) {
        $this->maxSize = $maxSize;
        parent::__construct($filename, $maxFiles, $level, $bubble, $filePermission, $useLocking);
    }

    protected function getOriginFilename(): string
    {
        return $this->filename;
    }

    protected function write(LogRecord $record): void
    {
        // print_r($record);
        // // 當第一筆資料寫入, 如果 log 檔是新的, 我們應該 rotate 他 (once per day)
        // if (null === $this->mustRotate) {
        //     $this->mustRotate = null === $this->url || !file_exists($this->url);
        // }

        // // 紀錄時間大於預定紀錄的時間檔案時，關閉檔案並且 rotate 新的檔案資源
        // if ($this->nextRotation <= $record->datetime) {
        //     $this->mustRotate = true;
        //     $this->close();
        // }

        // 先寫，再去做檔案切割
        parent::write($record);
        
        /** 取得檔案的名稱 */
        $filename = $this->getTimedFilename();
        clearstatcache();
        if ($this->maxSize > filesize($filename)) {
            return ;
        }

        $matchFiles = glob(str_replace('.log', '*', $filename));
        $n = count($matchFiles);
        // 檔案名稱為 mylog-2022-11-09.log
        // 我需要把檔案名稱改成 mylog-2022-11-09.1.log
        // 所以我要抓到當前資料夾中所有 mylog-2022-11-09*
        $newfilename = str_replace('.log', ".{$n}.log", $filename);
        @rename($filename, $newfilename);
        unset($filename);

        $this->mustRotate = true;
        $this->close();
    }
}