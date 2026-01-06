<?php


if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
header('Content-type: text/html; charset=utf-8');

include('db.php');
include('header.php');
include('packages.php');

if((!empty($_COOKIE['jruid']))&&(empty($_GET['uid']))){header('Location: /cv-writing?uid='.$_COOKIE['jruid']);}

if(!empty($_GET['opened'])){

$_GET['opened'] = addslashes($_GET['opened']);

mysql_query("UPDATE `cvlist` SET `opened` = '1' WHERE `md5` =  '{$_GET['opened']}' LIMIT 1");

}

$uid = addslashes($_GET['uid']);

$i = 1;$origpricedisplay = 'display:none;';
foreach($jrpackages as $key){$currentprice[$i] = $jrpackages[$i]['pricecut'];$i++;}


if(!empty($uid)){$q = mysql_query("SELECT * FROM `evaluation` WHERE `md5` = '$uid' LIMIT 1");

if(mysql_num_rows($q)=='1'){//FOUND RESULT

$fetchinfo = mysql_fetch_array($q);

//SETCOOKIE
if(empty($_COOKIE['jruid'])){setcookie("jruid",$uid,time() + (10 * 365 * 24 * 60 * 60));}

//PROFESSION
if(!empty($fetchinfo['profession']))$profession = ucwords($fetchinfo['profession']).' ';

//DECIDE IF OFFER IS ON
if($fetchinfo['giveoffer']=='1'){//OFFER IS OFF

$i = 1;$origpricedisplay = 'display:block;';
foreach($jrpackages as $key){$currentprice[$i] = $jrpackages[$i]['price'];$origprice[$i] = $jrpackages[$i]['pricecut'];$i++;}
}

}

}

if(!empty($profession)){$h1 = 'Get More '.$profession.' Interviews, Guaranteed.';}else{$h1 = 'LAND your next job, faster.';}

$currentyear = date("Y");

?>
<!DOCTYPE html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Our CV Writing Service - Winning CV</title>
	<link href="//fonts.googleapis.com/css?family=PT+Sans" rel="stylesheet">
	<link href="//fonts.googleapis.com/css2?family=Inter:wght@500;600&display=swap" rel="stylesheet">
	<link href="stylenew.css" rel="stylesheet">
	<link rel="stylesheet" href="src/css/swipebox.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.css">
	<style>
		#banner {
			padding: 15px 0 35px 0;
			background-color: black;
			background-image: url(//s3-eu-west-1.amazonaws.com/jobrise/cv-writing-bg2.jpg);
			background-size: cover;
		}

		h1 {
			text-align: left;
			color: white;
		}

		.sec_title {
			padding-bottom: 20px;
		}

		.guarantees {
			margin-top: 83px;
			text-align: left;
			margin-bottom: 29px;
		}

		.guarantees img {
			margin-right: 40px;
		}

		.greenbtn {
			width: 80%;
			height: 45px;
			line-height: 43px;
			margin: 27px;
		}

		.benefits {
			list-style: none;
			margin: 0;
			padding: 0;
			margin-bottom: 15px;
		}

		.benefits li {
			background-image: url('/imgs/bigtick.png');
			background-repeat: no-repeat;
			padding-left: 65px;
			margin-top: 45px;
		}

		.benefits li .title {
			font-size: 20px;
			color: #008600;
			font-weight: bold;
			margin-top: 3px;
			display: block;
		}

		.benefits li .desc {
			margin-top: 3px;
			display: block;
		}





.banner-2-stats{
    margin:auto;
    max-width:1000px;
    margin-top:20px;
}
.banner-2-stats > div{
    display:inline-block;
    margin:0 12px 15px 0;
    padding:8px 0;
    text-align:center;
    width:299px;
    background:#f5f5f5;
    border:1px solid #c6c6c6;
    border-radius:2px;
    -webkit-border-radius:2px;
    box-shadow:0 0px 1px 1px rgba(0,0,0,0.15);
    -webkit-box-shadow:0 0px 1px 1px rgba(0,0,0,0.15);
}
.banner-2-stats > div > div:first-child{
    color:#111;
     font-size: 23px;
    font-weight:bold;
}
.banner-2-stats > div > div:last-child{
    color:#777;
    font-size:14px;
    font-weight:bold;
    margin-top:-5px;
}

