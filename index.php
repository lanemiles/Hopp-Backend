<?php

$ip = $_SERVER['REMOTE_ADDR'];

$url = "http://api.ipinfodb.com/v3/ip-city/?key=679f052fb8999ea90dd37a592a5186176e504fc22eb64d8703df502c11a98ea4&ip=" . $ip;

   // create curl resource 
        $ch = curl_init(); 

        // set url 
        curl_setopt($ch, CURLOPT_URL, $url); 

        //return the transfer as a string 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

        // $output contains the output string 
        $output = curl_exec($ch); 

        // close curl resource to free up system resources 
        curl_close($ch); 

        $stuff =  $output;

        $pos4 = strpos($stuff, ";");
        $first = substr($stuff, $pos4+1);

         $pos4 = strpos($first, ";");
        $first = substr($first, $pos4+1);

          $pos4 = strpos($first, ";");
        $first = substr($first, $pos4+1);

          $pos4 = strpos($first, ";");
        $first = substr($first, $pos4+1);

        $pos4 = strpos($first, ";");
        $good = substr($first, $pos4+1);

		$pos4 = strpos($first, ";");
        $first = substr($good, $pos4+1);

        $pos4 = strpos($first, ";");
        $first = substr($first, $pos4+1);


        $good = str_replace($first, "", $good);
        $good = substr($good,0,strlen($good)-1);
   $good = str_replace(";", ",", $good);

   $commaPos = strpos($good,",");

   $city = substr($good,$commaPos+1);
   $state = substr($good,0,$commaPos);

   $place = $city . "," . $state;
       


$host   = "localhost";
$db = "lanemiles";
//$conn = new PDO("mysql:host=$host;dbname=$db","lanemiles","Baxter!12");


//if ($place != "CLAREMONT,CALIFORNIA") {

//    $sql = "INSERT INTO `lanemiles`.`visitors` (`id`, `Date`, `IP`, `Location`) VALUES (NULL, CURRENT_TIMESTAMP, '$ip', '$place');";
 //   $q   = $conn->query($sql); 
//}

?>

<html>
<head>
	<title>Hey There -- I'm Lane</title>
	<link href='http://fonts.googleapis.com/css?family=Lato:100,300,400,700,900' rel='stylesheet' type='text/css'>
	<link rel='stylesheet' href='styles.css' type='text/css'>

</head>
<body>
	<div id='container'>
		
		<div id='left'>
			<h1 class='hello'>Hey There!</h1>
			<p class='mynameis'>My name is <span class='name'><br>Lane Miles</span> <br>and I like to <br>
				<span class='code'>write code.</span>
				<span class='code' style='display: none;'>solve problems.</span>
				<span class='code' style='display: none;'>talk politics.</span>
				<span class='code' style='display: none;'>solder stuff.</span>


			</p>
		</div>

		<div id='right'>
		
			<div class='block'>

				
				<table>
					<tr>
						<td class='leftpic'><img src='pics/universitybig.png' width=55 height=55></img></td>
						<td class='righttext'>Pomona College, 2017</td>
					</tr>

					<tr>
						<td class='leftpic'><img src='pics/emailbig.png' width=55 height=55></img></td>
						<td class='righttext'>Lane.Miles@Pomona.edu</td>
					</tr>

					<tr>
						<td class='leftpic'><img src='pics/phone2.png' width=55 height=55></img></td>
						<td class='righttext'>301-325-7737</td>
					</tr>

					<tr>
						<td class='leftpic'><img src='pics/linkedinbig.png' width=55 height=55></img></td>
						<td class='righttext'><a class='links' href='http://www.linkedin.com/in/lanemiles' target='_blank'>linkedin.com/in/lanemiles</a></td>
					</tr>

					<tr>
						<td class='leftpic'><img src='pics/githubbig.png' width=55 height=55></img></td>
						<td class='righttext'><a class='links' href='http://www.github.com/lanemiles' target='_blank'>github.com/lanemiles</a></td>
					</tr>

					<tr>
						<td class='leftpic'><img src='pics/resumebig.png' width=55 height=55></img></td>
						<td class='righttext'><a class='links' href='Lane Miles Resume.pdf' target='_blank'>My Resume</a></td>
					</tr>
				</table>
				

			</div>
		</div>

<div style='clear: both;'></div>

	</div>

	<script text='text/javascript'>
	window.addEventListener('resize', function(event){
  var windowHeight = window.innerHeight;
	var height = document.getElementById('container').offsetHeight;
	var margin = (windowHeight - height) / 2;
	if (windowHeight > height) {
		document.getElementById('container').style.marginTop = margin;
	}
});


	var windowHeight = window.innerHeight;
	var height = document.getElementById('container').offsetHeight;
	var margin = (windowHeight - height) / 2;
	if (windowHeight > height) {
		document.getElementById('container').style.marginTop = margin;
	}
</script>


<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script>
(function() {

    var quotes = $(".code");
    var quoteIndex = -1;

    function showNextQuote() {
        ++quoteIndex;

        if (quotes.eq(quoteIndex % quotes.length).text() == "write code." && quoteIndex == 0) {
        		quotes.eq(quoteIndex % quotes.length)
            .fadeIn(2000)
            .delay(000)
            .fadeOut(1500, showNextQuote);
            
            
        	}

        	else if (quotes.eq(quoteIndex % quotes.length).text() == "write code." && quoteIndex != 0) {
        		quotes.eq(quoteIndex % quotes.length)
            .fadeIn(2000)
            .delay(000);
            
            
            
        	} else {
        
        	quotes.eq(quoteIndex % quotes.length)
            .fadeIn(1500)
            .delay(1000)
            .fadeOut(1500, showNextQuote);
    }
        }
        

    showNextQuote();

})();
</script>
</body>
</html>