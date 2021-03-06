<?php
	include('../lib_timezones.php');

	header("Content-type: text/plain");

	date_default_timezone_set('America/Los_Angeles');

	$summer = date_create();
	$winter = date_create();

	date_timestamp_set($summer, gmmktime(0,0,0,6,30,2012));
	date_timestamp_set($winter, gmmktime(0,0,0,12,30,2012));

	$zones = timezones_list();
	$map = array();

	foreach ($zones as $row){

		$tz = timezone_open($row[1]);
		date_timezone_set($summer, $tz);
		date_timezone_set($winter, $tz);

		$so = date_offset_get($summer) / 60;
		$wo = date_offset_get($winter) / 60;

		$key = $so.':'.$wo;
		if ($row[2]){
			$map[$key] = $row[1];
		}else{
			if (!array_key_exists($key, $map)) $map[$key] = $row[1];
		}
	}


	$lines = file('timezones.js.template');
	echo implode('', $lines);


	# do our own pretty printing to this works
	# on older PHP.

	$map_lines = array();
	foreach ($map as $k => $v) $map_lines[] = "\t'$k':\t'$v'";

	$zone_lines = array();
	foreach ($zones as $row) $zone_lines[] = "\t[\"{$row[0]}\", '{$row[1]}']";

	echo "\nvar _timezones_map = {\n".implode(",\n", $map_lines)."\n};\n";
	echo "\nvar _timezones_list = [\n".implode(",\n", $zone_lines)."\n];\n";
