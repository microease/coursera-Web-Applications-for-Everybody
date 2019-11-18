<!DOCTYPE html>
<html>
<head>
    <title>microease PHP Test</title>
</head>
<body>
<h1>microease PHP Test</h1>
<p>The SHA256 hash of "Charles Severance" is
    <?php print hash('sha256', 'Charles Severance');?></p>
<pre>ASCII ART:

    ***********
    **       **
    **       **
    **       **
    **       **
    **       **
    ***********
</pre>
<a href="check.php">Click here to check the error setting</a>
<br/>
<a href="fail.php">Click here to cause a traceback</a>
</body>