.firsthalfdiv .picture{width:220px;height:220px;border-radius: 100%; border: 3px solid #203791;
    position: relative;
    top: 25%;
    left: -2%;}


.firsthalfdiv,.secondhalfdiv{width:50%;float:left;height:100%;position: relative;box-sizing: border-box;padding:20px;}
.secondhalfdiv{}

.divpos{top:25%;position: relative;}

	    .who{font-size: 14px;font-weight: bold;margin-bottom: 10px;}
	    .desc{line-height: 24px;}
	    .ctabtn a{margin:0;margin-top:15px;}

	    .mobilebr{display:block;}

		#proPkgCard {
			max-width: 330px;
			font-family: 'Inter', sans-serif;
			font-weight: 500;
			background: #FFF;
			box-shadow: 0 0 6px 0 rgba(0, 0, 0, 0.24);
			margin: 32px auto;
		}

		#proPkgCard * {
			font-family: 'Inter', sans-serif;
			line-height: normal;
			letter-spacing: normal;
		}

		#proPkgCardHeader {
			font-size: 20px;
			text-align: center;
			padding: 12px;
			border-bottom: 1px solid #F3F3F3;
		}

		#proPkgCardBody {
			padding: 16px;
			padding-bottom: 0;
			text-align: center;
		}

		#proPkgCardBody .intro {
			margin: 0 0 12px;
			font-size: 14px;
		}

		#proPkgCardBody .price {
			color: #008819;
			margin-bottom: 8px;
			display: flex;
			justify-content: center;
		}

		#proPkgCardBody .price .currency {
			position: relative;
			top: 5px;
			font-size: 15px;
		}

		#proPkgCardBody .price .value {
			font-size: 40px;
		}

		#proPkgCardBody .prompt {
			font-size: 13px;
			margin-bottom: 8px;
		}

		#proPkgCardFooter {
			padding: 16px;
			padding-top: 20px;
			text-align: center;
		}

		#proPkgCardFooter a {
			display: block;
			font-size: 13px;
			font-weight: 600;
			text-decoration: none;
			color: #FFF;
			border-radius: 2px;
			border: 1px solid #5DA669;
			background: #71D381;
			padding: 8px;
		}

		#proPkgCardFooter a:hover {
			background: #4e9e5a;
		}

		#proPkgCardFooter a.disabled {
			opacity: 0.5;
			cursor: not-allowed;
			pointer-events: none;
		}

		/* Segmented Slider Styles */
		#word-count-slider {
			margin-top: 48px;
			height: 8px;
			position: relative;
			background: #D1D1D1;
			border-radius: 6px;
			border: none;
			box-shadow: none;
			position: relative;
			cursor: pointer;
		}
		
		/* Overlay layer that creates constant gaps on top of everything */
		.noUi-base::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background-image: 
				linear-gradient(90deg, transparent 10.61%, white 10.61%, white 11.61%, transparent 11.61%),
				linear-gradient(90deg, transparent 21.72%, white 21.72%, white 22.72%, transparent 22.72%),
				linear-gradient(90deg, transparent 32.83%, white 32.83%, white 33.83%, transparent 33.83%),
				linear-gradient(90deg, transparent 43.94%, white 43.94%, white 44.94%, transparent 44.94%),
				linear-gradient(90deg, transparent 55.06%, white 55.06%, white 56.06%, transparent 56.06%),
				linear-gradient(90deg, transparent 66.17%, white 66.17%, white 67.17%, transparent 67.17%),
				linear-gradient(90deg, transparent 77.28%, white 77.28%, white 78.28%, transparent 78.28%),
				linear-gradient(90deg, transparent 88.39%, white 88.39%, white 89.39%, transparent 89.39%);
			pointer-events: none;
			border-radius: 6px;
			z-index: 2;
		}

		.noUi-connect {
			background: #71D381;
		}

		.noUi-handle {
			border: none;
			border-radius: 50%;
			background: #1F4124;
			cursor: pointer;
			box-shadow: unset;
			width: 20px !important;
			height: 20px !important;
		}

		.noUi-handle:before,
		.noUi-handle:after {
			display: none;
		}

		/* Tooltip styling with SVG background */
		.noUi-tooltip {
			background-color: transparent;
			background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='42' height='27' viewBox='0 0 42 27' fill='none'%3E%3Cmask id='path-1-inside-1_1_79' fill='white'%3E%3Cpath d='M21 26.5L14.0723 20H0V0H42V20H27.9277L21 26.5Z'/%3E%3C/mask%3E%3Cpath d='M21 26.5L14.0723 20H0V0H42V20H27.9277L21 26.5Z' fill='%23F9F9F9'/%3E%3Cpath d='M21 26.5L20.6579 26.8646L21 27.1856L21.3421 26.8646L21 26.5ZM14.0723 20L14.4144 19.6354L14.2701 19.5H14.0723V20ZM0 20H-0.5V20.5H0V20ZM0 0V-0.5H-0.5V0H0ZM42 0H42.5V-0.5H42V0ZM42 20V20.5H42.5V20H42ZM27.9277 20V19.5H27.7299L27.5856 19.6354L27.9277 20ZM21 26.5L21.3421 26.1354L14.4144 19.6354L14.0723 20L13.7301 20.3646L20.6579 26.8646L21 26.5ZM14.0723 20V19.5H0V20V20.5H14.0723V20ZM0 20H0.5V0H0H-0.5V20H0ZM0 0V0.5H42V0V-0.5H0V0ZM42 0H41.5V20H42H42.5V0H42ZM42 20V19.5H27.9277V20V20.5H42V20ZM27.9277 20L27.5856 19.6354L20.6579 26.1354L21 26.5L21.3421 26.8646L28.2699 20.3646L27.9277 20Z' fill='%23DDDDDD' mask='url(%23path-1-inside-1_1_79)'/%3E%3C/svg%3E");
			background-size: contain;
			background-repeat: no-repeat;
			background-position: center;
			width: 42px;
			height: 27px;
			display: flex;
			justify-content: center;
			padding: 0;
			padding-top: 4px;
			font-size: 10px;
			font-weight: 600;
			border: none;
			border-radius: 0;
		}

		@media screen and (max-width:900px) {

#banner{background-position: center;}

			.package{
    float: none;
    margin: 30px 0;}

			.cnwidth{padding:10px;}

			.benefits li {
				    padding-left: 37px;
    background-size: 26px;
    background-position-y: 3px;
    margin-top: 30px;
			}


			.sec_desc{font-size:21px; margin-top: 40px;}


		}


		@media screen and (max-width:650px) {

			.firsthalfdiv,.secondhalfdiv{width:100%;}
			.secondhalfdiv{margin-top: -32px;}

						.firsthalfdiv .picture{top:25%;width:150px;height:150px;background-size:cover;}
			.secondhalfdiv .divpos{top:17%;}

			.divpos {
    top: 25%;
    position: relative;
}

			.guarantees img {
				max-width:32%;
				height:auto;
				margin-right: 0;
			}

			.mobilebr{display:none;}

		}

		@media screen and (min-width:981px) {
			#proPkgCard {
				margin-bottom: 0;
			}
		}

	</style>
