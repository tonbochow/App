<?php

function pwdHash($password, $type = 'md5') {
    return hash($type, $password);
}

//递归删除某目录下所有文件
function delFileUnderDir($dirName, $keep_file = null) {
    $flag = false;
    if ($handle = opendir("$dirName")) {
        while (false !== ( $item = readdir($handle) )) {
            if ($item != "." && $item != "..") {
                if (is_dir("$dirName/$item")) {
                    delFileUnderDir("$dirName/$item");
                    rmdir("$dirName/$item");
                } else {
                    if ($keep_file !== null) {
                        if ($keep_file != "$dirName/$item") {
                            unlink("$dirName/$item");
                        }
                    } else {
                        unlink("$dirName/$item");
                    }
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

//将存入数据库的文件写入配置文件
function writeConfig($config_file, $config_content, $field) {
    $field_config = C($field);  //将默认配置参数的内容赋值给$a;
    $new_config = array_merge($field_config, $config_content);
    $settingstr = "<?php \n return array(\n'".$field."' =>array(\n";
    foreach ($new_config as $key => $v) {
        $settingstr.= "\t'" . $key . "'=>'" . $v . "',\n";
    }
    $settingstr.="),\n);\n?>\n";
    file_put_contents($config_file, $settingstr);
    delFileUnderDir(RUNTIME_PATH);//修改配置文件后清除缓存
}

?>