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
pg_free_result($row);
//tbl_container has styleId and invId

//such that each tbl_invStorage[invId] has many tbl_inventory
//and each tbl_inventory have multiple tbl_container

if($tbl_container_exists['exists'] === 'f')
{
	//var_dump('false');

	$sql = 'CREATE TABLE public.tbl_container
			(
			  "containerId" SERIAL PRIMARY KEY,
			  "name" character varying(100),
			  "styleId" bigint,
			  "scaleId" bigint,
			  "sizeScaleId" bigint,
			  "colorId" bigint,
			  "opt1ScaleId" bigint,
			  "opt2ScaleId" bigint,
			  "quantity" integer,
			  "box" character varying(30) UNIQUE,
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
			  "columnSize" character varying(150)
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
   pg_free_result($result); 
}

//creating table conveyor
$sql = "SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE  table_schema = 'public' AND table_name = 'tbl_conveyor')";

$tbl_conveyor_exists;
if(!($result=pg_query($connection,$sql)))
{
    print("Failed StyleQuery: " . pg_last_error($connection));
    exit;
}
while($row = pg_fetch_array($result))
{
    $tbl_conveyor_exists=$row;
}
pg_free_result($result);
//tbl_container has styleId and invId

//such that each tbl_invStorage[invId] has many tbl_inventory
//and each tbl_inventory have multiple tbl_container

if($tbl_conveyor_exists['exists'] === 'f')
{
	//var_dump('false');

	$sql = 'CREATE TABLE public.tbl_conveyor
			(
			  "conveyorId" SERIAL PRIMARY KEY,
			  "name" character varying(100),
			  "styleId" bigint,
			  "scaleId" bigint,
			  "sizeScaleId" bigint,
			  "colorId" bigint,
			  "opt1ScaleId" bigint,
			  "opt2ScaleId" bigint,
			  "quantity" integer,
			  "slot" character varying(30) UNIQUE,
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
			  "columnSize" character varying(150)
			)
			WITH (
			  OIDS=FALSE
			);
			ALTER TABLE public.tbl_container
			  OWNER TO globaluniformuser';

	if(!($result=pg_query($connection,$sql)))
    {
        
        print_r('Application.php -- error in insert tbl_conveyor');
        print("Failed StyleQuery: " . pg_last_error($connection));
        exit();
    }
    else
    {
    	// successfully built the table
    }
    pg_free_result($result);
    
}

//tbl_log_updates create
$sql = "SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE  table_schema = 'public' AND table_name = 'tbl_log_updates')";
$tbl_log_updates_exists;
if(!($result=pg_query($connection,$sql)))
{
    print("Failed StyleQuery: " . pg_last_error($connection));
    exit;
}
while($row = pg_fetch_array($result))
{
    $tbl_log_updates_exists=$row;
}
pg_free_result($row);
if($tbl_log_updates_exists['exists'] === 'f')
{
	//var_dump('false');

	$sql = 'CREATE TABLE public.tbl_log_updates
			(
			  "Logid" SERIAL PRIMARY KEY,
			  "styleId" bigint,
			  "createdBy" bigint DEFAULT 0,
			  "createdDate" bigint DEFAULT 0,
			  "updatedDate" bigint DEFAULT 0,
			  "previous" text,
			  "present" text
			)
			WITH (
			  OIDS=FALSE
			);
			ALTER TABLE public.tbl_log_updates
			  OWNER TO globaluniformuser';

	if(!($result=pg_query($connection,$sql)))
    {
        
        print_r('Application.php -- error in insert tbl_log_updates');
        print("Failed StyleQuery: " . pg_last_error($connection));
        exit();
    }
    else
    {
    	// successfully built the table
    }
    pg_free_result($result);
}
$sql = "SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE  table_schema = 'public' AND table_name = 'storage_map')";

$tbl_storage_map_exists;
if(!($result=pg_query($connection,$sql)))
{
    print("Failed StyleQuery: " . pg_last_error($connection));
    exit;
}
while($row = pg_fetch_array($result))
{
    $tbl_storage_map_exists=$row;
}
pg_free_result($row);
if($tbl_storage_map_exists['exists'] === 'f')
{
	$sql = $sql = 'CREATE TABLE public.storage_map
			(
			  "id" SERIAL PRIMARY KEY,
			  "locationId" bigint,
			  "warehouseId" bigint,
			  "inventoryIds" text,
			  "containerIds" text,
			  "conveyerIds" text
			)
			WITH (
			  OIDS=FALSE
			);
			ALTER TABLE public.storage_map
			  OWNER TO globaluniformuser';

	if(!($result=pg_query($connection,$sql)))
    {
        
        print_r('Application.php -- error in insert tbl_container');
        print("Failed StyleQuery: " . pg_last_error($connection));
        exit();
    }
    else
    {
    	//print('successfully built the table
    }

    pg_free_result($result);

}

//adding contId to table tbl_invStorage

	$sql = "SELECT EXISTS (SELECT column_name 
		FROM information_schema.columns 
		WHERE table_name='tbl_invStorage' and column_name='warehouse_id')";

	$column_exists;
	if(!($result=pg_query($connection,$sql)))
    {
        print_r('Application.php -- error in insert column warehouse_id in table tbl_invStorage');
        print("Failed StyleQuery: " . pg_last_error($connection));
        exit();
    }
    while($row = pg_fetch_array($result))
	{
	    $column_exists=$row;
	}
	pg_free_result($row);
    if($column_exists['exists'] === 'f')
    {


    	$sql = 'ALTER TABLE "tbl_invStorage" ADD COLUMN 
    			warehouse_id bigint NULL ';
    	// var_dump($sql);
    	// exit();
    	if(!($result=pg_query($connection,$sql)))
	    {
	        print_r('Application.php -- error in adding column warehouse_id to tbl_invStorage');
	        print("Failed StyleQuery: " . pg_last_error($connection));
	        exit();
	    }
	    else
	    {

	    	print_r('successfully added column convId');
	    	//exit();
	    }
	    pg_free_result($result);

    }


    $sql = "SELECT EXISTS (SELECT column_name 
		FROM information_schema.columns 
		WHERE table_name='tbl_inventory' and column_name='warehouse_id')";

	$column_exists;
	if(!($result=pg_query($connection,$sql)))
    {
        print_r('Application.php -- error in insert column warehouse_id in table tbl_inventory');
        print("Failed StyleQuery: " . pg_last_error($connection));
        exit();
    }
    while($row = pg_fetch_array($result))
	{
	    $column_exists=$row;
	}
	pg_free_result($row);
    if($column_exists['exists'] === 'f')
    {


    	$sql = 'ALTER TABLE "tbl_inventory" ADD COLUMN 
    			warehouse_id bigint NULL ';
    	// var_dump($sql);
    	// exit();
    	if(!($result=pg_query($connection,$sql)))
	    {
	        print_r('Application.php -- error in adding column warehouse_id to tbl_inventory');
	        print("Failed StyleQuery: " . pg_last_error($connection));
	        exit();
	    }
	    else
	    {

	    	print_r('successfully added column warehouse_id');
	    	//exit();
	    }
	    pg_free_result($result);

    }

    $sql = "SELECT EXISTS (SELECT column_name 
		FROM information_schema.columns 
		WHERE table_name='inventory' and column_name='warehouse_id')";

	$column_exists;
	if(!($result=pg_query($connection,$sql)))
    {
        print_r('Application.php -- error in insert column warehouse_id in table tbl_inventory');
        print("Failed StyleQuery: " . pg_last_error($connection));
        exit();
    }
    while($row = pg_fetch_array($result))
	{
	    $column_exists=$row;
	}
	pg_free_result($row);
    if($column_exists['exists'] === 'f')
    {


    	$sql = 'ALTER TABLE "inventory" ADD COLUMN 
    			warehouse_id bigint NULL ';
    	// var_dump($sql);
    	// exit();
    	if(!($result=pg_query($connection,$sql)))
	    {
	        print_r('Application.php -- error in adding column warehouse_id to tbl_inventory');
	        print("Failed StyleQuery: " . pg_last_error($connection));
	        exit();
	    }
	    else
	    {

	    	//print_r('successfully added column warehouse_id');
	    	//exit();
	    }
	    pg_free_result($result);

    }


    $sql = "SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE  table_schema = 'public' AND table_name = 'warehouse')";

	$tbl_warehouse_exists;
	if(!($result=pg_query($connection,$sql)))
	{
	    print("Failed StyleQuery: " . pg_last_error($connection));
	    exit;
	}
	while($row = pg_fetch_array($result))
	{
	    $tbl_warehouse_exists=$row;
	}
	pg_free_result($row);
	if($tbl_warehouse_exists['exists'] === 'f')
	{
		$sql = $sql = 'CREATE TABLE public.warehouse
				(
				  "id" SERIAL PRIMARY KEY,
				  "locationId" bigint,
				  "warehouse_name" character varying(30)
				)
				WITH (
				  OIDS=FALSE
				);
				ALTER TABLE public.warehouse
				  OWNER TO globaluniformuser';

		if(!($result=pg_query($connection,$sql)))
	    {
	        
	        print_r('Application.php -- error in creating warehouse table');
	        print("Failed StyleQuery: " . pg_last_error($connection));
	        exit();
	    }
	    else
	    {
	    	//print('successfully built the table
	    }

	    pg_free_result($result);

	}




	$sql = "SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE  table_schema = 'public' AND table_name = 'audit_logs')";

	$tbl_audit_logs_exists;
	if(!($result=pg_query($connection,$sql)))
	{
	    print("Failed StyleQuery: " . pg_last_error($connection));
	    exit;
	}
	while($row = pg_fetch_array($result))
	{
	    $tbl_audit_logs_exists=$row;
	}
	pg_free_result($row);
	if($tbl_audit_logs_exists['exists'] === 'f')
	{
		$sql = $sql = 'CREATE table "audit_logs" 
					("id" SERIAL PRIMARY KEY,"inventory_id" VARCHAR(50),
					"employee_id" VARCHAR(50),"updated_time" VARCHAR(50),
					"log" TEXT )
				WITH (
				  OIDS=FALSE
				);
				ALTER TABLE public.warehouse
				  OWNER TO globaluniformuser';

		if(!($result=pg_query($connection,$sql)))
	    {
	        
	        print_r('Application.php -- error in creating warehouse table');
	        print("Failed StyleQuery: " . pg_last_error($connection));
	        exit();
	    }
	    else
	    {
	    	//print('successfully built the table
	    }

	    pg_free_result($result);
	}


	$sql = "SELECT EXISTS (SELECT column_name 
		FROM information_schema.columns 
		WHERE table_name='tbl_invStorage' and column_name='slot')";

	$column_exists;
	if(!($result=pg_query($connection,$sql)))
    {
        print_r('Application.php -- error ');
        print("Failed StyleQuery: " . pg_last_error($connection));
        exit();
    }
    while($row = pg_fetch_array($result))
	{
	    $column_exists=$row;
	}
	pg_free_result($row);
    if($column_exists['exists'] === 'f')
    {


    	$sql = 'ALTER TABLE "tbl_invStorage" ADD COLUMN 
    			slot character varying(30) ';
    	// var_dump($sql);
    	// exit();
    	if(!($result=pg_query($connection,$sql)))
	    {
	        print_r('Application.php -- error ');
	        print("Failed StyleQuery: " . pg_last_error($connection));
	        exit();
	    }
	    else
	    {
	    	print_r('successfully added column slot');
	    	//exit();
	    }
	    pg_free_result($result);

    }

    $sql = "SELECT EXISTS (SELECT column_name 
		FROM information_schema.columns 
		WHERE table_name='tbl_invStorage' and column_name='type')";

	$column_exists;
	if(!($result=pg_query($connection,$sql)))
    {
        print_r('Application.php -- error ');
        print("Failed StyleQuery: " . pg_last_error($connection));
        exit();
    }
    while($row = pg_fetch_array($result))
	{
	    $column_exists=$row;
	}
	pg_free_result($row);
    if($column_exists['exists'] === 'f')
    {
    	$sql = 'ALTER TABLE "tbl_invStorage" ADD COLUMN 
    			type character varying(30)';
    	// var_dump($sql);
    	// exit();
    	if(!($result=pg_query($connection,$sql)))
	    {
	        print_r('Application.php -- error ');
	        print("Failed StyleQuery: " . pg_last_error($connection));
	        exit();
	    }
	    else
	    {
	    	print_r('successfully added column slot');
	    	//exit();
	    }
	    pg_free_result($result);
    }




