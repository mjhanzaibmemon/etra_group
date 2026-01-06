<?php

include('header.php');

?>
<!DOCTYPE html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Approach and Credentials - Keyshock</title>
	<link href="https://fonts.googleapis.com/css?family=PT+Sans" rel="stylesheet">
	<link href="stylenew.css" rel="stylesheet">
	<style>
		.layoutboth {
			width: 50%;
			box-sizing: border-box;
			float: left;
			margin-top: 50px;
			margin-bottom: 40px;
		}
		
		.number {
			width: 20%;
			float: left;
			text-align: center;
			line-height: 110px;
			background: rgb(255, 255, 255);
			background: -moz-linear-gradient(top, rgba(255, 255, 255, 1) 0%, rgba(229, 229, 229, 1) 100%);
			background: -webkit-linear-gradient(top, rgba(255, 255, 255, 1) 0%, rgba(229, 229, 229, 1) 100%);
			background: linear-gradient(to bottom, rgba(255, 255, 255, 1) 0%, rgba(229, 229, 229, 1) 100%);
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e5e5e5', GradientType=0);
			font-size: 40px;
			color: #008600;
			font-weight: bold;
			height: 100%;
			border-right: 1px solid #dcdbdb;
		}
		
		.number:hover {
			/* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#f7f7f7+0,d6d6d6+100 */
			background: rgb(247, 247, 247);
			/* Old browsers */
			background: -moz-linear-gradient(top, rgba(247, 247, 247, 1) 0%, rgba(214, 214, 214, 1) 100%);
			/* FF3.6-15 */
			background: -webkit-linear-gradient(top, rgba(247, 247, 247, 1) 0%, rgba(214, 214, 214, 1) 100%);
			/* Chrome10-25,Safari5.1-6 */
			background: linear-gradient(to bottom, rgba(247, 247, 247, 1) 0%, rgba(214, 214, 214, 1) 100%);
			/* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f7f7f7', endColorstr='#d6d6d6', GradientType=0);
			/* IE6-9 */
			cursor: pointer;
		}
		
		.numberdesc {
			width: 20%;
			float: left;
			text-align: center;
			font-size: 23px;
			color: #008600;
			padding-top: 21px;
		}
		/* CHANGE NAME */
		
		*,
		*:after,
		*:before {
			box-sizing: border-box;
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			-webkit-font-smoothing: antialiased;
			font-smoothing: antialiased;
			text-rendering: optimizeLegibility;
		}
		
		.mainServices .sectionSeparator {
			display: block;
			margin: 65px auto 30px;
		}
		
		.mainServiceItem {
			font-size: 16px;
			font-weight: 400;
			color: #383838;
			position: relative;
			display: inline-block;
			min-height: 255px;
			text-align: center;
			background-color: #fff;
			box-shadow: 0 0 5px rgba(0, 0, 0, .35);
			background-image: linear-gradient(to top, #f1f1f1, #fff);
			transition: all 0.3s ease-in-out;
		}
		
		.mainServiceItem:after {
			content: "";
			display: block;
			position: absolute;
			top: 0;
			left: 0;
			z-index: -1;
			width: 100%;
			height: 100%;
			background-color: #fff;
			box-shadow: 0 0 5px rgba(0, 0, 0, .35);
			background-image: linear-gradient(to top, #f1f1f1, #fff);
			transform: rotate(-2deg);
		}
		
		.mainServiceItem.added:after {
			box-shadow: 0 5px 55px 3px #deb218;
		}
		
		.mainServiceItem:before {
			content: "";
			display: block;
			position: absolute;
			top: 10px;
			left: 10%;
			margin: 0 0 0 -5px;
			z-index: 2;
			width: 2px;
			height: 10px;
			background: #333333;
			transform: rotate(45deg);
		}
		
		.mainServiceItem.added:before {
			content: "";
			display: block;
			position: absolute;
			top: -5px;
			left: 50%;
			margin: 0 0 0 -5px;
			z-index: 2;
			width: 10px;
			height: 22px;
			background: url(resources/graphics/paperclip.svg) no-repeat center center;
			transform: rotate(0deg);
		}
		
		.mainServiceItem .mainservice {
			display: none;
			visibility: hidden;
		}
		
		.mainServiceItem>section.page {
			display: block;
			width: 100%;
			max-width: 185px;
			height: 100%;
			color: #383838;
		}
		
		.mainServiceItem>section.page:before {
			content: '.';
			display: inline-block;
			height: 100%;
			vertical-align: middle;
			margin-right: -0.85em;
			opacity: 0;
		}
		
		.mainServiceItem>section.page>.content {
			max-width: 185px;
			display: inline-block;
			vertical-align: middle;
			margin: 0px;
			padding: 25px 0px;
		}
		
		.mainServiceItem>section.page>.content>h3 {
			font-size: 15px;
			line-height: 18px;
			margin: 0px;
			padding: 0px 10px;
			/* -> */
			display: table-cell;
			vertical-align: middle;
			height: 60px;
			/* <- 25/04/2014 Denis */
			letter-spacing: -1px;
			font-family: 'alegreyabold', serif;
			color: #7f7f7f;
			text-shadow: 1px 1px #fff;
			text-transform: uppercase;
			word-wrap: break-word;
		}
		
		.mainServiceItem.added>section.page>.content>h3 {
			color: #222222;
		}
		
		.mainServiceItem>section.page>.content>.serviceNumber {
			font-size: 15px;
			font-family: "merriweatherbold", serif;
			color: #d5d5d5;
			display: none;
			position: absolute;
			top: 10px;
			left: 10px;
		}
		
		.mainServiceItem>section.page>.content>.options {
			margin: 0px 10px;
			height: auto;
			clear: both;
		}
		
		.mainServiceItem>section.page>.content>.options>span {
			display: block;
			font-size: 10px;
			text-align: center;
			text-transform: uppercase;
			font-weight: bold;
			margin: 5px 0px 5px 0px;
		}
		
		.mainServiceItem>section.page>.content>.options>div {
			width: 50%;
			height: auto;
			display: inline-block;
			font-size: 10px;
			color: #808080;
			letter-spacing: 0px;
			margin: 10px 0px 0px 0px;
		}
		
		.mainServiceItem>section.page>.content>.options>div input[type="checkbox"],
		main.services>.mainServiceItem>section.page>.content>.options>div input[type="radio"] {
			margin: -1px 0px 0px 0px;
		}
		
		.mainServiceItem>section.page>.content>.options>div label .number {
			display: block;
			font-family: "Arial", sans-serif;
			font-weight: bold;
			font-size: 30px;
			line-height: 30px;
			letter-spacing: -2px;
			color: #333;
		}
		
		.mainServiceItem>section.page>.content>.price {
			width: 100%;
			height: auto;
			text-align: center;
			font-size: 35px;
			font-family: "Arial", sans-serif;
			color: #deb218;
			font-weight: bold;
			line-height: 22px;
			margin: 25px 0px 0px 0px;
			display: none;
		}
		
		.mainServiceItem>section.page>.content>.price>span {
			font-size: 30px;
		}
		
		.mainServiceItem>section.page>.content>.price>.vat {
			padding: 0px;
			margin: 0;
			width: 100%;
			height: auto;
			text-align: center;
			font-size: 15px;
			font-family: "merriweatherbold", serif;
			text-transform: uppercase;
			color: #808080;
		}
		
		.mainServiceItem>section.page>.content>div.addremoveButton {
			display: block;
			width: auto;
			min-width: 90px;
			height: auto;
			min-height: 40px;
			font-size: 16px;
			font-family: "merriweatherregular", serif;
			color: #fff;
			border-radius: 4px;
			overflow: hidden;
			position: absolute;
			bottom: -20px;
			left: 50%;
			transform: translateX(-50%);
		}
		
		.mainServiceItem>section.page>.content button.add {
			font-size: 16px;
			font-family: "merriweatherregular", serif;
			color: #fff;
			border-radius: 4px;
			background: #deb218;
			overflow: hidden;
			padding: 10px 15px 7px;
		}
		
		.mainServiceItem.added>section.page>.content button.remove {
			font-size: 16px;
			font-family: "merriweatherregular", serif;
			color: #fff;
			border-radius: 4px;
			background: #6c6c6c;
			overflow: hidden;
			padding: 10px 15px 7px;
		}
		
		.mainServiceItem>section.page>.content button.remove,
		.mainServiceItem.added>section.page>.content button.add {
			display: none;
		}
		
		.mainServiceItem.added>section.page>.content button.remove {
			display: block;
		}
		
		.mainServiceItem:last-child {
			margin-right: 0;
		}
		
		.lines.one {
			width: 50%;
			border-right: 2px solid black;
			border-bottom: 2px solid black;
			margin-top: 30px;
			height: 30px;
		}
		
		.lines.two {
			width: 100%;
			border-left: 2px solid black;
			height: 120px;
			border-bottom: 2px solid black;
		}
		
		.lines.three {
			width: 100%;
			border-right: 2px solid black;
			height: 120px;
		}
		
		.lines.four {
			width: 50%;
			height: 60px;
			float: left;
			border-top: 2px solid black;
			border-left: 2px solid black;
			position: relative
		}
		
		.lines.fake {
			width: 50%;
			height: 60px;
			float: left
		}
		
		.mobile_steps {
			display: none;
		}
		
		.m_step {
			display: inline-block;
			margin: 10px 1%;
			width: 30%;
			height: 135px;
			background: rgb(255, 255, 255);
			background: -moz-linear-gradient(top, rgba(255, 255, 255, 1) 0%, rgba(229, 229, 229, 1) 100%);
			background: -webkit-linear-gradient(top, rgba(255, 255, 255, 1) 0%, rgba(229, 229, 229, 1) 100%);
			background: linear-gradient(to bottom, rgba(255, 255, 255, 1) 0%, rgba(229, 229, 229, 1) 100%);
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e5e5e5', GradientType=0);
			color: #008600;
		}
		
		.m_step:hover {
			background: rgb(247, 247, 247);
			background: -moz-linear-gradient(top, rgba(247, 247, 247, 1) 0%, rgba(214, 214, 214, 1) 100%);
			background: -webkit-linear-gradient(top, rgba(247, 247, 247, 1) 0%, rgba(214, 214, 214, 1) 100%);
			background: linear-gradient(to bottom, rgba(247, 247, 247, 1) 0%, rgba(214, 214, 214, 1) 100%);
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f7f7f7', endColorstr='#d6d6d6', GradientType=0);
		}
		
		.m_step span {
			display: block;
			text-align: center;
			font-size: 40px;
			padding: 20px 15px;
		}
		
		.m_step p {
			display: block;
			text-align: left;
			font-size: 15px;
			padding: 0 10px 20px;
			margin: 0;
		}
		
		.txt_cn {
			padding: 15px 0 35px 0
		}
		
		@media screen and (max-width:700px) {
			.mobile_steps {
				display: block;
			}
			.layoutboth {
				display: block;
				margin-right: 0!important;
				margin-bottom: 15px;
				float: none;
				width: 98%;
				margin-top: 20px;
				padding: 0 5px!important;
			}
			h1 {
				font-size: 27px;
				text-align: left;
				padding: 0 5px
			}
			.lines {
				border: 0!important;
				display: none;
			}
			.lines.four,
			.lines.fake {
				display: block;
			}
		}
		
		@media screen and (max-width:500px) {
			.m_step {
				width: 47%;
			}
		}
		
		@media screen and (max-width:380px) {
			.m_step {
				display: block;
				max-width: 210px;
				width: 90%;
				margin: 20px auto;
			}
		}

	</style>
	<script>
		function invitecode(id) {


			var w = 510;
			var h = 340;

			var newDiv = document.createElement("div");
			newDiv.id = 'newDiv';
			document.body.appendChild(newDiv);

			var insideHTML = '<div class="blackbg" onclick="return closeDiv()"></div>';

			insideHTML += '<div style="z-index:30;position:fixed;margin:auto;top:50%;left:50%;margin-left:' + (w / 2 - 22) + 'px;margin-top:-' + (h / 2 - 5) + 'px;"></div>';

			insideHTML += '<div class="popup" style="width:' + w + 'px;height:' + h + 'px;margin-left:-' + (w / 2) + 'px;margin-top:-' + (h / 2) + 'px;"><a target="_parent" class="close" href="#" onclick="return closeDiv()">Close</a>';

			insideHTML += '<iframe src="http://keyshock.com/approachsteps.php?id=' + id + '" scrolling="yes" style="width: 100%;height: 495px;border: 0;overflow-x:hidden;"></iframe></div>';

			document.getElementById('newDiv').innerHTML = insideHTML;
			return false;
		}

		function closeDiv() {

			document.body.removeChild(document.getElementById('newDiv'));
			return false;
		}

	</script>
</head>

<body>


	<?=$header ?>

		<div class="txt_cn">


			<div class="cnwidth">

				<h1>How Keyshock Works? 5 Tested And Proven Steps.</h1>


				<div class="layoutboth" style="padding-right:30px;">We refuse to believe in 'one size fits all' generic template CV writing. If you operate at a certain level and want to either progress or change your career, the current market demands a serious, credible CV which is non-formulaic, nor rushed or missing detailed industry insight. As specialists in CV writing at professional and management-level, our skillsets include capturing and wording even the most complex or diverse set of skills.</div>


				<div class="layoutboth">Our superior team of ten, specialize in the highest quality CV, resume and LinkedIn profile writing. What really makes us different is our unrivaled experience at this level for a beginner, professional and management level CVs. The guarantee of our success rates is recognized by the various job board partners that recommend us.</div>


			</div>

		</div>



		<div class="grey_sec benefits_cn">


			<div class="cnwidth">

				<div class="lines one"></div>
				<div class="lines two">
					<div class="number" onclick="return invitecode('1');">01</div>
					<div class="number" onclick="return invitecode('2');">02</div>
					<div class="number" onclick="return invitecode('3');">03</div>
					<div class="number" onclick="return invitecode('4');">04</div>
					<div class="number" onclick="return invitecode('5');">05</div>
				</div>

				<div class="lines three">
					<div class="numberdesc">Establishing<br>your<br>target role</div>
					<div class="numberdesc">Asking<br>purposeful<br>questions</div>
					<div class="numberdesc">Delivery<br>of first<br>draft</div>
					<div class="numberdesc">Any<br>changes you<br>need</div>
					<div class="numberdesc">Your career<br>has been<br>changed</div>
				</div>
				<div class="mobile_steps">
					<div class="m_step" onclick="return invitecode('1');">
						<span>01</span>
						<p>Establishing your target role</p>
					</div>
					<div class="m_step" onclick="return invitecode('2');">
						<span>02</span>
						<p>Asking purposeful questions</p>
					</div>
					<div class="m_step" onclick="return invitecode('3');">
						<span>03</span>
						<p>Delivery of first draft</p>
					</div>
					<div class="m_step" onclick="return invitecode('4');">
						<span>04</span>
						<p>Any changes youneed</p>
					</div>
					<div class="m_step" onclick="return invitecode('5');">
						<span>05</span>
						<p>Your career has been changed</p>
					</div>
				</div>
				<div class="lines fake"></div>
				<div class="lines four">
					<img src="/imgs/next.png" width="35" height="19" style="position: absolute;    left: -18px;    bottom: -1px;"></div>


				<div align="center">
					<div class="mainServiceItem" style="    width: 200px;
    height: 280px;
    margin-top: 50px;
    font-size: 18px;
    color: #004492;
    font-weight: bold;"><img src="/imgs/iconletter.png" style="margin-top: 30px;margin-bottom: 22px;"><br>YOUR NEW LIFETIME<br>PROFESSIONAL<br>CV</div>


					<br><a class="greenbtn" href="/cv-writing">Get Started »</a>

				</div>

			</div>

		</div>






		<?=$footer ?>





</body>

</html>
