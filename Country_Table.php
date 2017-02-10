<html>
<head>
<title>Europe: Les Pays</title>
<link rel="stylesheet" type="text/css" href="CSS/design.css">
</head>
<body>
<?php 
session_start();



/* ################  Change the state   ###############*/
if( $_POST ){
	
	$k = $_POST['k'];
	$criteria = $_POST['cri'];
	$_SESSION['cri']=$criteria;
	$_SESSION['k']=$k;
	$_SESSION['offset']=0;	
	echo " <h3>de: ".$_SESSION['offset']." a :".($_SESSION['offset']+$_SESSION['k'])."</h3>" ;
	
	
}
 else{ 
 	
if(isset($_GET['next'])){
	if($_SESSION['offset']<40){
	$_SESSION['offset']+=$_SESSION['k'];
	 echo " <h3>de :".$_SESSION['offset']."    a :".($_SESSION['offset']+$_SESSION['k'])."  </h3>" ;}
	else {
		echo "<h3 style='color:#ee6419;' >pas d'autre pays</h3>";}
}
	
	else {
		
		if(isset($_GET['prev'])){
			if($_SESSION['offset']!=0){
			$_SESSION['offset']-=$_SESSION['k'];
			echo " <h3>de: ".$_SESSION['offset']." a :".($_SESSION['offset']+$_SESSION['k'])."</h3>" ;}
			else {
				echo "<h3  style='color:#ee6419;' >la liste principale</h3>";
			}
		
	    }
	}
	
 }
 


include_once ('semsol/ARC2.php');

$dbpconfig = array (
		"remote_store_endpoint" => "http://dbpedia.org/sparql"
);

$store = ARC2::getRemoteStore ( $dbpconfig );

if ($errs = $store->getErrors ()) {
	echo "<h1>getRemoteStore error<h1>";
}

/* ########### GET the new state #############*/
$k=$_SESSION['k'];
$criteria=$_SESSION['cri'];
$offset=$_SESSION['offset'];


$prefix = 'PREFIX dbo: <http://dbpedia.org/ontology/>
PREFIX dbpedia: <http://dbpedia.org/resource/>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX dbp:  <http://dbpedia.org/property/>
PREFIX dbc: <http://dbpedia.org/resource/Category:>
PREFIX purl: <http://purl.org/dc/terms/>
PREFIX skos:<http://www.w3.org/2004/02/skos/core#>';


$query=$prefix.'
select distinct ?country ?cou avg(?population) AS ?populationDensity ?area ?cap

where{

?country purl:subject dbc:Countries_in_Europe.


?country dbo:populationDensity ?population  .
?country dbp:areaKm ?area .

?country  dbo:capital ?capital .
?capital rdfs:label ?cap .
?country rdfs:label ?cou .
filter(lang(?cap)="en" && lang(?cou)="en" ). 
}ORDER BY DESC (?'.$criteria.')
		OFFSET '.$offset.'
			LIMIT '.$k ;


	
	$rows = $store->query ( $query, 'rows' );
	if ($errs = $store->getErrors ()) {
		echo "Query errors";
		print_r ( $errs );
	}
	
	echo "<table id='table'><thead><tr><th>Country</th><th>Capital</th><th>Area (Km)</th><th>population Density</th><th>City/PopulatedPlace</th><th>Leaders</th></tr></thead>";
	
	foreach ($rows as $row)
	{
		echo "<tr><td><b>".$row['cou']."</b></td><td>".$row['cap']."</td><td>".$row['area']."</td><td>".number_format($row['populationDensity'], 2, '.', '')."</td><td><select id='city' size='5'>";
	
		
		$query2=$prefix.'SELECT distinct ?citylabel

WHERE {
?city rdf:type ?x . filter(?x=dbo:Settlement || ?x=dbo:PopulatedPlace).
?city dbo:country <'.$row['country'].'>.
?city dbo:populationTotal ?pop .
?city rdfs:label ?citylabel.
FILTER (lang(?citylabel)="en").  
FILTER(?pop>10000).
}
LIMIT 10';
		
		$subrows = $store->query ( $query2, 'rows' );
		
		foreach ($subrows as $subrow)
		{
		  echo "<option>".$subrow['citylabel']."</option>";
		}
		
		echo"</select></td><td><ul>";
		
		
		$query3=$prefix.'
SELECT  ?leaderN ?leader
WHERE { <'.$row['country'].'> dbo:leader ?leader.
?leader rdfs:label ?leaderN.
FILTER (lang(?leaderN)="en") .
}';
			
$subrows = $store->query ( $query3, 'rows' );

foreach ($subrows as $subrow)
{
echo '<li><a href="'.$subrow['leader'].'">'.$subrow['leaderN'].'</a></li>';
	
}
		echo"</ul></td></tr>";
	}
echo "</table>";
              		?>
              		
              		</body>
</html>
              		