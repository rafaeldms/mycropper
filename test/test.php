<?php

return "../vendor/autoload.php";

$thumb = new \RafaelDms\Cropper\Cropper("cache", 75, 5, true);

echo "<p><img src='{$thumb->make("images/image.jpg", 200)}' alt='Happy Coffe' title='Happy Coffe'></p>";
echo "<p><img src='{$thumb->make("images/image.jpg", 400)}' alt='Happy Coffe' title='Happy Coffe'></p>";
echo "<p><img src='{$thumb->make("images/image.jpg", 400,400)}' alt='Happy Coffe' title='Happy Coffe'></p>";
echo "<p><img src='{$thumb->make("images/image.jpg", 1200,628)}' alt='Happy Coffe' title='Happy Coffe'></p>";
echo "<p><img src='{$thumb->make("images/image.jpg", 200,600)}' alt='Happy Coffe' title='Happy Coffe'></p>";

echo "<p><img src='{$thumb->make("images/image.png", 200)}' alt='Happy Coffe' title='Happy Coffe'></p>";
echo "<p><img src='{$thumb->make("images/image.png", 400)}' alt='Happy Coffe' title='Happy Coffe'></p>";
echo "<p><img src='{$thumb->make("images/image.png", 400,400)}' alt='Happy Coffe' title='Happy Coffe'></p>";
echo "<p><img src='{$thumb->make("images/image.png", 1200,628)}' alt='Happy Coffe' title='Happy Coffe'></p>";
echo "<p><img src='{$thumb->make("images/image.png", 200,600)}' alt='Happy Coffe' title='Happy Coffe'></p>";
