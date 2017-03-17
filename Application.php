<?php
//error_reporting(E_ALL); ini_set('display_errors', '1'); 
$server_URL = "http://127.0.0.1:4569";  //Server address needed for sending sample request email 
$db_server = "localhost";
$db_name = "php_intranet_uniformsourcing";                          // database name
$db_uname= "globaluniformuser";                              // username to connect to database
$db_pass= "globaluniformpassword";                                // password of username to connecto to database
$debug="off";                                   // set to on for the little debug code i have set on/off
$PHPLIBDIR="/var/www/html/phplib/";               // base dir for getting libs
$PHPLIB=$PHPLIBDIR;
$JSONLIB=$PHPLIBDIR."jsonwrapper/";     
// DB connection stuff
//$connection = mysql_connect($db_server, $db_uname, $db_pass)
//or die(mysql_error());
//$db = mysql_select_db($db_name, $connection)
//or die(mysql_error());
$isMailServer ="false";
$mailServerAddress = "colomx.i2net.com";// if isMailServer is false please specify the mail server address (ex. mail.i2net.com)
$account_emailid="accounting@uniforms.net";
try{
	$connection = pg_connect("host = $db_server ".
						 "dbname = $db_name ".
						 "user = $db_uname ".
						 "password = $db_pass");

}
catch(\Exception $e)
{
	var_dump($e->getMessage());
}

// Central variables for entire module
$compquery=("SELECT \"ID\", \"client\" ".
			"FROM \"clientDB\" ".
			"WHERE \"ID\" = '1' ");
if(!($resultcomp=pg_query($connection,$compquery))){
	print("Failed compquery: " . pg_last_error($connection));
	exit;
}
while($rowcomp = pg_fetch_array($resultcomp)){
	$datacomp[]=$rowcomp;
}
	$compname="".$datacomp[0]['client']."";			// company name




$sql = "SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE  table_schema = 'public' AND table_name = 'tbl_container')";

$tbl_container_exists;
if(!($result=pg_query($connection,$sql)))
{
    print("Failed StyleQuery: " . pg_last_error($connection));
    exit;
}
while($row = pg_fetch_array($result))
{
    $tbl_container_exists=$row;
}

//tbl_container has styleId and invId

//such that each tbl_invStorage[invId] has many tbl_inventory
//and each tbl_inventory have multiple tbl_container

if($tbl_container_exists['exists'] === 'f')
{
	//var_dump('false');

	$sql = 'CREATE TABLE public.tbl_container
			(
			  "containerId" bigint NOT NULL DEFAULT 
			  		nextval((\'tbl_container_containerId_seq\'::text)::regclass),
			  "name" character varying(100) UNIQUE,
			  "styleId" bigint NOT NULL,
			  "invId"   bigint,
			  "scaleId" bigint,
			  "sizeScaleId" bigint,
			  "colorId" bigint,
			  "opt1ScaleId" bigint,
			  "opt2ScaleId" bigint,
			  quantity integer,
			  "locationId" character varying(200),
			  notes character varying,
			  "styleNumber" character varying(20),
			  "mainSize" character varying(150),
			  "rowSize" character varying(150),
			  "isStorage" smallint DEFAULT 0,
			  "newQty" integer DEFAULT 0,
			  "isActive" smallint NOT NULL DEFAULT 1,
			  "createdBy" bigint DEFAULT 0,
			  "createdDate" bigint DEFAULT 0,
			  "updatedBy" bigint DEFAULT 0,
			  "updatedDate" bigint DEFAULT 0,
			  "columnSize" character varying(150),
			  CONSTRAINT tbl_container_pkey PRIMARY KEY ("containerId")
			)
			WITH (
			  OIDS=FALSE
			);
			ALTER TABLE public.tbl_container
			  OWNER TO globaluniformuser';

	if(!($result=pg_query($connection,$sql)))
    {
        
        print_r('Application.php -- error in insert tbl_container');
        print("Failed StyleQuery: " . pg_last_error($connection));
        exit();
    }
    else
    {
    	// successfully built the table
    }
    
}





//adding contId to table tbl_invStorage

	// $sql = "SELECT EXISTS (SELECT column_name 
	// 	FROM information_schema.columns 
	// 	WHERE table_name='tbl_invStorage' and column_name='contId')";

	// if(!($result=pg_query($connection,$sql)))
 //    {
 //        print_r('Application.php -- error in insert tbl_invStorage');
 //        print("Failed StyleQuery: " . pg_last_error($connection));
 //        exit();
 //    }
 //    while($row = pg_fetch_array($result))
	// {
	//     $tbl_container_exists=$row;
	// }
 //    if($tbl_container_exists['exists'] === 'f')
 //    {
 //    	$sql = "ALTER TABLE "tbl_invStorage" ADD COLUMN convId BIGINT";
 //    	if(!($result=pg_query($connection,$sql)))
	//     {
	//         print_r('Application.php -- error in adding column convId to tbl_invStorage');
	//         print("Failed StyleQuery: " . pg_last_error($connection));
	//         exit();
	//     }
	//     else
	//     {
	//     	print_f('successfully added column convId');
	//     }

 //    }
//end -- adding contId to table tbl_invStorage




    //exit();





?>