//end -- adding contId to table tbl_invStorage

$sql = "SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE  table_schema = 'public' AND table_name = 'storage_table_data')";

$storage_table_data;

if(!($result=pg_query($connection,$sql)))
{
    print("Failed StyleQuery: " . pg_last_error($connection));
    exit;
}
while($row = pg_fetch_array($result))
{
    $storage_table_data=$row;
}
pg_free_result($row);
if($storage_table_data['exists'] === 'f')
{
	$sql = $sql = 'CREATE TABLE public.storage_table_data
			(
			  "id" SERIAL PRIMARY KEY,
			  "locationId" bigint,
			  "rack" character varying(30),
			  "row" character varying(30),
			  "shelf" character varying(30),
			  "type" character varying(30)
			)
			WITH (
			  OIDS=FALSE
			);
			ALTER TABLE public.storage_table_data
			  OWNER TO globaluniformuser';

	if(!($result=pg_query($connection,$sql)))
    {
        
        print_r('Application.php -- error in insert storage_table_data');
        print("Failed StyleQuery: " . pg_last_error($connection));
        exit();
    }
    else
    {
    	//print('successfully built the table
    }

    pg_free_result($result);

}

$sql = "SELECT EXISTS (SELECT column_name 
		FROM information_schema.columns 
		WHERE table_name='tbl_inventory' and column_name='location_details_id')";

