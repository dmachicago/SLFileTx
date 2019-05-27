<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    function findMember ($memberid, $pw)
    {
        echo "findMember memberid: $memberid" . PHP_EOL;
        echo "findMember pw: $pw" . PHP_EOL;
        
        $global_ipaddr = '45.32.129.86';
        $rc = 0;
        $url = 'https://' . $global_ipaddr . '/var/www/html/php/filedb.php';
        $fqn = '/var/www/html/php/_jsonFiles/members.txt';
        $str = file_get_contents($fqn);
        $json = json_decode($str, true); // decode the JSON into an associative array
        
        //var_dump($json);
        //var_dump($str);
        
        $str = '{
    "members": [{
        "member": {
            "data": {
                "memberid": "MrW",
                "pw": "Welcome1#",
                "FavoriteNumber": "5656",
                "US-State": "Minnosota",
                "status": "enabled",
                "expdate": "2020/12/31"
            }
        }
    }, {
        "member": {
            "data": {
                "memberid": "deaN",
                "pw": "Welcome1#",
                "FavoriteNumber": "3434",
                "US-State": "Minnosota",
                "status": "enabled",
                "expdate": "2020/12/31"
            }
        }
    }, {
        "member": {
            "data": {
                "memberid": "wMiller",
                "pw": "JuneBug@01",
                "FavoriteNumber": "2419",
                "US-State": "Minnosota",
                "status": "enabled",
                "expdate": "2020/12/31"
            }
        }
    }]
}';
        
        $jsonArray = json_decode($str, true);
        $jsonObject = json_decode($str);
        //var_dump($jsonArray);
        //var_dump($jsonObject);
        $currmember = '';
        $prevmember = '@@@@@';
        $jsonIterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($jsonArray));
        foreach ( $jsonIterator as $key => $val ) {
            if ( $currmember != $prevmember ) {
                $prevmember = $currmember;
            }
            if ( $key == 'memberid' && $val == $memberid ) {
                $currmember = $memberid;
                echo '** Member ID Found: $key';
            }
            if ( $key == 'pw' && $val == $pw && $currmember == $prevmember ) {
                $currmember = $memberid;
                echo '**@@@  Member EXISTS: $prevmember';
                $rc = 1;
                break;
            }
            //if(is_array($val)) {
            //    echo "key: $key:\n";
            //} else {
            //    echo "Pair: $key => $val\n";
            //}
        }
        echo "RC: $rc:\n";
        
        return $rc;
    }

?>