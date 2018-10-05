<!doctype html>
<html>
<head>
		<link rel="stylesheet" type="text/css" href="styles.css">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
		<script type="text/javascript" src="/media/js/site.js?_=5e8f232afab336abc1a1b65046a73460"></script>
		<script type="text/javascript" src="/media/js/dynamic.php?comments-page=examples%2Fdata_sources%2Fdom.html" async></script>
		<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
		<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" language="javascript" src="../resources/demo.js"></script>
		<script type="text/javascript" class="init">		
			$(document).ready( function() {
				$('#subjectTable').DataTable({
					"pageLength": 50,
					"scrollX": true
				});
			} );

		</script>

</head>

<body>
	<?php
		$dbcon = mysqli_connect("localhost","root","root","virus");
		function table_head(){
				echo "<div class=\"tablediv\">
						<table id=\"subjectTable\" width=\"100%\" class=\"display\">
						<thead>
							<tr>
								<td>Query</td>
								<td>Subject</td>
								<td>Percentage</td>
								<td>Alignment Length</td>
								<td>E-Value</td>
								<td>Query Taxid</td>
								<td>Subject Taxid</td>
								<td>Query HTML</td>
								<td>Subject HTML</td>
							</tr>
						</thead>
						<tbody>";
			}
		$host = $_GET['host'];
		$subject = $_GET['quer'];
		
		table_head();
		$query = "SELECT Query, Subject, Percentage, Alignment_Length, EValue, Query_Taxid, Subject_Taxid, Query_HTML, Subject_HTML FROM $host_new" . $host . "_worst WHERE Subject = \"$subject\"";
		echo $query;
		$result = mysqli_query($dbcon, $query);
		if(mysqli_num_rows($result) == 0){
			echo "<h3>No bad hits found for specified subject</h3>";
		}
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			echo "\r\n<tr>";
					echo "<td>" . $row['Query'] . "</td>";
					echo "<td>" . $row['Subject'] . "</td>";
					echo "<td>" . $row['Percentage'] . "</td>";
					echo "<td>" . $row['Alignment_Length'] . "</td>";
					echo "<td>" . $row['EValue'] . "</td>";
					echo "<td>" . $row['Query_Taxid'] . "</td>";
					echo "<td>" . $row['Subject_Taxid'] . "</td>";
					echo "<td><a href=\"" . $row['Query_HTML'] . "\" target=\"_blank\">" . explode("/", $row['Query_HTML'])[4]."</a></td>";
					echo "<td><a href=\"" . $row['Subject_HTML'] . "\" target=\"_blank\">" . explode("/", $row['Subject_HTML'])[4]."</a></td>";	
					echo "</tr>";
		}
		echo "</tbody>";
		echo "</table>";
	?>
</body>


</html>