$column_exists;
if(!($result=pg_query($connection,$sql)))
{
    print_r('Application.php -- error ');
    print("Failed StyleQuery: " . pg_last_error($connection));
    exit();
}
while($row = pg_fetch_array($result))
{
    $column_exists=$row;
}
pg_free_result($row);
if($column_exists['exists'] === 'f')
{
	$sql = 'ALTER TABLE "tbl_inventory" ADD COLUMN 
			location_details_id bigint ';
	// var_dump($sql);
	// exit();
	if(!($result=pg_query($connection,$sql)))
    {
        print_r('Application.php -- error ');
        print("Failed StyleQuery: " . pg_last_error($connection));
        exit();
    }
    else
    {
    	//print_r('successfully added column slot');
    	//exit();
    }
    pg_free_result($result);

}

$sql = "SELECT EXISTS (SELECT column_name 
		FROM information_schema.columns 
		WHERE table_name='tbl_invStorage' and column_name='unit')";

$column_exists;
if(!($result=pg_query($connection,$sql)))
{
    print_r('Application.php -- error ');
    print("Failed StyleQuery: " . pg_last_error($connection));
    exit();
}
while($row = pg_fetch_array($result))
{
    $column_exists=$row;
}
pg_free_result($row);
if($column_exists['exists'] === 'f')
{
	$sql = 'ALTER TABLE "tbl_invStorage" ADD COLUMN 
			unit character varying(30) ';
	// var_dump($sql);
	// exit();
	if(!($result=pg_query($connection,$sql)))
    {
        print_r('Application.php -- error ');
        print("Failed StyleQuery: " . pg_last_error($connection));
        exit();
    }
    else
    {
    	//print_r('successfully added column slot');
    	//exit();
    }
    pg_free_result($result);

}
//tbl_date_interval_setting create
$sql = "SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE  table_schema = 'public' AND table_name = 'tbl_date_interval_setting')";
$tbl_date_interval_setting_exists;

