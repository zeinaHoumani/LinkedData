<html>
<head>
<title>Europe: Les Pays</title>
<link rel="stylesheet" type="text/css" href="CSS/design.css">

<script src="jQuery/jquery.js"></script> 
<script src="jQuery/My_jquery.js"></script> 
 
</head>

<body>

	<div id="block">
		<img id="main" src="Images/pays.jpg" height="230" alt="image can't be loaded" />

 <?php
 
 /** Used for the Functions PREVIOUS  NEXT **/
 session_start();
 $_SESSION['k'] = 10;
 $_SESSION['cri'] = "area";
 $_SESSION['offset']=0;

     /* ARC2 static class inclusion :
	 *  provides a RemoteStore component which makes it possible to work with SPARQL endpoints as if they were local stores
	 *  */
 
	include_once ('semsol/ARC2.php');
	
	$dbpconfig = array (
			"remote_store_endpoint" => "http://dbpedia.org/sparql" 
	);
	
	$store = ARC2::getRemoteStore ( $dbpconfig );
	
	if ($errs = $store->getErrors ()) {
		echo "<h1>getRemoteStore error<h1>";
	}
	
	/* ##################### Prefix to all the queries used is this project ####################### */
	
	$prefix = 'PREFIX dbo: <http://dbpedia.org/ontology/>
PREFIX dbpedia: <http://dbpedia.org/resource/>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX dbp:  <http://dbpedia.org/property/>
PREFIX dbc: <http://dbpedia.org/resource/Category:>
PREFIX purl: <http://purl.org/dc/terms/>
PREFIX skos:<http://www.w3.org/2004/02/skos/core#>';
	
	/* ##################### Definition ####################### */
	
	$query = $prefix . '
SELECT ?x ?label
WHERE {
dbpedia:Europe dbo:abstract ?x .
dbpedia:Europe rdfs:label ?label .
Filter( lang(?x)="fr" && lang(?label)="en") .

}';
	
	/* execute the query */
	$rows = $store->query ( $query, 'rows' );
	
	if ($errs = $store->getErrors ()) {
		echo "Query errors";
		print_r ( $errs );
	}
	
	echo "<div >";
	
	echo "<div id='def' >";
	
	foreach ( $rows as $row ) {
		echo "<h1>" . $row ['label'] . ":</h1> <p>" . $row ['x'] . "</p>";
	}
	;
	
	echo "</div>";
	
	/* ##################### Info Box ####################### */
	
	$query = $prefix . '

SELECT COUNT(*) AS ?count ?type ?population ?demonym ?area ?lang
WHERE {
dbpedia:Europe a ?x .
?x rdfs:label ?type .
dbpedia:Europe dbo:populationTotal ?population .
dbpedia:Europe dbo:demonym ?demonym .
dbpedia:Europe dbo:areaTotal ?area .
dbpedia:Europe dbp:languages ?lang .
Filter( str(?type)="continent" && lang(?type)="en")
}';
	
	$rows = $store->query ( $query, 'rows' );
	if ($errs = $store->getErrors ()) {
		echo "Query errors";
		print_r ( $errs );
	}
	

	echo "	<div id='info' > <h2>Info:</h2> <ul>";
	
	foreach ( $rows as $row ) {
		echo "<li> C'est une: " . $row ['type'] . "</li>" . "<li> Population Total: " . $row ['population'] . "</li>" . 
		"<li> Surface: " . $row ['area'] . "</li>" . "<li> Langages: " . $row ['lang'] . "</li>" . 
		"<li> R&eacutesidents: " . $row ['demonym'] . "</li>";
	}
	
	echo "</ul></div>";
	echo "</div>";
	
	
	
	/* ##################################################### List of Countries ################################################## */
	
	
	echo ' <div id="a">';
	
	echo '<h2>Liste des pays europ&eacuteens:</h2>';

		
	
	/* ########################## Eastern Europe ############################*/
	
	echo "<div id='region1'>";
	echo "<h3>Eastern Europe</h3>
		 		<ul id='east'>";
	
	
	/** PROBLEMs FACED:**/
	/* 
	 * 1) The catastrophe : ARC2 library does NOT SUPPORT SPARQL1.1 that's mean we can't use MINUS OPERATOR and NOT EXISTS !!  "I hate my life"
	 *
	 * So the correct query bellow can't be used with ARC2 -> we create a SPARQL1.0 query To solve this problem
	 * 
	 * 2) The query return old result like Byzantine Empire ! that's why we used the property dissolutionYear 
	 * 
	 * 3) in dbpedia: Baltic_states (Estonia, Latvia and Lithuania) are not mentioned in Eastern Europe or any other region So we add it seperatly
	 * 
	 * 4) The Second Minus is because on dbpedia there are some countries mentioned in  central Europe and Western/eastern at the same time !!
		
		$query=$prefix.'
		
		select distinct ?country ?name
		 
		where{
		
			?country a dbo:Country .
			?country rdfs:label ?name .
			?country purl:subject ?y. filter(?y=dbc:Eastern_Europe  || ?y=dbc:Southeastern_Europe || ?y=dbc:Baltic_states ).
		
			filter(lang(?name)="en" && str(?name)!="Baltic states" ) .
			optional{ ?country dbo:dissolutionYear ?x .}
		
		
			minus { ?country dbo:dissolutionYear ?x filter(?x < 2016) }
			minus { ?country purl:subject dbc:Central_Europe }
		}
		ORDER BY (?name)';
		
*/
	
	$query=$prefix.'
		select distinct ?country ?name
   		
		where{
		
		?country a dbo:Country .
                ?country rdfs:label ?name .
                ?country purl:subject ?y. filter(?y=dbc:Eastern_Europe  || ?y=dbc:Southeastern_Europe || ?y=dbc:Baltic_states ).

		filter(lang(?name)="en" && str(?name)!="Baltic states" ) .

		optional{ ?country dbo:dissolutionYear ?x}

		 optional{?country purl:subject ?z
                         FILTER(?z=dbc:Central_Europe) }
		FILTER(!BOUND(?z) && !BOUND(?x))
              

		}