</head>

<body>


	<?=$header ?>



		<div id="banner">


			<div class="cnwidth">

<h1><?=$h1?></h1>

				<p class="sec_desc">Our professional CV-writing service has helped over 35,000+ professionals<br class="mobilebr">
					<font style="color:yellow;font-style:italic;"> land more interviews and get hired faster.</font></p>

				<div class="guarantees">
					<img src="//keyshock.com/imgs/1stguarantee1.png">
					<img src="//keyshock.com/imgs/2ndguarantee1.png">
					<img src="//keyshock.com/imgs/3rdguarantee1.png">
				</div>

			</div>

		</div>


<div class="benefits_cn stats_cn">


			<div class="cnwidth" align="center">

				<div class="sec_title">Trusted by professionals from:</div>

<div class="mobilelogoshow">
	<img src="//keyshock.com/imgs/cl/moblogo.png">
</div>
<div class="owl-carousel owl-theme">
            <div class="item">
              <h4><img style="-webkit-user-select: none;" src="//keyshock.com/imgs/cl/1.png"></h4>
            </div>
            <div class="item">
              <h4><img style="-webkit-user-select: none;" src="//keyshock.com/imgs/cl/2.png"></h4>
            </div>
            <div class="item">
              <h4><img style="-webkit-user-select: none;" src="//keyshock.com/imgs/cl/3.png"></h4>
            </div>
            <div class="item">
              <h4><img style="-webkit-user-select: none;" src="//keyshock.com/imgs/cl/4.png"></h4>
            </div>
            <div class="item">
              <h4><img style="-webkit-user-select: none;" src="//keyshock.com/imgs/cl/5.png"></h4>
            </div>
            <div class="item">
              <h4><img style="-webkit-user-select: none;" src="//keyshock.com/imgs/cl/6.png"></h4>
            </div>
            <div class="item">
              <h4><img style="-webkit-user-select: none;" src="//keyshock.com/imgs/cl/11.png"></h4>
            </div>
            <div class="item">
              <h4><img style="-webkit-user-select: none;" src="//keyshock.com/imgs/cl/12.png"></h4>
            </div>
          </div>
			</div>

		</div>


		<div class="grey_sec">


			<div class="cnwidth">

				<div class="sec_title">Guaranteed Winning CV for the job you want to land.</div>
				<p style="text-align:left">