if(!($result=pg_query($connection,$sql)))
{
    print("Failed StyleQuery: " . pg_last_error($connection).$sql);
    exit;
}

while($row = pg_fetch_array($result))
{
    $tbl_date_interval_setting_exists=$row;
}
pg_free_result($row);
if($tbl_date_interval_setting_exists['exists'] === 'f')
{
	//var_dump('false');

	$sql = 'CREATE TABLE public.tbl_date_interval_setting
			(
			  "Colid" SERIAL PRIMARY KEY,
			  "color" text,
			  "interval" bigint DEFAULT 0,
			  "createdDate" bigint DEFAULT 0,
			  "updatedDate" bigint DEFAULT 0
			)
			WITH (
			  OIDS=FALSE
			);
			ALTER TABLE public.tbl_date_interval_setting
			  OWNER TO globaluniformuser';

	if(!($result=pg_query($connection,$sql)))
    {
        
        print_r('Application.php -- error in insert tbl_date_interval_setting');
        print("Failed StyleQuery: " . pg_last_error($connection));
        exit();
    }
    else
    {
    			$sql = '';
                $sql = "INSERT INTO \"tbl_date_interval_setting\" (";
                $sql .= " \"color\", \"interval\", \"createdDate\", \"updatedDate\" ";
                $sql .= " ) VALUES (";
                $sql .= " 'red' ";
                $sql .= ", '3'";
                $sql .= ", '". date('U') ."'";
                $sql .= ", '". date('U') ."'";
                $sql .= ")";
                //echo $sql;$return_arr['set']=
                if(!($audit = pg_query($connection,$sql))){
                    $return_arr['error'] = pg_last_error($connection);
                }

                $sql = '';
                $sql = "INSERT INTO \"tbl_date_interval_setting\" (";
                $sql .= " \"color\", \"interval\", \"createdDate\", \"updatedDate\" ";
                $sql .= " ) VALUES (";
                $sql .= " 'yellow' ";
                $sql .= ", '2'";
                $sql .= ", '". date('U') ."'";
                $sql .= ", '". date('U') ."'";
                $sql .= ")";
                //echo $sql;$return_arr['set']=
                if(!($audit = pg_query($connection,$sql))){
                    $return_arr['error'] = pg_last_error($connection);
                }

                $sql = '';
                $sql = "INSERT INTO \"tbl_date_interval_setting\" (";
                $sql .= " \"color\", \"interval\", \"createdDate\", \"updatedDate\" ";
                $sql .= " ) VALUES (";
                $sql .= " 'green' ";
                $sql .= ", '1'";
                $sql .= ", '". date('U') ."'";
                $sql .= ", '". date('U') ."'";
                $sql .= ")";
                //echo $sql;$return_arr['set']=
                if(!($audit = pg_query($connection,$sql))){
                    $return_arr['error'] = pg_last_error($connection);
                }
    	 		
    }
   pg_free_result($result); 
}
$sql = "UPDATE \"tbl_invStorage\" SET unit = box WHERE box != 'null'";
if(!($result=pg_query($connection,$sql)))
{
    print_r('Application.php -- error -- failed to update unit field with box value ');
    print("Failed StyleQuery: " . pg_last_error($connection));
    exit();
}