ORDER BY (?name)';
	
	
	
		$subrows = $store->query ( $query, 'rows' );
		
		foreach ( $subrows as $subrow ) {
			echo "<li><a href='" . $subrow ['country'] . "'>" . $subrow ['name'] . "</a></li>";
		}
		
		echo "</ul>";
		echo "</div>";
		
		
		
		/* ########################## Center Europe ############################*/
		
		echo "<div id='region2'>";
		echo "<h3>Center Europe</h3>
		 		<ul id='center'>";
		
		$query=$prefix.'
		
		select  ?country ?name
   		
		where{
   		
		?country purl:subject dbc:Central_Europe.
		?country a dbo:Country .
		?country rdfs:label ?name .
		filter(lang(?name)="en" ) }
				ORDER BY (?name) ';
		
		
		$subrows = $store->query ( $query, 'rows' );
		
		foreach ( $subrows as $subrow ) {
			echo "<li><a href='" . $subrow ['country'] . "'>" . $subrow ['name'] . "</a></li>";
		}
		
		echo "</ul>";
		echo "</div>";
		
		
		
		
		
		/* ########################## Western Europe ############################*/
		
		
		
		echo "<div id='region3'>";
		echo "<h3>Western Europe</h3>
		 		<ul id='west'>";
		
		$query=$prefix.'
		
		select distinct ?country ?name
   		
		where{
		
		?country a dbo:Country .
                ?country rdfs:label ?name .
                ?country purl:subject ?y. filter(?y=dbc:Western_Europe  || ?y=dbc:Northwestern_Europe || ?y=dbpedia:Iberian_Peninsula).
		filter(lang(?name)="en" ).

 		optional{?country purl:subject ?z
                         FILTER(?z=dbc:Central_Europe) }
		FILTER(!BOUND(?z))
           }
		ORDER BY (?name)';
		
		
		$subrows = $store->query ( $query, 'rows' );
		if ($errs = $store->getErrors ()) {
			echo "Query errors";
			print_r ( $errs );}
			
		foreach ( $subrows as $subrow ) {
			echo "<li><a href='" . $subrow ['country'] . "'>" . $subrow ['name'] . "</a></li>";
		}
		
		echo "</ul>";
		echo "</div>";
		
	
	echo "</div>"; // the entire list
              		
	
	
	
	
	/*###################    Dispaly Countries with a Criteria  ####################### */
	
	
	echo '<form id="form"  method="POST"><hr style="border: 1px solid #bfc5c4; width:590px;"> <b> Number:  </b> <select name="k" class="styled-select blue semi-square">';
	
	for($j=10;$j<=50;$j++)
	{
		echo '<option value='.$j.'>'.$j.'</option>';
	}
	echo '</select>';
	

	echo ' <b> Criteria:</b>
	<select name="cri" id="soflow-color">
  		<option>area</option>
 		<option>populationDensity</option>
	</select>';
	
	
	echo '<input type="submit" id="display" value="display">';
	
	echo '<hr style="border: 1px solid #bfc5c4; width:590px;">';
	
    echo '<br><div id="navigation_wrapper">
     <input type="button" id="prev-button" value="previous">
	<input type="button" id="next-button" value="Next">
</div>';       		

    echo "<h3>Veuillez patienter quelques secondes</h3>";
 
    echo '<div id="test"></div></from>';
              		
	?>
	
  

  </div>
</body>
</html>