<?php

// Tools for drawing page

$pagehead = <<<HTML
<!doctype html>
<meta charset=utf-8>
<title>ganggarrison.com/plugins</title>
<link rel=stylesheet href=style.css>

<div id=head><img src="http://static.ganggarrison.com/GG2ForumLogo.png" alt="" id=logo><img src="http://static.ganggarrison.com/Themes/GG2/images/smflogo.gif" alt="" id=smflogo></div>
<div id=desc>
    <p>This directory provides the source of "server-sent plugins" used in GG2, see the <a href="http://www.ganggarrison.com/forums/index.php?topic=33509">Server-sent plugins FAQ</a>.</p>
</div>

<script>
(function () {
    'use strict';

    window.onload = function () {
        var oldMD5s = document.getElementsByClassName('old-md5');

        for (var i = 0; i < oldMD5s.length; i++) {
            oldMD5s[i].style.display = 'none';
        }

        var para, label, checkbox;
        para = document.createElement('p');
        label = document.createElement('label');
        checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.checked = true;
        checkbox.onclick = checkbox.onchange = function () {
            for (var i = 0; i < oldMD5s.length; i++) {
                oldMD5s[i].style.display = (checkbox.checked) ? 'none' : '';
            }
        };
        label.appendChild(checkbox);
        label.appendChild(document.createTextNode(' Hide MD5 hashes for old plugin versions'));
        para.appendChild(label);

        document.getElementById('desc').appendChild(para);
    };
}());
</script>
HTML;

function draw_row ($row, $type='td') {
    echo "<tr>";
    foreach ($row as $cell) {
        echo "<$type>" . $cell . "</$type>";
    }
    echo "</tr>";
}

function draw_table ($rows, $sort_by = NULL) {
    // Empty rows - we can't draw headings as we derive these from rows
    if (empty($rows))
        return;

    echo "<table>";
    echo "<thead>";
    // Draw header using keys of first row
    reset($rows);
    $columns = array_keys($rows[key($rows)]);
    $headerrow = array();
    foreach ($columns as $name) {
        // Sort by link
        if ($sort_by === $name) {
            $headerrow[] = "▼<a href=?>$name</a>";
        } else {
            $headerrow[] = "<a href=\"?sort_by=$name\">$name</a>";
        }
    }
    draw_row($headerrow, 'th');
    echo "</thead>";
    echo "<tbody>";
    foreach ($rows as $row) {
        draw_row($row);
    }
    echo "</tbody>";
    echo "</table>";
}

// Prepare data to display

$data = json_decode(file_get_contents("../data.json"), true);
$plugindata = $data['plugins'];
$authordata = $data['authors'];
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'name';
if ($sort_by === "name") {
    uksort($plugindata, "strcasecmp");
} else if ($sort_by !== NULL && $sort_by !== "md5s") {
    uasort($plugindata, function ($a, $b) use ($sort_by) {
        return strcasecmp($a[$sort_by], $b[$sort_by]);
    });
}
$plugintable = array();
foreach ($plugindata as $name => $plugin) {
    $removed = isset($plugin['removed']) && $plugin['removed'];
    $row = array(
        'name' => $removed ? "<del>$name</del>" : $name,
        'author' => implode(' ', array_map(function ($s) use ($authordata) {
            if (isset($authordata[$s])) {
                return "<a href=\"http://www.ganggarrison.com/forums/index.php?action=profile;u=$authordata[$s]\">$s</a>";
            } else {
                return $s;
            }
        }, explode(' ', $plugin['author']))),
        'topic' => is_null($plugin['topic']) ? "none" : "<a href=\"http://www.ganggarrison.com/forums/index.php?topic={$plugin['topic']}\">#{$plugin['topic']}</a>",
        'md5s' => ''
    );
    // Removed plugins have no MD5s
    if ($removed) {
        $row['md5s'] = "(removed)";
    // Single MD5 is just a link
    } else if (!(count($plugin['md5s']) > 1)) {
        $md5 = $plugin['md5s'][0];
        $row['md5s'] = "<a href=\"$name@$md5.zip\" class=md5>$md5</a>";
    } else {
    // If we have multiple MD5s, we'll make list with latest and old versions
        $row['md5s'] = "<ul>";
        foreach ($plugin['md5s'] as $index => $md5) {
            $row['md5s'] .= ($index === 0) ? "<li>" : "<li class=old-md5>";
            $row['md5s'] .= "<a href=\"$name@$md5.zip\" class=md5>$md5</a>";
            $row['md5s'] .= ($index === 0) ? "" : " (old)";
            $row['md5s'] .= "</li>";
        }
        $row['md5s'] .= "</ul>";
    }
    $plugintable[] = $row;
}

// Display it!
echo $pagehead;

// Default to sorting by name
draw_table($plugintable, $sort_by);
