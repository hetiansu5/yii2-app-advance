<?php
namespace common\service\aws;

use Aws\S3\S3Client;
use Aws\S3\PostObjectV4;
use common\lib\fw\InstanceTrait;

class AwsService
{
    use InstanceTrait;
    const SDK_VERSION = 'latest'; //SDK version

    private static $instance = array(); //单例模式

    /**
     * 静态变量所有实例公用
     * @var array
     */

    private static $config = array(

        'version' => self::SDK_VERSION,

    );

    /**
     * set configured params（设置配置参数）
     * @param array $config
     * version - SDK version
     * region - storage's region 必填
     * key - Access key ID 必填
     * secret - Secret access key 必填
     */

    public static function setConfig($config)

    {

        foreach ($config as $key => $val) {

            self::$config[$key] = $val;

        }

    }

    /**
     * get signed token for uploading from client（获取用于客户端/前端直传文件的上传凭证）
     * @param string $bucket 空间名
     * @param array $ops 表单控制参数
     * @param array $form_inputs 表单控制参数
     * @param string $expires demo:+1 hours|+20 minutes 有效时间
     * @return array
     */

    public static function getUploadToken($bucket, $ops, $form_inputs, $expires)

    {

        $client = self::getS3Client(self::$config);

        $post_object = new PostObjectV4(

            $client,

            $bucket,

            $form_inputs,

            $ops,

            $expires

        );

        return array(

            'attributes' => $post_object->getFormAttributes(),

            'inputs' => $post_object->getFormInputs()

        );

    }

    /**
     * create bucket（创建新空间）
     * @param string $bucket 空间名
     * @param array $ops 其他非必选控制参数
     * @return array
     */

    public static function createBucket($bucket, $ops = array())

    {

        $ops['Bucket'] = $bucket;

        return self::getS3Client(self::$config)->createBucket($ops);

    }

    /**
     * upload local file （直接通过API直传文件）
     * @param string $bucket 空间名
     * @param string $key 资源名称
     * @param string $file_path 本地文件路径
     * @param array $ops 其他非必选控制
     * @return array
     */

    public static function putObject($bucket, $key, $file_path, $ops = array())

    {

        $ops['Bucket'] = $bucket;

        $ops['Key'] = $key;

        !isset($ops['Body']) ? $ops['Body'] = fopen($file_path, 'r') : null;

        !isset($ops['ACL']) ? $ops['ACL'] = 'private' : null;

        return self::getS3Client(self::$config)->putObject($ops);

    }

    /**
     * get file description info （获取文件的描述信息，可以拿到文件的hash（ETag）值等信息）
     * @param string $bucket bucket
     * @param string $key resource key
     * @param array $ops optional parameters
     * @return AwsResult
     */

    public static function getObject($bucket, $key, $ops = array())

    {

        $ops['Bucket'] = $bucket;

        $ops['Key'] = $key;

        return self::getS3Client(self::$config)->getObject($ops);

    }

    /**
     * get instance of S3Client(单例模式)
     * @param array $config
     * @return AwsS3S3Client
     */

    public static function getS3Client($config = array())

    {

        empty($config) ? $config = self::$config : null;

        $hash = md5(serialize($config));

        if (!isset(self::$instance['s3_client' . $hash])) {

            self::$instance['s3_client' . $hash] = new S3Client(array(

                'version' => $config['version'],

                'region' => $config['region'],

                'credentials' => array(

                    'key' => $config['key'],

                    'secret' => $config['secret']

                )

            ));

        }

        return self::$instance['s3_client' . $hash];

    }

    public static function defaultPush($key,$file){

        $config = array(

            'key' => \Yii::$app->params['aws_key'],

            'secret' => \Yii::$app->params['aws_secret'],

            'region' => \Yii::$app->params['aws_region'], //根据自己创建空间时设置的region节点填写

            'version' => self::SDK_VERSION,

        );

        $client = self::getS3Client($config);

        $ops['Bucket'] = \Yii::$app->params['aws_bucket'];;

        $ops['Key'] = $key;

        $ops['ACL'] = \Yii::$app->params['aws_acl'];

        $ops['Body'] = fopen($file, 'r');


        $res = $client->putObject($ops);

        return $res['ObjectURL']?$res['ObjectURL']:'';
    }

}