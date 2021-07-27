<?php 

//get all data according to lat and lng with order by desitance

SELECT b.link_id,b.lat,b.lng FROM ( SELECT a.link_id, a.lat, a.lng,(3959 * acos (cos ( radians('45.648098') )* cos( radians( a.lat ) )* cos( radians( a.lng ) - radians('-122.598259') )+ sin ( radians('45.648098') )* sin( radians( a.lat ) )) ) AS distance FROM jos_mt_links as a   where link_featured = 1 HAVING distance < '2') As b GROUP BY b.link_id  ORDER BY distance



http://www.directoriomovilnw.com/webservice_2.php?service_type=get_img&lat=30.38167313&lng=76.48526799

http://www.directoriomovilnw.com/webservice_2.php?service_type=get_img&lat=45.648098&lng=-122.598259


//get all data according to lat and lng

SELECT a.*, a.latitude, a.longitude,(3959 * acos (cos ( radians('%s') ) * cos( radians( a.latitude ) ) * cos( radians( a.longitude ) - radians('%s') )+ sin ( radians('%s') )* sin( radians( a.latitude ) )) )  AS distance FROM tbl_radio_dtl as a  HAVING distance < '%s' ORDER BY a.id DESC


SELECT a.*, a.latitude, a.longitude,(3959 * acos (cos ( radians('%s') ) * cos( radians( a.latitude ) ) * cos( radians( a.longitude ) - radians('%s') )+ sin ( radians('%s') )* sin( radians( a.latitude ) )) )  AS distance FROM tbl_radio_dtl as a  HAVING distance < '%s' ORDER BY distance