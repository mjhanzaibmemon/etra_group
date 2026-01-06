<?

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
header('Content-type: text/html; charset=utf-8');

include('db.php');
include('header.php');
include('packages.php');

$tpl = file_get_contents('cv-testimonial-temp.html');

$tpl = str_replace('{header}',$header,$tpl);
$tpl = str_replace('{footer}',$footer,$tpl);

$id = $_GET['id'];

if(empty($id)){die('404 - Not Found');}

$tests = array
    (
 	'1' => array(
 		'img' => '5',
		'name' => 'Maria',
		'quote1' => 'Emphasize her social skills proficiency and demonstrate the quality of those skills.',
		'exp' => '3+',
		'previousjob' => 'Counselor, working at SAdAS',
		'goal' => 'Unsure of her next steps, bored at current job as she was working there for over 7 years.',
		'challenge1' => 'Maria was searching for her dream job, but wasn’t sure how to get it. She needed to define her relevant job skills and package them to attract the right job opportunities.',
		'challenge2' => 'Maria was still relatively new to the workforce and didn’t have any set career goals. She wanted to use her communication skills, but wasn’t sure in what type of job or how to package her skills appealingly. Maria barely had a CV to begin with, so she needed one that spoke to these specific assets, in a professional-looking CV format.',
		'quote2' => 'The initial impact that [the CV] has when it looks so well done gives a whole different impression.',
		'howhelped' => 'Maria bought the Professional Growth CV-writing package, which includes a CV and two revisions. He was paired with a professional CV writer from his industry, Steven, who worked with him to:',
		'bulletp1' => 'Emphasize her social skills proficiency and demonstrate the quality of those skills.',
		'bulletp2' => 'Demonstrate how her social skills and foreign language skills provided value in the workplace.',
		'bulletp3' => 'Optimize her CV with more industry-specific keywords.',
		'before' => 'maria-before',
		'after' => 'maria-after',
		'quote3' => 'The first interview I got was my dream job with NHS. I was so excited — they were talking about all this travel that I would get to do. Working with Winning CV gave me a lot of confidence.'),

 	'2' => array(
 		'img' => '3',
		'name' => 'Aliz',
		'quote1' => 'I really wanted to make a career switch. I knew I had to do something very different with my CV. I just didn’t know where to start to give the best impression.',
		'exp' => '13+',
		'previousjob' => 'Support Worker, Children’s Social Care',
		'goal' => 'Failed the six-second test, needed a CV that highlighted her qualifications.',
		'challenge1' => 'Aliz had great work experience, but her job accomplishments were buried in her CV. She needed help highlighting her selling point and telling her career story.',
		'challenge2' => 'Aliz had a lot of experience in being a support worker, but she was looking to change careers using her teaching skills in a different industry. If she wanted to switch careers, she needed to tell a better story and highlight how her work experience and technical skills could add value to a new employer. Aliz’s CV was too long, and her achievements were buried too deep to be noticed by recruiters.',
		'quote2' => 'The CV was well beyond what I thought I would get back with the amount of effort I put in.',
		'howhelped' => '
Aliz bought the Executive Priority CV-writing package, which includes a CV, a cover letter, and LinkedIn makeover. She was paired with our certified CV writer, Jonathan from our team of 10, who worked with her to:',
		'bulletp1' => 'Highlight career accomplishments that were relevant to the job applications she was targeting.',
		'bulletp2' => 'Develop a career statement and areas of expertise section to set the tone.',
		'bulletp3' => 'Limit the details of her early career history to take her CV down to two pages.',
		'before' => 'aliz-before',
		'after' => 'aliz-after',
		'quote3' => 'Now I’ve got a great secondary school that I can stand behind and be proud of — one that I feel values me as an employee.'),

 	'3' => array(
 		'img' => '1',
		'name' => 'Joshua',
		'quote1' => 'I wrote my own CV and it just wasn’t going anywhere. I didn’t know what was wrong and I desperately needed to change jobs due to my circumstance.',
		'exp' => '14+',
		'previousjob' => 'Checkout operator',
		'goal' => 'Failed the six second test, needed to use core skills to enter a new career.',
		'challenge1' => 'Joshua spent a numerous amount of years stuck in the same industry not being able to use a powerful CV to land him a new career. He needed help telling his career story in a cohesive way to land his dream job.',
		'challenge2' => ' Joshua spent most of his career in customer service and checkout operation. Upon coming to the United Kingdom, he had his experience from his previous job in Ghana to land him a potential job in the UK. Due to this being an insecurity, he stuck with staying at his current profession for a long time until we came and brought his selling points to a new desired industry: banking and finance.',
		'quote2' => 'I just didn\'t feel confident sending my CV out. I knew I wasn\'t going to get any callbacks and I didn\'t know how to make it better due to my past work experience.',
		'howhelped' => '
Joshua bought the Career Evolution CV-writing package, which includes a CV, cover letter, and two CV revisions. He was paired with a professional CV writer, David, who worked with him to:',
		'bulletp1' => 'Identify skills used in his recent work that were transferrable to his target job.',
		'bulletp2' => 'Craft a professional work summary at the top, calling out his career goals and qualifications.',
		'bulletp3' => 'Reformat his CV, giving it a clean, polished look.',
		'before' => 'joshua-before',
		'after' => 'joshua-after',
		'quote3' => 'The first time I had received the CV through email, I immediately applied for Barclays banking. From there they offered me a cashier position based on the highlighted skillsets and experiences. Technically, I\'ve doubled my salary, pleased with the CV!'),

 	'4' => array(
 		'img' => '2',
		'name' => 'Bradley ',
		'quote1' => 'I just didn\'t feel confident sending my CV out. I knew I wasn\'t going to get any callbacks and I didn\'t know how to make it better.',
		'exp' => '3+',
		'previousjob' => 'Security Specialist',
		'goal' => 'Wanted to change industries, needed a CV that told a better story.',
		'challenge1' => 'Bradley wanted to transition from one industry to another. He needed help highlighting the job skills he had that could translate when changing careers.',
		'challenge2' => 'Bradley had been working in the nonprofit world for a while, and was looking to make a change to the financial services industry. She was eager to use her budget management experience, but was unsure how to play up her job skills in a way that would attract employers. Her original CV simply didn’t give recruiters a good idea of her career goals.',
		'quote2' => 'My writer Gareth was able to pull out a better story. When I saw the skills that Gareth highlighted for me, I was able to think, ‘Oh right, I am good at that.',
		'howhelped' => '
Bradley bought the Career Evolution package, which includes a CV, cover letter, and two CV revisions. He was paired with a professional CV writer, Gareth, who worked with him to:',
		'bulletp1' => 'Figure out which job skills would be most valuable in his target career field.',
		'bulletp2' => 'Highlight these job skills throughout her professional summary and work experience.',
		'bulletp3' => 'Added proper keywords to ensure her CV would pass the recruiting software.',
		'before' => 'bradley-before',
		'after' => 'bradley-after',
		'quote3' => 'With the new CV, I feel more confident. I’m not great at talking about myself or promoting myself so it was great to have someone else do that.'),


 	'5' => array(
 		'img' => '4',
		'name' => 'Paul ',
		'quote1' => 'I really wanted to make a career switch with my management skills. I knew I had to do something very different with my CV.',
		'exp' => '22+',
		'previousjob' => 'Retirement Property Manager',
		'goal' => 'Failed the six-second test, needed a CV that truly highlighted his experience.',
		'challenge1' => 'Paul had exceptional work experience, but his job accomplishments were buried in his CV. He needed help highlighting his selling point and telling his career story.',
		'challenge2' => 'Paul had a lot of experience in retirement property schemes, but he was looking to change careers using his management skills in a different industry. If he wanted to switch careers, he needed to tell a better story and highlight how his work experience and technical skills could add value to a new employer. Paul’s CV was too long, and his achievements were buried too deep to be noticed by recruiters.',
		'quote2' => 'The CV was well beyond what I thought I would get back with the amount of effort I put in.',
		'howhelped' => 'Paul bought the Executive Priority CV-writing package, which includes a CV, a cover letter, and LinkedIn makeover. He was paired with a top certified CV writer, Elliot, who worked with him to:',
		'bulletp1' => 'Highlight career accomplishments that were relevant to the job applications he was targeting.',
		'bulletp2' => 'Develop a career statement and areas of expertise section to set the tone.',
		'bulletp3' => 'Limit the details of his early career history to take his CV down to two pages.',
		'before' => 'paul-before',
		'after' => 'paul-after',
		'quote3' => 'Now I’ve got a great company that I can stand behind and be proud of — one that I feel values me as in a senior position.'),


    );




