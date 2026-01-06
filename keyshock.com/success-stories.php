<?

include('db.php');
include('header.php');

if(!empty($_GET['open'])){
$_GET['open'] = addslashes($_GET['open']);
	mysql_query("UPDATE `cvlist` SET `opened` = '1' WHERE `md5` = '{$_GET['open']}' LIMIT 1");}

?>

<!DOCTYPE html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Success Stories - Keys Hock</title>
	<link href="https://fonts.googleapis.com/css?family=PT+Sans" rel="stylesheet">
	<link href="/stylenew.css" rel="stylesheet">
	<style>
		
		body{background-color:#fff;}
		.footer{margin-top: -5px;}

		h1{text-align: center;color: #fff;font-weight:bold;}
		h2{font-size: 26px;margin-bottom: 10px;}

		.mainheader{box-shadow: none;-webkit-box-shadow: none;}


		.benefits_cn{padding:0;}

		.firstsection{height:473px;background-color:#e7eaf2!important;}

		.grey_sec{background-color:#fff;margin-top: -5px;}


		.firstsection .cnwidth{height:100%;}
		.first .cnwidth{    max-width: 75%;}

		.firsthalfdiv .picture{width:220px;height:220px;border-radius: 100%; border: 3px solid #203791;position: relative; top: 25%;
    left: -2%;}


		.firsthalfdiv,.secondhalfdiv{width:50%;float:left;height:100%;position: relative;box-sizing: border-box;}
		.secondhalfdiv{padding:20px;}

		.divpos{top:25%;position: absolute;text-align: left;padding-right: 20px;}
		.quote1{margin:0;margin-top:20px;font-size: 22px;}



		.secondsectioncnwidth{padding: 30px 0 ;height:100%;}
		.snapsection{width:25%;box-sizing:border-box;padding: 0 15px;display:block;height:100%;float:left;border-right:1px solid #e4e8f0;text-align: left;font-size:15px;}
		.snapsection .headline{font-size:12px;color:#203791;display:block;margin-bottom:10px;}
		.snapsection:last-child{border-right:0;}



		.firstsnap{font-size:22px;font-weight:bold;}

	    .first{background-color:#203791!important;padding: 0 0;}
	    .third{background-color:#fff!important;}
	    .heading2{color:#fff;font-size: 19px;line-height: 29px;text-align: center}

	    .who{font-size: 14px;font-weight: bold;margin-bottom: 10px;}
	    .desc{line-height: 24px;}
	    .ctabtn{}

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

			.divpos{position:relative;text-align: center;padding-right:0;}

			.firsthalfdiv .picture {
    width: 130px;
    height: 130px;
    background-size:cover;
    margin-top: 5px;
}



.textdiv{width:100%;order: 2;float:none;}
.picturediv{width:100%;order: 1;float:none;}



}




		@media screen and (max-width:500px) {

.before,.after{width:100%;float:none;}

		}




	</style>
</head>

<body>


	<?=$header ?>



<div class="grey_sec firstsection first">

	<div class="cnwidth" align="center">


			<div class="secondhalfdiv">

				<div class="divpos">
					<h1>Success Stories</h1>
					<div class="heading2">Keys Hock knows how to help people get ahead in the job search. See what we did for these professionals.</div>

				</div>

			</div>


			<div class="firsthalfdiv" style="background-image:url('https://keyshock.com/imgs/shakehands.jpg');background-size:cover;background-position:center;">


			</div>




	</div>

</div>



<div class="grey_sec firstsection second">

	<div class="cnwidth" align="center">

			<div class="firsthalfdiv picturediv">

			<div class="picture " style="background-image: url('https://keyshock.com/imgs/testimonials/5.jpg');"></div>

			</div>


			<div class="secondhalfdiv textdiv">

				<div class="divpos">
					
					<h2>Looking to move up the career ladder</h2>
					<div class="who">MARIA, HEALTH & COUNSELLING SECTOR</div>
					<div class="desc">Maria was searching for her dream job, but wasn’t sure how to get it. She needed to define her relevant job skills and package them to attract the right job opportunities.</div>
					<div class="ctabtn"><a href="https://keyshock.com/success-stories/maria" class="greenbtn">See how we helped</a></div>


				</div>

			</div>

	</div>

</div>





<div class="grey_sec firstsection third">

	<div class="cnwidth" align="center">


			<div class="secondhalfdiv textdiv">

				<div class="divpos">
					
					<h2>Lacking experience in the industry</h2>
					<div class="who">BRADLEY, IT & SECURITY SECTOR</div>
					<div class="desc">Bradley wanted to transition from one industry to another. He needed help highlighting the job skills he had that could translate when changing careers.</div>
					<div class="ctabtn"><a href="https://keyshock.com/success-stories/bradley" class="greenbtn">See how we helped</a></div>


				</div>

			</div>



			<div class="firsthalfdiv picturediv">

			<div class="picture" style="background-image: url('https://keyshock.com/imgs/testimonials/2.jpg');"></div>

			</div>




	</div>

</div>



<div class="grey_sec firstsection second">

	<div class="cnwidth" align="center">

			<div class="firsthalfdiv picturediv">

			<div class="picture" style="background-image: url('https://keyshock.com/imgs/testimonials/1.jpg');"></div>

			</div>


			<div class="secondhalfdiv textdiv">

				<div class="divpos">
					
					<h2>Moving to a different industry</h2>
					<div class="who">JOSHUA, CUSTOMER SERVICES</div>
					<div class="desc">
Joshua spent a numerous amount of years stuck in the same industry not being able to use a powerful CV to land him a new career. He needed help telling his career story in a cohesive way to land his dream job.</div>
					<div class="ctabtn"><a href="https://keyshock.com/success-stories/joshua" class="greenbtn">See how we helped</a></div>


				</div>

			</div>

	</div>

</div>




<div class="grey_sec firstsection third">

	<div class="cnwidth" align="center">


			<div class="secondhalfdiv textdiv">

				<div class="divpos">
					
					<h2>Lack of accomplishments</h2>
					<div class="who">ALIZ, SOCIAL WORKER</div>
					<div class="desc">Aliz had great work experience, but her job accomplishments were buried in her CV. She needed help highlighting her selling point and telling her career story.</div>
					<div class="ctabtn"><a href="https://keyshock.com/success-stories/aliz" class="greenbtn">See how we helped</a></div>


				</div>

			</div>



			<div class="firsthalfdiv picturediv">

			<div class="picture" style="background-image: url('https://keyshock.com/imgs/testimonials/3.jpg');"></div>

			</div>




	</div>

</div>






<div class="grey_sec firstsection second">

	<div class="cnwidth" align="center">

			<div class="firsthalfdiv">

			<div class="picture picturediv" style="background-image: url('https://keyshock.com/imgs/testimonials/4.jpg');"></div>

			</div>


			<div class="secondhalfdiv textdiv">

				<div class="divpos">
					
					<h2>Find an employer that appreciates skills</h2>
					<div class="who">PAUL, RETIREMENT HOME SERVICES</div>
					<div class="desc">Paul had exceptional work experience, but his job accomplishments were buried in his CV. He needed help highlighting his selling point and telling his career story.</div>
					<div class="ctabtn"><a href="https://keyshock.com/success-stories/paul" class="greenbtn">See how we helped</a></div>


				</div>

			</div>

	</div>

</div>





		<?=$footer ?>


</body>

</html>