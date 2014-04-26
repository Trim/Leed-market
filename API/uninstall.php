<?php

$constant = "<?php
define('PLUGIN_ENABLED','0');
?>";

file_put_contents(Plugin::path().'constantAPI.php', $constant);

?>