Our team of recruitment and writing experts who will turn your CV into something that’s professional, personal, sets the correct tone of a winning CV!
				</p>


				<ul class="benefits">

					<li>
						<font class="title">Double interview Guarantee.</font>
						<span class="desc">Our team guarantee you will get 2x more job interviews within 60 days or we'll rewrite your CV for free.</span></li>


					<li>
						<font class="title">Trusted by over 35,000+ professionals.</font>
						<span class="desc">Since 2006, our team has helped over 35,000+ professionals <br>win small and big job positions all over the US.</span></li>


					<li>
						<font class="title">Expert support and career advice.</font>
						<span class="desc">Join the Winning CV family. Our CV Experts are ready to help you with employment and your CV.</span></li>


				</ul>


			</div>

		</div>



		<div class="benefits_cn">


			<div class="cnwidth" align="center">

				<div class="sec_title">Everything you need to win your ideal job.</div>
<ul style="text-align:left;">
<li>One-to-one consultation with a recruitment expert.</li>
<li>A bespoke professional CV written around you.</li>
<li>Targeted to the jobs you want to apply for.</li>
<li>Skill matched & Keyword optimized.</li>
<li>100% Applicant Tracking System (ATS) compatible.</li>
<li>Unlimited amendments before sign off.</li>
<li>CV in both Word & PDF formats.</li>
<li>Direct contact with CV Expert from beginning to end.</li>
<li>100% Satisfaction Guarantee.</li>
</ul>


			</div>

		</div>




		<div class="grey_sec">


<div class="cnwidth" align="center">

			<div class="firsthalfdiv picturediv">

			<div class="picture " style="background-image: url('//keyshock.com/imgs/testimonials/5.jpg');"></div>

			</div>


			<div class="secondhalfdiv textdiv">

				<div class="divpos">
					
					<h2>How we've made a huge difference.</h2>
					<div class="who">MARIA, HEALTH &amp; COUNSELLING SECTOR</div>
					<div class="desc">"The first interview I got was my dream job with a major healthcare system. I was so excited — they were talking about all this travel that I would get to do. Working with Winning CV gave me a lot of confidence."</div>
					<div class="ctabtn"><a href="//keyshock.com/success-stories/maria" class="greenbtn">See how we helped</a>
						<a href="//keyshock.com/success-stories" class="greenbtn" style="background:none;color:black;border:none;">See more success stories</a></div>


				</div>

			</div>

	</div>

		</div>

<div class="benefits_cn stats_cn">


			<div class="cnwidth" align="center">

				<div class="sec_title">What we've achieved From 2006 to <?=$currentyear ?>.</div>