if(!empty($_GET['opened'])){

$_GET['opened'] = addslashes($_GET['opened']);

mysql_query("UPDATE `cvlist` SET `opened` = '1' WHERE `md5` =  '{$_GET['opened']}' LIMIT 1");

}




$tpl = str_replace('{img}', $tests[$id]['img'], $tpl);
$tpl = str_replace('{name}', $tests[$id]['name'], $tpl);
$tpl = str_replace('{quote1}', $tests[$id]['quote1'], $tpl);
$tpl = str_replace('{exp}', $tests[$id]['exp'], $tpl);
$tpl = str_replace('{previousjob}', $tests[$id]['previousjob'], $tpl);
$tpl = str_replace('{goal}', $tests[$id]['goal'], $tpl);
$tpl = str_replace('{challenge1}', $tests[$id]['challenge1'], $tpl);
$tpl = str_replace('{challenge2}', $tests[$id]['challenge2'], $tpl);
$tpl = str_replace('{quote2}', $tests[$id]['quote2'], $tpl);
$tpl = str_replace('{howhelped}', $tests[$id]['howhelped'], $tpl);
$tpl = str_replace('{bulletp1}', $tests[$id]['bulletp1'], $tpl);
$tpl = str_replace('{bulletp2}', $tests[$id]['bulletp2'], $tpl);
$tpl = str_replace('{bulletp3}', $tests[$id]['bulletp3'], $tpl);
$tpl = str_replace('{before}', $tests[$id]['before'], $tpl);
$tpl = str_replace('{after}', $tests[$id]['after'], $tpl);
$tpl = str_replace('{quote3}', $tests[$id]['quote3'], $tpl);


$tpl = str_replace('{allpackages}', $allpackages, $tpl);

echo $tpl;

?>