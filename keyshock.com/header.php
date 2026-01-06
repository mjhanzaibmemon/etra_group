<?php

$currenturl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

if (strpos($currenturl, 'keyshock.com') !== false) {
    $webname = 'Winning CV';
    $stylelogo = 'winningcvst';
    $logoimg = 'logo2019winningcv';

    $blockedpg = array('/','success-stories','success-rate','contact-us');

    //if((in_array($_SERVER['REQUEST_URI'],$blockedpg))||(strpos($_SERVER['REQUEST_URI'], 'job-search') !== false)){die;}

}else{
    $webname = 'Keys Hock';
    $logoimg = 'logo2018';
}

$jrpackages = array
    (
        '1' => array('id' => '1','title' => 'Professional Growth','pricecut' => '49','price' => '29','percent' => '33%','sku' => 'sku_Ewd6LS1dbJ0Tsn'),
        '2' => array('id' => '2','title' => 'Career Evolution','pricecut' => '69','price' => '39','percent' => '33%','sku' => 'sku_Ewd7xVwQ8ZT54Q'),
        '3' => array('id' => '3','title' => 'Executive Priority','pricecut' => '79','price' => '49','percent' => '33%','sku' => 'sku_Ewd7uOfAcQpnaD')
    );


if($indexhomepage==1)$margintop = '    margin-top: 370px;';

/*$header = '<div align="center" class="headerasd">
    
    <a href="/login" class="recloginbtn navigationbutton" style="float:left;">Recruiter Login</a>
    <a href="/login?postjob=1" class="postajobbtn navigationbutton" style="float:left;margin-left:30px;">Post a job</a>
    <a href="/search-jobs" class="browsebtn navigationbutton" style="float:right;">Browse All Jobs</a>

    <a href="/"><img class="logo" src="/imgs/logo.png"></a></div>';
  */  

$header= '<div align="center" class="mainheader">
  
  <a href="javascript:void(0);" style="
    position: absolute;
    right: 10px;
    top: 29px;
    height: 25px;
    width: 25px;
    text-decoration: none;
    background-image: url(//keyshock.com/imgs/nav-browse-mob.png);
    background-size: 25px;
    background-repeat: no-repeat;" class="mobnavmenu" onclick="myFunction()"></a>

  <div class="headerpos">


  <a class="imgmainlogo '.$stylelogo.'" href="//keyshock.com/"><img border="0" src="/imgs/'.$logoimg.'.png"></a>
     
       
       
    <ul class="menu" id="myTopnav">



    <li><a href="//keyshock.com/">Home</a></li>
    <li><a href="//keyshock.com/cv-writing">CV Services</a></li>
    <li><a href="//keyshock.com/success-stories">Before & After</a></li>
    <li><a href="//keyshock.com/about">About us</a></li>
    <li><a href="//keyshock.com/contact-us">Contact</a></li>
    </ul>   
     

     </div>

</div>

<script>

function myFunction() {
    var x = document.getElementById("myTopnav");
    if (x.className === "menu") {
        x.className += " responsive";
    } else {
        x.className = "menu";
    }
}




</script>

';

$footer = '<div class="footer" align="center">


    <div class="footerholder" align="center">


        <div class="logo"><img border="0" src="/imgs/'.$logoimg.'.png"></div>
        



        <div class="linkholder">
        <div class="footerheader">SERVICE</div>
        <ul class="footernav">
        <li><a href="//keyshock.com/">Home</a></li>
        <li><a href="//keyshock.com/cv-writing">CV Writing</a></li>
        <li><a href="//keyshock.com/success-rate">Success Rate</a></li>
        <li><a href="//keyshock.com/success-stories">Before & After</a></li>
        <li><a href="/contact-us">Contact Us</a></li></ul></div>

        <div class="linkholder">
        <div class="footerheader">PACKAGES</div>
        <ul class="footernav">
        <li><a href="//keyshock.com/cv-writing">Professional Growth CV</a></li>
        <li><a href="//keyshock.com/cv-writing">Career Evolution CV</a></li>
        <li><a href="//keyshock.com/cv-writing">Executive Priority CV</a></li></ul></div>


        <div class="linkholder">
        <div class="footerheader">SUPPORT</div>
        <ul class="footernav">
        <li><a href="//keyshock.com/contact-us">Contact Us</a>
        <li><a href="//keyshock.com/payments">Payments</a></li>
        <li><a href="//keyshock.com/terms-and-conditions">Terms & Conditions</a></li>
        <li><a href="//keyshock.com/privacy-policy">Privacy policy</a></li></ul></div>


        
    </div>
    <div class="copyright">© 2006 - '.date("Y").' Keys Hock CV Experts. Proud American CV specialists.<img style="float:right;" src="/imgs/footerlogos.png"></div>


</div>


   <script>
  (function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');

  ga(\'create\', \'UA-41728467-4\', \'auto\');
  ga(\'send\', \'pageview\');

</script> 

';


?>