<div class="banner-2-stats">
<div><div>$3 million</div><div>Of job salaries</div></div>
<div><div>400,000+</div><div>US job views</div></div>
<div><div>90,000+</div><div>Active jobs</div></div>
<div><div>20,000+</div><div>Job subscribers</div></div>
<div><div>35,000+</div><div>Successful applicants</div></div>
<div><div>272,100+</div><div>CVs sent</div></div></div>

			</div>

		</div>

		<div class="grey_sec benefits_cn" style="background-color: #fffab2;">


			<div class="cnwidth">

				<div class="sec_title">Your ideal job is waiting. Get started with our CV experts now.</div> 

				<div id="proPkgCard">
					<div id="proPkgCardHeader">Professional Growth CV</div>
					<div id="proPkgCardBody">
						<p class="intro">An expertly written and keyword-optimized CV that sets you apart.</p>
						<div class="price">
							<span class="currency">$</span>
							<span class="value">25</span>
						</div>
						<div class="prompt">Select the amount of words you want:</div>
						<div id="word-count-slider"></div>
					</div>
					<form method="post" id="formPrice" action="/order/payment">
						<input type="hidden" name="packagePrice" id="packagePrice" value="25">
						<div id="proPkgCardFooter">
							<a href="#" onclick="document.getElementById('formPrice').submit();">Order Now ></a>
						</div>
					</form>
				</div>

			</div>

		</div>









		<?=$footer ?>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.js"></script>

    <script src="src/js/jquery.swipebox.js"></script>
			<script type="text/javascript">
				$(document).ready(function() {

					// Initialize segmented slider with error handling
					try {
						const slider = document.getElementById('word-count-slider');
						const wordCounts = [0, 250, 500, 750, 1000, 1500, 1750, 2000, 2500, 3000];
						const prices = [0, 25, 35, 50, 60, 75, 99, 105, 120, 150];
						
						if (!slider) {
							console.warn('Segmented slider element not found');
							return;
						}

						if (typeof noUiSlider === 'undefined') {
							console.error('noUiSlider library not loaded');
							return;
						}
						
						noUiSlider.create(slider, {
							start: [1],
							step: 1,
							range: {
								'min': 0,
								'max': 9
							},
							connect: [true, false],
							tooltips: [{
								to: function(value) {
									try {
										var index = Math.round(value);
										var count = wordCounts[index];
										// Format numbers >= 1000 with comma separator
										if (count >= 1000) {
											return count.toLocaleString('en-US');
										}
										return count;
									} catch (e) {
										console.error('Error formatting tooltip:', e);
										return '0';
									}
								}
							}]
						});

						// Update price display with error handling
						slider.noUiSlider.on('update', function (values, handle) {
							try {
								const index = parseInt(values[handle]);
								const selectedPrice = prices[index];
								const selectedPriceElement = document.querySelector('#proPkgCardBody .price .value');
								const orderLink = document.querySelector('#proPkgCardFooter a');

								if (selectedPriceElement && selectedPrice !== undefined) {
									selectedPriceElement.textContent = selectedPrice;
									document.getElementById('packagePrice').value = selectedPrice;
									// Disable/enable order link based on price
									if (orderLink) {
										if (selectedPrice === 0) {
											orderLink.classList.add('disabled');
											orderLink.setAttribute('aria-disabled', 'true');
										} else {
											orderLink.classList.remove('disabled');
											orderLink.removeAttribute('aria-disabled');
										}
									}
								}

							} catch (e) {
								console.error('Error updating price display:', e);
							}
						});

					} catch (error) {
						console.error('Error initializing segmented slider:', error);
					}

					/* Basic Gallery */
					$('.swipebox').swipebox();

					/* Video */
					$('.swipebox-video').swipebox();

					/* Dynamic Gallery */
					$('#gallery').click(function(e) {
						e.preventDefault();
						$.swipebox([{
								href: '//swipebox.csag.co/mages/image-1.jpg',
								title: 'My Caption'
							},
							{
								href: '//swipebox.csag.co/images/image-2.jpg',
								title: 'My Second Caption'
							}
						]);
					});

				});

			</script>

			<script src="owl.carousel.min.js"></script>

			          <script>
            $(document).ready(function() {
              $('.owl-carousel').owlCarousel({
              	pagination: false,
                margin: 10,
                responsiveClass: true,
                responsive: {
                  0: {
                    items: 1,
                    autoplay: 1700,
                    autoplayTimeout: 2000,
                    loop: true,
                    nav: false
                  },
                  600: {
                    items: 3,
                    autoplay: 1700,
                    loop: true,
                    autoplayTimeout: 2000,
                    nav: false
                  },
                  1000: {
                    items: 5,
                    loop: true,
                    autoplay: 1700,
                    autoplayTimeout: 2000,
                    nav: false,
                    dots: false,
                    margin: 20
                  }
                }
              })
            })
          </script>

</body>

</html>
