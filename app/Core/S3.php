<?php
// namespace Core;
use Aws\S3\S3Client;
class S3 {
    private $s3;
    function __construct() {
        $this->s3 = new S3Client(config('s3'));
    }
    function get($bukket, $key) {
        try {
            return $this->s3->getObject([
                'Bucket' => $bukket,
                'Key' => $key,
            ]);
        } catch (Aws\S3\Exception\S3Exception $e) {
            return false;
        }
    }
    function put($bukket, $key, $file, $ct = null, $acl = 'public-read') {
        try {
            return $this->s3->putObject([
                'Bucket' => $bukket,
                'Key' => $key,
                'Body' => $file,
                'ContentType' => $ct,
                'ACL' => $acl,
            ]);
        } catch (Aws\S3\Exception\S3Exception $e) {
            return false;
        }
    }
    function delete($bukket, $key) {
        try {
            return $this->s3->deleteObject([
                'Bucket' => $bukket,
                'Key' => $key,
            ]);
        } catch (Aws\S3\Exception\S3Exception $e) {
            return false;
        }
    }
    function url($bukket, $key) {
        try {
            return $this->s3->getObjectUrl($bukket, $key);
        } catch (Aws\S3\Exception\S3Exception $e) {
            return false;
        }
    }
    function exist($bukket, $key) {
        try {
            return $this->s3->doesObjectExist($bukket, $key);
        } catch (Aws\S3\Exception\S3Exception $e) {
            return false;
        }
    }
}
function s3() {
    return $GLOBALS['s3'] = $GLOBALS['s3'] ?? new S3();
}
?>
