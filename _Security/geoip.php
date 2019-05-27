<?php
    function showProblemIP($a, $limit){
        foreach($a as $ip => $x_value) {
            if ($x_value >= $limit) {
                echo "Key=" . $ip . ", Value=" . $x_value;
            }
        }
    }

    function addToArray($ipAddr, & $a){
        $cnt = 0 ;
        if (array_key_exists($ipAddr, $a)) {
            $cnt = $a[$ipAddr] ;
            $cnt += 1 ;
            $a[$ipAddr] = $cnt;
        }
        else
            $a[$ipAddr] = "1";
    }

    function getProcessDate ()
    {
        try {
            $myfile = fopen("logProcessDate.txt", "r");
            $dt = fgets($myfile);
            fclose($myfile);
            print 'STARTING TIME: ' . $dt . PHP_EOL;
            
            return strtotime($dt);
        }
        catch ( Exception $e ) {
            echo 'WARNING Message: ' . $e->getMessage() . PHP_EOL;
            
            return strtotime('01-01-1975');
        }
    }
    
    function saveProcessDate ($dt)
    {
        $myfile = fopen("logProcessDate.txt", "w") or die("Unable to open file!");
        fwrite($myfile, "\n" . $dt);
        fclose($myfile);
    }
    
    function getPDate ($str)
    {
        $strdt = '';
        $A = explode(" ", $str);
        $strdt = $A[0] . '-' . $A[1] . '-' . date("Y") . ' ' . $A[2];
        $dt = strtotime($strdt);
        
        return $dt;
    }
    
    function getIpInfo ($ip, $code)
    {
		print ('IpInfo: ' . $ip . '/' . $code);
        $dd = 0 ;
        if ( $code == 'C' ) {
            $httpstr = "https://ipapi.co/" . $ip . "/country/";
            if ($dd == 1) print ("@C httpstr: $httpstr" . PHP_EOL);
            return file_get_contents($httpstr, false);
        } else {
            $httpstr = "https://ipapi.co/" . $ip . "/json/";
            if ($dd == 1) print ("@NULL httpstr: $httpstr" . PHP_EOL);
            return file_get_contents($httpstr, false);
        }
    }
    
    $localIP = getHostByName(getHostName());
    $arrIp['NOIP'] = "-1";
    print ("SVR IP: $localIP" . PHP_EOL);
    $str = '';
    $r = 0;
    $icnt = 0;
    $tgtcnt = 0;
    $file = fopen("/var/log/syslog.1", "r");
    $pos = 0;
    $idpos = 0;
    $ipaddr = '';
    $endpoint = 0;
    $l = 0;
    $lastip = '';
    $lastpdate = getProcessDate();
    $currpdate = null;
    
    $str = fgets($file);
    while ( !feof($file) ) {
        $icnt += 1;
        $r = $icnt % 100;
        
        $pos = strpos($str, '[UFW BLOCK]');
        $idpos = strpos($str, 'ID=');
        if ( $pos > 0 && $idpos > 0 ) {
            $pos = strpos($str, 'SRC=');
            $rowdt = getPDate($str);
            if ( $rowdt > $lastpdate ) {
                print ("@1 Date compare: $rowdt > $lastpdate" . PHP_EOL);
                if ( $pos > 0 ) {
                    $pos = $pos + 4;
                    /*$endpoint = strpos($str, 'DST=', $pos);*/
                    $endpoint = strpos($str, ' ', $pos);
                    $l = $endpoint - $pos;
                    $ipaddr = substr($str, $pos, $l);
                    if ( $ipaddr != $localIP ) {
    
                        addToArray($ipaddr, $arrIp) ;
                        $tgtcnt += 1;
                        
                        if ( $tgtcnt % 5 == 0 ) {
                            print ("Sleeping 3 sec...");
                            sleep(3);
                        }
                        
                        $ipinfo = getIpInfo($ipaddr, '@');
                        echo $ipinfo;
                        
                        $lastip = $ipaddr;
                        /* substr(string,start,length) */
                    }
                }
            }
            else {
                print ("@2 Date compare: $rowdt < $lastpdate" . PHP_EOL);
            }
        }
        $str = fgets($file);
    }
    fclose($file);
    
    print ("Total hits: $tgtcnt" . PHP_EOL);
    print ("Total Lines: $icnt " . PHP_EOL);
    
    showProblemIP($arrIp, 3) ;
    
    $ipinfo = getIpInfo($ipaddr, '@');
    echo $ipinfo;
?>
