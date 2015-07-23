<?php
	session_start();

	$hostName	= 'test.net';
	$https		= (isset($_SERVER['HTTPS'])) ? $_SERVER['HTTPS'] : 'off';
	if(strtolower($https) != 'on')
	{
		header('HTTP/1.1 301 Moved Permanently');
		header('Location: https://' . $hostName . '/server-control');
		exit;
	}

	$isLoggedIn		= false;
	$isLogginError	= false;
	
	if(isset($_GET['login']))
	{
		if($_GET['login'] == 'true')
		{
			if(isset($_POST['user']) && isset($_POST['password']))
			{
				if($_POST['user'] == 'admin' and $_POST['password'] == 'some_pass')
				{
					$_SESSION['LoggedId']	= time() + 60 * 60; // 1 hour session
				}else{
					$isLogginError			= false;
				}
			}
		}
	}

	if(isset($_GET['phpinfo']))
	{
		if($_GET['phpinfo'] == 'true')
		{
			phpinfo();
			exit;
		}
	}

	if(isset($_GET['opcacheReset']))
	{
		if($_GET['opcacheReset'] == 'true')
		{
			opcache_reset();
			header('Refresh:5; url=https://' . $hostName . '/server-control', true, 303);
			echo 'Opcache reset successful.';
			exit;
		}

	}
	
	if(isset($_SESSION['LoggedId']))
	{
		if($_SESSION['LoggedId'] >= time())
		{
			$isLoggedIn	= true;
		}
	}

	if($isLoggedIn)
	{
		echo <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!--
	\$Id\$
	(c) 2011 Jerome Loyet
	The PHP License, version 3.01
	This is sample real-time status page for FPM. You can change it to better feet your needs.
-->
	<head> 
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<style type="text/css"> 
			body {background-color: #ffffff; color: #000000;}
			body, td, th, h1, h2 {font-family: sans-serif;}
			pre {margin: 0px; font-family: monospace;}
			a:link {color: #000099; text-decoration: none; background-color: #ffffff;}
			a:hover {text-decoration: underline;}
			table {border-collapse: collapse;}
			.center {text-align: center;}
			.center table { margin-left: auto; margin-right: auto; text-align: left;}
			.center th { text-align: center !important; }
			td, th { border: 1px solid #000000; font-size: 75%; vertical-align: baseline;}
			h1 {font-size: 150%;}
			h2 {font-size: 125%;}
			.p {text-align: left;}
			.e {background-color: #ccccff; font-weight: bold; color: #000000;}
			.h {background-color: #9999cc; font-weight: bold; color: #000000;}

			.v {background-color: #cccccc; color: #000000;}
			.w {background-color: #ccccff; color: #000000;}

			.h th {
				cursor: pointer;
			}
			img {float: right; border: 0px;}
			hr {width: 600px; background-color: #cccccc; border: 0px; height: 1px; color: #000000;}
		</style> 
	<title>PHP-FPM status page</title>
	<meta name="ROBOTS" content="NOINDEX,NOFOLLOW,NOARCHIVE" /></head> 
	<body>
		<div class="center">
			<table border="0" cellpadding="3" width="95%">
				<tr class="h">
					<td> 
						<a href="http://www.php.net/"><img border="0" src="https://static.php.net/www.php.net/images/php.gif" alt="PHP Logo" /></a><h1 class="p">PHP-FPM real-time status page</h1>
					</td>
				</tr>
			</table>
			<br />
			<table border="0" cellpadding="3" width="95%">
				<tr><td class="e">Status URL</td><td class="v"><input type="text" id="url" size="45" /></td></tr> 
				<tr><td class="e">Ajax status</td><td class="v" id="status"></td></tr> 
				<tr><td class="e">Refresh Rate</td><td class="v"><input type="text" id="rate" value="1" /></td></tr> 
				<tr>
					<td class="e">Actions</td>
					<td class="v">
						<button onclick="javascript:refresh();">Manual Refresh</button>
						<button id="play" onclick="javascript:playpause();">Play</button>
					</td>
				</tr> 
			</table>
			<h1>Pool Status</h1> 
			<table border="0" cellpadding="3" width="95%" id="short">
				<tr style="display: none;"><td>&nbsp;</td></tr>
			</table>
			<h1>Active Processes status</h1> 
			<table border="0" cellpadding="3" width="95%" id="active">
				<tr class="h"><th>PID&darr;</th><th>Start Time</th><th>Start Since</th><th>Requests Served</th><th>Request Duration</th><th>Request method</th><th>Request URI</th><th>Content Length</th><th>User</th><th>Script</th></tr>
			</table>
			<h1>Idle Processes status</h1> 
			<table border="0" cellpadding="3" width="95%" id="idle">
				<tr class="h"><th>PID&darr;</th><th>Start Time</th><th>Start Since</th><th>Requests Served</th><th>Request Duration</th><th>Request method</th><th>Request URI</th><th>Content Length</th><th>User</th><th>Script</th><th>Last Request %CPU</th><th>Last Request Memory</th></tr>
			</table>
			<h1>Other Controls</h1>
			<table border="0" cellpadding="3" width="95%">
				<tbody><tr class="e">
					<td>
						<a href="https://{$hostName}/server-control/poweradmin-2.1.7/index.php" target="_blank">DNS Manager</a>
					</td>
					<td>
						<a href="https://{$hostName}/server-control/index.php?phpinfo=true" target="_blank">PHP Info</a>
					</td>
					<td>
						<a href="https://{$hostName}/server-control/index.php?opcacheReset=true" target="_blank">Reset Opcache</a>
					</td>
					<td>
						<a href="https://{$hostName}:666" target="_blank">Server Manager</a>
					</td>
				</tr>
			</tbody></table>
		</div>

		<script type="text/javascript">
<!--
			var xhr_object = null;
			var doc_url = document.getElementById("url");
			var doc_rate = document.getElementById("rate");
			var doc_status = document.getElementById("status");
			var doc_play = document.getElementById("play");
			var doc_short = document.getElementById("short");
			var doc_active = document.getElementById("active");
			var doc_idle = document.getElementById("idle");
			var rate = 0;
			var play=0;
			var delay = 1000;
			var order_active_index = 0;
			var order_active_reverse = 0;
			var order_idle_index = 0;
			var order_idle_reverse = 0;
			var sort_index;
			var sort_order;

			doc_url.value = location.protocol + '//' + location.host + "/server-status?json&full";

			ths = document.getElementsByTagName("th");
			for (var i=0; i<ths.length; i++) {
				var th = ths[i];
				if (th.parentNode.className == "h") {
					th.onclick = function() { order(this); return false; };
				}
			}

			xhr_object = create_ajax();

			function create_ajax() {
				if (window.XMLHttpRequest) {
					return new XMLHttpRequest();
				}
				var names = [
					"Msxml2.XMLHTTP.6.0",
					"Msxml2.XMLHTTP.3.0",
					"Msxml2.XMLHTTP",
					"Microsoft.XMLHTTP"
				];
				for(var i in names)
				{
					try {
						return new ActiveXObject(names[i]);
					}	catch(e){}
				}
				alert("Browser not compatible ...");
			}

			function order(cell) {
				var table;

				if (cell.constructor != HTMLTableCellElement && cell.constructor != HTMLTableHeaderCellElement) {
					return;
				}

				table = cell.parentNode.parentNode.parentNode;

				if (table == doc_active) {
					if (order_active_index == cell.cellIndex) {
						if (order_active_reverse == 0) {
							cell.innerHTML = cell.innerHTML.replace(/.$/, "&uarr;");
							order_active_reverse = 1;
						} else {
							cell.innerHTML = cell.innerHTML.replace(/.$/, "&darr;");
							order_active_reverse = 0;
						}
					} else {
						var c = doc_active.rows[0].cells[order_active_index];
						c.innerHTML = c.innerHTML.replace(/.$/, "");
						cell.innerHTML = cell.innerHTML.replace(/$/, order_active_reverse == 0 ? "&darr;" : "&uarr;");
						order_active_index = cell.cellIndex;
					}
					reorder(table, order_active_index, order_active_reverse);
					return;
				}

				if (table == doc_idle) {
					if (order_idle_index == cell.cellIndex) {
						if (order_idle_reverse == 0) {
							cell.innerHTML = cell.innerHTML.replace(/.$/, "&uarr;");
							order_idle_reverse = 1;
						} else {
							cell.innerHTML = cell.innerHTML.replace(/.$/, "&darr;");
							order_idle_reverse = 0;
						}
					} else {
						var c = doc_idle.rows[0].cells[order_idle_index];
						c.innerHTML = c.innerHTML.replace(/.$/, "");
						cell.innerHTML = cell.innerHTML.replace(/$/, order_idle_reverse == 0 ? "&darr;" : "&uarr;");
						order_idle_index = cell.cellIndex;
					}
					reorder(table, order_idle_index, order_idle_reverse);
					return;
				}
			}

			function reorder(table, index, order) {
				var rows = [];
				while (table.rows.length > 1) {
					rows.push(table.rows[1]);
					table.deleteRow(1);
				}
				sort_index = index;
				sort_order = order;
				rows.sort(sort_table);
				for (var i in rows) {
					table.appendChild(rows[i]);
				}
				var odd = 1;
				for (var i=1; i<table.rows.length; i++) {
					table.rows[i].className = odd++ % 2 == 0 ? "v" : "w";
				}
				return;
			}

			function sort_table(a, b) {
				if (a.cells[0].tagName == "TH") return -1;
				if (b.cells[0].tagName == "TH") return 1;

				if (a.cells[sort_index].__search_t == 0) { /* integer */
					if (!sort_order) return a.cells[sort_index].__search_v - b.cells[sort_index].__search_v;
					return b.cells[sort_index].__search_v - a.cells[sort_index].__search_v;;
				}

				/* string */
				if (!sort_order) return a.cells[sort_index].__search_v.localeCompare(b.cells[sort_index].__search_v);
				else return b.cells[sort_index].__search_v.localeCompare(a.cells[sort_index].__search_v);
			}

			function playpause() {
				rate = 0;
				if (play) {
					play = 0;
					doc_play.innerHTML = "Play";
					doc_rate.disabled = false;
				} else {
					delay = parseInt(doc_rate.value);
					if (!delay || delay < 1) {
						doc_status.innerHTML = "Not valid 'refresh' value";
						return;
					}
					play = 1;
					doc_rate.disabled = true;
					doc_play.innerHTML = "Pause";
					setTimeout("callback()", delay * 1000);
				}
			}

			function refresh() {
				if (xhr_object == null) return;
				if (xhr_object.readyState > 0 && xhr_object.readyState < 4) {
					return; /* request is running */
				}
				xhr_object.open("GET", doc_url.value, true);
				xhr_object.onreadystatechange = function() {
					switch(xhr_object.readyState) {
						case 0:
							doc_status.innerHTML = "uninitialized";
							break;
						case 1:
							doc_status.innerHTML = "loading ...";
							break;
						case 2:
							doc_status.innerHTML = "loaded";
							break;
						case 3:
							doc_status.innerHTML = "interactive";
							break;
						case 4:
							doc_status.innerHTML = "complete";
							if (xhr_object.status == 200) {
								fpm_status(xhr_object.responseText);
							} else {
								doc_status.innerHTML = "Error " + xhr_object.status;
							}
							break;
					}
				}
				xhr_object.send();
			}

			function callback() {
				if (!play) return;
				refresh();
				setTimeout("callback()", delay * 1000);
			}

			function fpm_status(txt) {
				var json = null;

				while (doc_short.rows.length > 0) {
					doc_short.deleteRow(0);
				}

				while (doc_active.rows.length > 1) {
					doc_active.deleteRow(1);
				}

				while (doc_idle.rows.length > 1) {
					doc_idle.deleteRow(1);
				}

				try {
					json = JSON.parse(txt);
				} catch (e) {
					doc_status.innerHTML =  "Error while parsing json: '" + e + "': <br /><pre>" + txt + "</pre>";
					return;
				}

				for (var key in json) {
					if (key == "processes") continue;
					if (key == "state") continue;
					var row = doc_short.insertRow(doc_short.rows.length);
					var value = json[key];
					if (key == "start time") {
						value = new Date(value * 1000).toLocaleString();
					}
					if (key == "start since") {
						value = time_s(value);
					}
					var cell = row.insertCell(row.cells.length);
					cell.className = "e";
					cell.innerHTML = key;

					cell = row.insertCell(row.cells.length);
					cell.className = "v";
					cell.innerHTML = value;
				}

				if (json.processes) {
					process_full(json.processes, doc_active, "Idle", 0, 0);
					reorder(doc_active, order_active_index, order_active_reverse);

					process_full(json.processes, doc_idle, "Idle", 1, 1);
					reorder(doc_idle, order_idle_index, order_idle_reverse);
				}
			}

			function process_full(processes, table, state, equal, cpumem) {
				var odd = 1;

				for (var i in processes) {
					var proc = processes[i];
					if ((equal && proc.state == state) || (!equal && proc.state != state)) {
						var c = odd++ % 2 == 0 ? "v" : "w";
						var row = table.insertRow(-1);
						row.className = c;
						row.insertCell(-1).innerHTML = proc.pid;
						row.cells[row.cells.length - 1].__search_v = proc.pid;
						row.cells[row.cells.length - 1].__search_t = 0;

						row.insertCell(-1).innerHTML = date(proc['start time'] * 1000);;
						row.cells[row.cells.length - 1].__search_v = proc['start time'];
						row.cells[row.cells.length - 1].__search_t = 0;

						row.insertCell(-1).innerHTML = time_s(proc['start since']);
						row.cells[row.cells.length - 1].__search_v = proc['start since'];
						row.cells[row.cells.length - 1].__search_t = 0;

						row.insertCell(-1).innerHTML = proc.requests;
						row.cells[row.cells.length - 1].__search_v = proc.requests;
						row.cells[row.cells.length - 1].__search_t = 0;

						row.insertCell(-1).innerHTML = time_u(proc['request duration']);
						row.cells[row.cells.length - 1].__search_v = proc['request duration'];
						row.cells[row.cells.length - 1].__search_t = 0;

						row.insertCell(-1).innerHTML = proc['request method'];
						row.cells[row.cells.length - 1].__search_v = proc['request method'];
						row.cells[row.cells.length - 1].__search_t = 1;

						row.insertCell(-1).innerHTML = proc['request uri'];
						row.cells[row.cells.length - 1].__search_v = proc['request uri'];
						row.cells[row.cells.length - 1].__search_t = 1;

						row.insertCell(-1).innerHTML = proc['content length'];
						row.cells[row.cells.length - 1].__search_v = proc['content length'];
						row.cells[row.cells.length - 1].__search_t = 0;

						row.insertCell(-1).innerHTML = proc.user;
						row.cells[row.cells.length - 1].__search_v = proc.user;
						row.cells[row.cells.length - 1].__search_t = 1;

						row.insertCell(-1).innerHTML = proc.script;
						row.cells[row.cells.length - 1].__search_v = proc.script;
						row.cells[row.cells.length - 1].__search_t = 1;

						if (cpumem) {
							row.insertCell(-1).innerHTML = cpu(proc['last request cpu']);
							row.cells[row.cells.length - 1].__search_v = proc['last request cpu'];
							row.cells[row.cells.length - 1].__search_t = 0;

							row.insertCell(-1).innerHTML = memory(proc['last request memory']);
							row.cells[row.cells.length - 1].__search_v = proc['last request memory'];
							row.cells[row.cells.length - 1].__search_t = 0;
						}
					}
				}
			}

			function date(d) {
				var t = new Date(d);
				var r = "";

				r += (t.getDate() < 10 ? '0' : '') + t.getDate();
				r += '/';
				r += (t.getMonth() + 1 < 10 ? '0' : '') + (t.getMonth() + 1);
				r += '/';
				r += t.getFullYear();
				r += ' ';
				r += (t.getHours() < 10 ? '0' : '') + t.getHours();
				r += ':';
				r += (t.getMinutes() < 10 ? '0' : '') + t.getMinutes();
				r += ':';
				r += (t.getSeconds() < 10 ? '0' : '') + t.getSeconds();


				return r;
			}

			function cpu(c) {
				if (c == 0) return 0;
				return c + "%";
			}

			function memory(mem) {
				if (mem == 0) return 0;
				if (mem < 1024) {
					return mem + "B";
				}
				if (mem < 1024 * 1024) {
					return mem/1024 + "KB";
				}
				if (mem < 1024*1024*1024) {
					return mem/1024/1024 + "MB";
				}
			}

			function time_s(t) {
				var r = "";
				if (t < 60) {
					return t + 's';
				}

				r = (t % 60) + 's';
				t = Math.floor(t / 60);
				if (t < 60) {
					return t + 'm ' + r;
				}

				r = (t % 60) + 'm ' + r;
				t = Math.floor(t/60);

				if (t < 24) {
					return t + 'h ' + r;
				}

				return Math.floor(t/24) + 'd ' + (t % 24) + 'h ' + t;
			}

			function time_u(t) {
				var r = "";
				if (t < 1000) {
					return t + '&micro;s'
				}

				r = (t % 1000) + '&micro;s';
				t = Math.floor(t / 1000);
				if (t < 1000) {
					return t + 'ms ' + r;
				}

				return time_s(Math.floor(t/1000)) + ' ' + (t%1000) + 'ms ' + r;
			}
-->
		</script>
	</body>
</html>
EOF;

	}else{
		echo <<<EOF
<!doctype html>
<html>
	<head>
		<meta charset="utf-8" />
		<style type="text/css"> 
			body {background-color: #ffffff; color: #000000;}
			body, td, th, h1, h2 {font-family: sans-serif;}
			pre {margin: 0px; font-family: monospace;}
			a:link {color: #000099; text-decoration: none; background-color: #ffffff;}
			a:hover {text-decoration: underline;}
			table {border-collapse: collapse;}
			.center {text-align: center;}
			.center table { margin-left: auto; margin-right: auto; text-align: left;}
			.center th { text-align: center !important; }
			td, th { border: 1px solid #000000; font-size: 75%; vertical-align: baseline;}
			h1 {font-size: 150%;}
			h2 {font-size: 125%;}
			.p {text-align: left;}
			.e {background-color: #ccccff; font-weight: bold; color: #000000;}
			.h {background-color: #9999cc; font-weight: bold; color: #000000;}

			.v {background-color: #cccccc; color: #000000;}
			.w {background-color: #ccccff; color: #000000;}

			.h th {
				cursor: pointer;
			}
			img {float: right; border: 0px;}
			hr {width: 600px; background-color: #cccccc; border: 0px; height: 1px; color: #000000;}
		</style>
	</head>
	<body>
		<div class="center">
			<form method="POST" action="?login=true">
			<table border="0" cellpadding="3" width="95%">
EOF;
				if($isLogginError)
				{
					echo '<tr><td class="e" colspan="2" rowspan="1">Invalid username or password</td></tr>';
				}
echo <<<EOF
				<tr><td class="e">User</td><td class="v"><input type="text" name="user" size="45" /></td></tr> 
				<tr><td class="e">Password</td><td class="v"><input type="password" name="password" size="45" /></td></tr> 
				<tr><td class="e" colspan="2" rowspan="1"><input type="submit" name="login" value="Login" size="45" /></td></tr> 
			</table>
			</form>
		</div>
	</body>
</html>
EOF;
	}