$sql = "UPDATE \"tbl_invStorage\" SET unit = slot WHERE slot != 'null'";
if(!($result=pg_query($connection,$sql)))
{
    print_r('Application.php -- error -- failed to update unit field with box value ');
    print("Failed StyleQuery: " . pg_last_error($connection));
    exit();
}



$sql = "SELECT EXISTS (SELECT column_name 
		FROM information_schema.columns 
		WHERE table_name='tbl_inventory' and column_name='oldinv')";

$column_exists;
if(!($result=pg_query($connection,$sql)))
{
    print_r('Application.php -- error ');
    print("Failed StyleQuery: " . pg_last_error($connection));
    exit();
}
while($row = pg_fetch_array($result))
{
    $column_exists=$row;
}
pg_free_result($row);
if($column_exists['exists'] === 'f')
{
	$sql = 'ALTER TABLE "tbl_inventory" ADD COLUMN 
			oldinv bigint';
	// var_dump($sql);
	// exit();
	if(!($result=pg_query($connection,$sql)))
    {
        print_r('Application.php -- error ');
        print("Failed StyleQuery: " . pg_last_error($connection));
        exit();
    }
    else
    {
    	//print_r('successfully added column slot');
    	//exit();
    }
    pg_free_result($result);

}
$sql = "SELECT EXISTS (SELECT column_name 
		FROM information_schema.columns 
		WHERE table_name='tbl_invStorage' and column_name='oldinv')";

