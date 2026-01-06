<?

include('db.php');
include('header.php');

?>


<!DOCTYPE html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>About Us - Keys Hock</title>
	<link href="https://fonts.googleapis.com/css?family=PT+Sans" rel="stylesheet">
	<link href="/stylenew.css" rel="stylesheet">
	<style>
		
		body{background-color:#fff;}
		.footer{margin-top: -5px;}

		h1{text-align: left;color: #000;font-weight:bold;}
		h2{font-size: 26px;margin-bottom: 10px;}

		.cnwidth {
    max-width: 100%;
}

		.cnwidth2 {
    max-width: 950px;margin: 60px 0;
}

		.mainheader{box-shadow: none;-webkit-box-shadow: none;}
		.ini{height:initial!important;}

		.benefits_cn{padding:0;}

		.firstsection{height:473px;    background-color: #f2f2f2;}

		.grey_sec{margin-top: -5px;}


		.firstsection .cnwidth{height:100%;}
		.first .cnwidth{max-width: 100%;}


		.firsthalfdiv{padding: 70px;}
		.firsthalfdiv .picture{width:220px;height:220px;border-radius: 100%; border: 3px solid #203791;position: relative; top: 25%;
    left: -2%;}
    	.firsthalfdiv .bgpic{background-size: cover;
    background-position: center;
    width: 100%;
    height: 100%;
    box-shadow: 0 9px 20px -8px rgba(32,33,36,0.3);
    overflow: hidden;}


		.firsthalfdiv,.secondhalfdiv{width:50%;float:left;height:100%;position: relative;box-sizing: border-box;}
		.secondhalfdiv{padding:20px;}

		.divpos{position: relative;text-align: left;padding: 60px;}
		.quote1{margin:0;margin-top:20px;font-size: 22px;}



		.secondsectioncnwidth{padding: 30px 0 ;height:100%;}
		.snapsection{width:25%;box-sizing:border-box;padding: 0 15px;display:block;height:100%;float:left;border-right:1px solid #e4e8f0;text-align: left;font-size:15px;}
		.snapsection .headline{font-size:12px;color:#203791;display:block;margin-bottom:10px;}
		.snapsection:last-child{border-right:0;}



		.firstsnap{font-size:22px;font-weight:bold;}

	    .first{padding: 0 0;}
	    .third{background-color:#fff!important;}
	    .heading2{font-size: 19px;line-height: 29px;text-align: left}

	    .who{font-size: 14px;font-weight: bold;margin-bottom: 10px;}
	    .desc{line-height: 24px;}
	    .ctabtn{}

	    .statsholder{width:750px;display:inline-block;margin-bottom:35px;}
	    .statsholder .stats{width:33%;float:left;display:block;}
	    .statsholder .stats .picture{height:200px;}
	    .statsholder .stats .picture img{width:200px;height:200px;}
	    .statsholder .stats .text{font-size:17px;}

	    .considertext{    line-height: 28px;}

		@media screen and (max-width:900px) {



		}


		@media screen and (max-width:650px) {
			h2{margin-top:20px;}
			.grey_sec{padding:0;}
			.cnwidth{width:100%;display: flex; flex-direction: column; }
			.second,.third{height:initial!important;}

			.secondsection{display:none;}
			.thirdsectioncnwidth .firstcnsect, .thirdsectioncnwidth .secondcnsect{float:none;width:100%;}
			.txt1,.txt2{margin-left:15px;}
			.bigquote{padding: 10px;font-size: 22px!important;width:100%!important;}
			.changepoints{padding-left: 40px;}
			.minititle{display:block;width:100%;padding-left:15px;}
			.beforeafter{display: inline-block;}
			
			.first .cnwidth {max-width: 100%;}
			.first .cnwidth .firsthalfdiv{display:none;}
			.first .cnwidth .secondhalfdiv {width:100%;}
			.first .cnwidth .secondhalfdiv .divpos{width:100%;padding-right:0;top:5%;position: relative;}
			.firstsection {  height: 300px;}

			.divpos{position:relative;text-align: left;padding-right:0;}

			.firsthalfdiv .picture {
    width: 130px;
    height: 130px;
    background-size:cover;
    margin-top: 5px;
}




.textdiv{width:100%;order: 2;float:none;}
.picturediv{width:100%;order: 1;float:none;}

.firstsection{height:initial;}
.cnwidth{display:block;    padding: 10px 15px;}
.cnwidth2{margin-top:0;    padding: 10px 15px;}
.statsholder{width:100%;margin-bottom:0;}
.statsholder .stats{float:none;width:100%;border-bottom: 1px solid #dedede;padding-bottom: 16px;}
.secondhalfdiv{padding:0;}
.divpos{padding:0;}
.header2{margin-bottom:10px;}
h1{margin-top:15px;margin-bottom:5px;    font-size: 26px;line-height:37px;}
.firsthalfdiv,.secondhalfdiv{width:100%;float:none;padding:0;}
.firsthalfdiv .bgpic{width:100%;height:270px;box-shadow: none;}
.heading2{font-size:17px;margin-bottom:15px;}
.co2{text-align:left!important;}
.first .cnwidth .firsthalfdiv{display:block;}
.firstbg{display:none!important;}

}




		@media screen and (max-width:500px) {

.before,.after{width:100%;float:none;}

		}




	</style>

<script>


if (document.body && document.body.offsetWidth) {
    scrwidth = document.body.offsetWidth;
}
if (document.compatMode == 'CSS1Compat' && document.documentElement && document.documentElement.offsetWidth) {
    scrwidth = document.documentElement.offsetWidth;
}


function valuation(theurl){


    if (scrwidth < '610') {

        var wSign=scrwidth - 40;
        var hSign="790";
        var position = "absolute";
        var toppos = "420px";
        scroll(0,0);

    } else {

var wSign="610";
var hSign="690";
var position = "fixed";
var toppos = "50%";
    
    }

var signDiv=document.createElement("div");  signDiv.id='signDiv';   document.body.appendChild(signDiv);
var signupHTML='<div style="background:#000;height:100%;position:fixed;top:0;left:0;width:100%;opacity:0.6;filter:alpha(opacity=60);z-index:10" onclick="return closevaluationDiv()"></div>';
signupHTML+='<div style="overflow: hidden;-webkit-border-radius:5px;border-radius:5px; box-shadow:1px 1px 0px 0px rgba(50, 50, 50, 0.75);-webkit-box-shadow:1px 1px 0px 0px rgba(50, 50, 50, 0.75); z-index:9999;width:'+wSign+'px;height:'+hSign+'px;position:'+ position +';left:50%;margin-left:-'+(wSign/2)+'px;top:'+ toppos +';margin-top:-'+(hSign/2)+'px;background:#fff;border:0">';
signupHTML+='<iframe src="/valuation.php" style="overflow: hidden;width:100%;height:100%;border:none;outline:none;" scrolling="yes" style="width: 100%;height: 520px;border: 0;"></iframe></div>';
document.getElementById('signDiv').innerHTML=signupHTML;
document.getElementById('email_popup').style.display = "none";


return false;
}
function closevaluationDiv(){
document.body.removeChild(document.getElementById('signDiv'));
return false;
}



</script>

</head>

<body>


<?=$header?>



<div class="grey_sec firstsection first">

	<div class="cnwidth" align="center">


			<div class="secondhalfdiv">

				<div class="divpos">
					<h1>You live once with one CV. Make it a <i>Winning CV</i>.</h1>
					<div class="heading2">Your CV is one of your most important career tools. But many professionals have no idea where to start, what to include, and what to leave off of their CVs. That’s where Winning CV comes in. We can carefully craft your career story to target the job that you want to land.</div>

				</div>

			</div>

			<div class="firsthalfdiv firstbg">
				<div class="bgpic" style="background-image:url('https://keyshock.com/imgs/kh-build.jpg');">


				</div>

			</div>


	</div>

</div>



<div class="grey_sec firstsection third">

	<div class="cnwidth" align="center">



			<div class="firsthalfdiv">
				<div class="bgpic" style="background-image:url('https://keyshock.com/imgs/kh-grp.jpg');">


				</div>

			</div>


			<div class="secondhalfdiv">

				<div class="divpos">
					<h2>Who we are.</h2>
					<div class="heading2">Your CV is one of your most important career tools. But many professionals have no idea where to start, what to include, and what to leave off of their CVs. That’s where Winning CV comes in. We can carefully craft your career story to target the job that you want to land.</div>

				</div>

			</div>







	</div>

</div>





<div class="grey_sec firstsection first ini">

	<div class="cnwidth cnwidth2" align="center">


<div class="statsholder" style="">

<h2>A professional CV can make you:</h2>

	<div class="stats">

		<div class="picture"><img src="https://keyshock.com/imgs/stat1.png"></div>
		<div class="text">38% more likely to be <b>contacted by recruiters.</b></div>

	</div>


	<div  class="stats">

		<div class="picture"><img src="https://keyshock.com/imgs/stat2.png"></div>
		<div class="text">31% more likely to <b>land an interview.</b></div>

	</div  class="stats">
	

	<div   class="stats">

		<div class="picture"><img src="https://keyshock.com/imgs/stat3.png"></div>
		<div class="text">40% more likely to <b>land a job.</b></div>

	</div>



</div>

	</div>

</div>


<div class="grey_sec firstsection first third ini">

	<div class="cnwidth cnwidth2" align="center">


<div class="statsholder" style="">

<h2 class="co2">Invest in your career, with a professionally written CV.</h2>


<div class="considertext co2">
	Consider this: The average length of a job search is approximately 20 weeks. For every week you’re unemployed, you’re missing out on each day’s pay you aren’t earning over a five-day work week. A professionally written CV is guaranteed to get you more interviews to land the job you want, faster. Even if this shortens your job search by just a day or two, you’ve made your money back, and then some. Think of it as an investment in your earning power.

</div>


</div>

	</div>

</div>







<div class="grey_sec firstsection first">

	<div class="cnwidth" align="center">



			<div class="secondhalfdiv">

				<div class="divpos">
					<h2>We're here to help you through the job search.</h2>
					<div class="heading2">Our team is made up of professional writers from around the US with exponential experience in Human Resources, recruiting, career coach, and job search strategy. We write CVs that will get passed the automated recruiting software used to screen out applicants and stand out to hiring managers for all the right reasons.</div>

				</div>

			</div>

			<div class="firsthalfdiv">
				<div class="bgpic" style="background-image:url('https://keyshock.com/imgs/kh-sm.jpg');">


				</div>

			</div>


	</div>

</div>


<div class="grey_sec firstsection first third ini">

	<div class="cnwidth cnwidth2" align="center">


<div class="statsholder" style="">

<h2 class="co2">Ready to change your career?</h2>

<div class="ctabtn">
	<a href="https://keyshock.com/cv-writing" class="greenbtn">Upgrade my CV</a>
</div>


<h2 class="co2">Want a professional CV review?</h2>

<div class="ctabtn">
	<a onclick="return valuation();" href="#" class="greenbtn" style="background-color:black;color:white;border:none;">Review my CV</a>


</div>




</div>

	</div>

</div>



<?=$footer?>



</body>

</html>
