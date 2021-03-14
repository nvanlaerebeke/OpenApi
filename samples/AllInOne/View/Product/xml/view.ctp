<?php 
$response['response']['CustomViewFile'] = true;
$var = Xml::build($response);
echo $var->asXml(); 