<?php

$s = file_get_contents('page-source.txt');

// <p class="alert alert-warning">

// 0298: SELECT DISTINCT &quot;Element&quot;.&quot;ClassName&quot;, &quot;Element&quot;.&quot;LastEdited&quot;, &quot;Element&quot;.&quot;Created&quot;, &quot;BlockA&quot;.&quot;Content&quot;, &quot;Element&quot;.&quot;ID&quot;, 
// 			CASE WHEN &quot;Element&quot;.&quot;ClassName&quot; IS NOT NULL THEN &quot;Element&quot;.&quot;ClassName&quot;
// 			ELSE &#039;DNADesign\\Elemental\\Models\\BaseElement&#039; END AS &quot;RecordClassName&quot;, &quot;Element&quot;.&quot;Sort&quot;
//  FROM &quot;Element&quot; LEFT JOIN &quot;BlockA&quot; ON &quot;BlockA&quot;.&quot;ID&quot; = &quot;Element&quot;.&quot;ID&quot;
//  WHERE (&quot;Element&quot;.&quot;ID&quot; = ?)
//  AND (&quot;Element&quot;.&quot;ClassName&quot; IN (?))
//  ORDER BY &quot;Element&quot;.&quot;Sort&quot; ASC
//  LIMIT 1
// 0.0003s
// </p>

preg_match_all('#<p class="alert alert\-warning">(.+?)</p>#s', $s, $m);

$total_time = 0;
$total_count = 0;

$res = [];
foreach ($m[1] as $s) {
    preg_match('#([0-9]+): (.+?)([0-9\.]+)s#s', $s, $m2);
    $sql = $m2[2];
    $sql = html_entity_decode($sql);
    $sql = trim($sql);
    $res[$sql] ??= ['count' => 0, 'time' => 0, 'sql' => $sql];
    $res[$sql]['count']++;
    $res[$sql]['time'] += $m2[3];
    $total_time += $m2[3];
    $total_count++;
}
usort($res, function ($a, $b) use ($res) {
    return $a <=> $b;
});
$res = array_reverse($res);

foreach ($res as $sql => &$data) {
    $data['time'] = round($data['time'], 4);
}

$tt = round($total_time, 4);
$tc = $total_count;
echo "Wrote to output.txt\n";
file_put_contents('output.txt', implode("\n\n", [
    "Total time: $tt",
    "Total count: $tc",
    var_export($res, true)
]));
