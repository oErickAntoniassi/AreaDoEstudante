<?php
function sanitize($input) {
    return htmlspecialchars(trim($input));
}

function redirect($url) {
    header("Location: $url");
    exit;
}
?>