<?php
/**
 * Created by PhpStorm.
 * User: fthvg
 * Date: 2017/5/17
 * Time: 16:56
 */

/**
 * 临时文件目录
 * @return string
 */
function getPath()
{
    $path = __DIR__ . '/' . date('Ym');
    if (!is_dir($path)) {
        mkdir($path);
    }
    return $path;
}

$md5 = $_POST['md5'];

$post = $_POST;
//todo 查询是否已存在md5


$file_path = getPath() . '/' . $_POST['file_name'];

if (!file_exists($file_path)) {
    if (isset($post['last_upload']) && $post['last_upload'] == 'yes') {
        move_uploaded_file($_FILES['file']['tmp_name'], $file_path);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimetype = finfo_file($finfo, $file_path);
        finfo_close($finfo);
        /*if($mimetype!='application/java-archive'){
            unlink($file_path);
            echo json_encode(['code'=>0,'message'=>'类型错误']);
        }*/
        $md5 = md5_file($file_path);
        $sha1 = sha1_file($file_path);
        if ($post['md5'] == $md5) {
            $data['md5'] = $md5;
            $data['sha1'] = $sha1;
            $data['create_time'] = time();
            $data['name'] = $_POST['name'];
            $data['size'] = filesize($file_path);
            $save = $file_path;//实际要保存的路径
            if (true) {
                //todo 保存到数据库中
                if ($file_path != $save) {
                    rename($file_path, $save);
                }
                echo json_encode(['code' => 1, 'fileInfo' => $data]);
            } else {
                unlink($file_path);
                echo json_encode(['code' => 0, 'message' => '数据库保存失败']);
            }
        }

    } else {
        move_uploaded_file($_FILES['file']['tmp_name'], $file_path);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimetype = finfo_file($finfo, $file_path);
        finfo_close($finfo);
        /*if($mimetype!='application/java-archive'){
            unlink($file_path);
            echo json_encode( ['code'=>0,'message'=>'类型出错']);
        }*/
    }

} else {
    file_put_contents($file_path, file_get_contents($_FILES['file']['tmp_name']), FILE_APPEND);
    if (isset($post['last_upload']) && $post['last_upload'] == 'yes') {
        $md5 = md5_file($file_path);
        $sha1 = sha1_file($file_path);
        if ($post['md5'] == $md5) {
            $data['md5'] = $md5;
            $data['sha1'] = $sha1;
            $data['create_time'] = time();
            $data['name'] = $_POST['file_name'];
            $data['size'] = filesize($file_path);
            $save = $file_path;//实际要保存的路径
            if (true) {
                //todo 保存到数据库中

                if ($file_path != $save) {
                    rename($file_path, $save);
                }

                echo json_encode(['code' => 1, 'fileInfo' => $data]);
            } else {
                unlink($file_path);
                echo json_encode(['code' => 0, 'message' => '数据库保存失败']);
            }
        } else {
            unlink($file_path);
            echo json_encode(['code' => 0, 'message' => '可能是网络原因导致，md5校检不正确，请刷新后重新上传！', 'md5' => $md5]);
        }
    }
}