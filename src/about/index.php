<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class About extends Base
{
  function __construct()
  {

  }

  function main(): string
  {
    $body = '
      <p>This website was made so people can learn a lesson from my life. Parents should definitely tell their child(ren) about their diagnosis. It\'s very important so they can know why things are the way they are. <a target="_blank" rel="noopener" href="https://www.aspergerexperts.com/blogs/entry/25-defense-mode-why-people-with-aspergers-seem-stuck-shutdown-so-often/">Defense mode</a> is a very important thing to understand both for the parent and the child. Also, don\'t look at the DSM. Instead, look at <a target="_blank" rel="noopener" href="https://www.aspergerexperts.com/">www.aspergersexperts.com</a>. They have everything there including resourses, explanations, forums, coaching, therapy, etc.</p>
      <p>I recommend professionals look at <a target="_blank" rel="noopener" href="https://www.aspergerexperts.com/products/courses/deep-into-defense-mode/">this defense mode course</a> to understand that people can look like they\'re doing things just to be mean but really, they\'re in defense mode and just scared.</p>';

    return $body;
  }
}

$about = new About();
$about->setUrl($_SERVER['REQUEST_URI']);
$about->setTitle('Ephraim Becker - About');
$about->setLocalStyleSheet(NULL);
$about->setLocalScript(NULL);
$about->setHeader('About');
$about->setBody($about->main());

$about->html();
?>
