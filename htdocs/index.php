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
HTML;

function draw_row ($row, $type='td') {
    echo "<tr>";
    foreach ($row as $cell) {
        echo "<$type>" . (is_null($cell) ? 'null' : $cell) . "</$type>";
    }
    echo "</tr>";
}

function draw_table ($rows, $sort_by = NULL) {
    // Empty rows - we can't draw headings as we derive these from rows
    if (empty($rows))
        return;

    // Sort rows
    if ($sort_by !== NULL)
        usort($rows, function ($a, $b) use ($sort_by) {
            return strcasecmp($a[$sort_by], $b[$sort_by]);
        });

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
            $headerrow[] = "<a href=?sort_by=$name>$name</a>";
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

$plugindata = json_decode(file_get_contents("../data.json"), true);
$plugindata = $plugindata['plugins'];
$plugintable = array();
foreach ($plugindata as $name => $plugin) {
    $row = array(
        'name' => $name,
        'author' => $plugin['author'],
        'topic' => "<a href=\"/forums/index.php?topic={$plugin['topic']}\">#{$plugin['topic']}</a>",
        'md5' => ''
    );
    foreach ($plugin['md5s'] as $index => $md5) {
        $row['md5'] .= "<a href=\"$name@$md5.zip\" class=md5>$md5</a>";
        // If we have > 1 MD5s, we'll mark latest and old versions
        if (count($plugin['md5s']) > 1) {
            $row['md5'] .= ($index === 0) ? " (latest)" : " (old)";
            // Newlines needed to separate
            if ($index !== count($plugin['md5s']) - 1)
                $row['md5'] .= "<br>";
        }
    }
    $plugintable[] = $row;
}

// Display it!
echo $pagehead;

// Default to sorting by name
draw_table($plugintable, isset($_GET['sort_by']) ? $_GET['sort_by'] : 'name');
