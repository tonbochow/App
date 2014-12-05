<?php

function pwdHash($password, $type = 'md5') {
    return hash($type, $password);
}

//递归删除某目录下所有文件
function delFileUnderDir($dirName) {
    $flag = false;
    if ($handle = opendir("$dirName")) {
        while (false !== ( $item = readdir($handle) )) {
            if ($item != "." && $item != "..") {
                if (is_dir("$dirName/$item")) {
                    delFileUnderDir("$dirName/$item");
                } else {
                    unlink("$dirName/$item");
                }
            }
        }
        closedir($handle);
        $flag = true;
    } else {
        $flag = true;
    }

    return $flag;
}

?>