$column_exists;
if(!($result=pg_query($connection,$sql)))
{
    print_r('Application.php -- error ');
    print("Failed StyleQuery: " . pg_last_error($connection));
    exit();
}
while($row = pg_fetch_array($result))
{
    $column_exists=$row;
}
pg_free_result($row);
if($column_exists['exists'] === 'f')
{
	 $sql = 'ALTER TABLE "tbl_invStorage" ADD COLUMN 
			oldinv bigint';
	// var_dump($sql);
	// exit();
	if(!($result=pg_query($connection,$sql)))
    {
        print_r('Application.php -- error ');
        print("Failed StyleQuery: " . pg_last_error($connection));
        exit();
    }
    else
    {
    	print_r('successfully added column slot');
    	exit();
    }
    pg_free_result($result);

}

$sql = "SELECT EXISTS (SELECT column_name 
		FROM information_schema.columns 
		WHERE table_name='tbl_log_updates' and column_name='warehouse')";

$column_exists;
if(!($result=pg_query($connection,$sql)))
{
    print_r('Application.php -- error ');
    print("Failed StyleQuery: " . pg_last_error($connection));
    exit();
}
while($row = pg_fetch_array($result))
{
    $column_exists=$row;
}
pg_free_result($row);
if($column_exists['exists'] === 'f')
{
	 $sql = 'ALTER TABLE "tbl_log_updates" ADD COLUMN 
			warehouse character varying(30)';
	// var_dump($sql);
	// exit();
	if(!($result=pg_query($connection,$sql)))
    {
        print_r('Application.php -- error ');
        print("Failed StyleQuery: " . pg_last_error($connection));
        exit();
    }
    else
    {
    	print_r('successfully added column slot');
    	exit();
    }
    pg_free_result($result);

}

// CREATE TABLE "locationDetails" (
//     id integer NOT NULL,
//     "locationId" character varying(30),
//     warehouse character varying(50),
//     container character varying(50),
//     conveyor character varying(50)
// );


$sql = "SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE  table_schema = 'public' AND table_name = 'locationDetails')";

$locationDetails_data;

if(!($result=pg_query($connection,$sql)))
{
    print("Failed StyleQuery: " . pg_last_error($connection));
    exit;
}
while($row = pg_fetch_array($result))
{
    $locationDetails_data=$row;
}
pg_free_result($row);
if($locationDetails_data['exists'] === 'f')
{
	$sql =  'CREATE TABLE public."locationDetails"
			(
			  "id" SERIAL PRIMARY KEY,
			  "locationId" character varying(50),
			  "warehouse" character varying(50),
			  "container" character varying(50),
			  "conveyor" character varying(50)
			)
			WITH (
			  OIDS=FALSE
			);
			ALTER TABLE public.locationDetails
			  OWNER TO globaluniformuser';

	if(!($result=pg_query($connection,$sql)))
    {
        
        print_r('Application.php -- error in insert locationDetails');
        print("Failed StyleQuery: " . pg_last_error($connection));
        exit();
    }
    else
    {
    	//print('successfully built the table
    }

    pg_free_result($result);

}

$sql = "SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE  table_schema = 'public' AND table_name = 'tbl_invLocation')";

$tbl_invLocation_data;

if(!($result=pg_query($connection,$sql)))
{
    print("Failed StyleQuery: " . pg_last_error($connection));
    exit;
}
while($row = pg_fetch_array($result))
{
    $tbl_invLocation_data=$row;
}
pg_free_result($row);
if($tbl_invLocation_data['exists'] === 'f')
{
	$sql = 'CREATE TABLE public."tbl_invLocation"
			(
				"locationId" bigint DEFAULT nextval((\'tbl_invLocation_locationId_seq\'::text)::regclass) NOT NULL,
				name character varying(200),
				"isActive" smallint DEFAULT 1,
				identifier character varying(10)
			)
			WITH (
			  OIDS=FALSE
			);
			ALTER TABLE public."tbl_invLocation"
			  OWNER TO globaluniformuser';

	if(!($result=pg_query($connection,$sql)))
    {
        
        print_r('Application.php -- error in insert tbl_invLocation');
        print("Failed StyleQuery: " . pg_last_error($connection));
        exit();
    }
    else
    {
    	//print('successfully built the table
    }

    pg_free_result($result);

}


// CREATE TABLE "tbl_invLocation" (
//     "locationId" bigint DEFAULT nextval(('tbl_invLocation_locationId_seq'::text)::regclass) NOT NULL,
//     name character varying(200),
//     "isActive" smallint DEFAULT 1,
//     identifier character varying(10)
// );

?>
