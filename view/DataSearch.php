<?php
/**
 * Created by PhpStorm.
 * User: pimaglio
 * Date: 2019-04-30
 * Time: 00:19
 */

if (isset($_SESSION['lang'])) {
    if ($_SESSION['lang'] === 'en')
        include '../controllers/en.php';
    if ($_SESSION['lang'] === 'fr')
        include '../controllers/fr.php';
}

$i = $idf + 5;
for ($idf; $idf < $i; $idf++) {
    if (!isset($data[$idf]))
        break ;
    if ($_SESSION['lang'] === 'en')
        $title = $data[$idf]['title'];
    else
        $title = $data[$idf]['title_fr'];
    if ($_SESSION['lang'] === 'en')
        $des = $data[$idf]['description'];
    else
        $des = $data[$idf]['description_fr'];
    $date = substr($data[$idf]['creation_date'], 0, 4);
    $note = $data[$idf]['note'];
    $id = $idf + 1;
    $img = $data[$idf]['image'];
    $title_alt = $title;
    $idurl = $data[$idf]['id'];
    $count = strlen($title);
    $svganim = "
    <svg x=\"0\" y=\"0\" width=\"100px\" height=\"100px\" viewBox=\"0 0 200 200\" class=\"play play--ripple\">
    <circle fill=\"transparent\" stroke=\"#E50812\" stroke-width=\"8\" cx=\"100\" cy=\"100\" r=\"96\"/>
    <circle clip-path=\"url(#clipper)\" fill=\"url(#ripple)\" cx=\"100\" cy=\"100\" r=\"120\" />
    <polygon fill=\"#E50812\" points=\"70.993,60.347 153.398,102.384 70.993,144.42   \"/> 
    </svg>

    ";
    if ($count > 28)
        $title = substr($title, 0, 28) . '...';
    if (already_seen($idurl) == 1)
        $btnview = "<div class=\"dejavu\">
                        <span class=\"new badge red\" data-badge-caption=\"$titledejavu\"></span>
                    </div>";
    else
        $btnview = "";
    echo "
        <div class=\"post-id card_movie fade-in two\" id='$id'>
        <a href='movie.php?id=$idurl'>
            <div style=\"background-image: url('$img')\" class=\"image_movie\">
                $btnview
                <svg x=\"0\" y=\"0\" width=\"0\" height=\"0\">
        <defs>
            <clipPath id=\"clipper\">
                <circle cx=\"100\" cy=\"100\" r=\"93\" />
            </clipPath>
            <radialGradient id=\"ripple\">
                <stop offset=\"0%\" stop-color=\"#000\" stop-opacity=\"0.25\">
                    <animate attributeName=\"offset\" values=\"0;0.70\" begin=\"0.2s\" dur=\"0.8s\" repeatCount=\"indefinite\" />
                </stop>
                <stop offset=\"5%\" stop-color=\"#5d5d5d\" stop-opacity=\"0.25\">
                    <animate attributeName=\"offset\" values=\"0.05;0.75\" begin=\"0\" dur=\"0.8s\" repeatCount=\"indefinite\" />
                </stop>
                <stop offset=\"10%\" stop-color=\"#000\" stop-opacity=\"0.25\">
                    <animate attributeName=\"offset\" values=\"0.10;1\" begin=\"0\" dur=\"0.8s\" repeatCount=\"indefinite\" />
                </stop>  
            </radialGradient>
        </defs>
    </svg>  
            </div>
            <div id='scroll-over'>
            <div class=\"card_movie_hover\">
                <div id='scroll-over'>
                    <p class='synops synops-title'>Synopsis:</p>
                    <p class='synops synopsdes'>$des</p>
                </div>
                <div class='animsvg'>
                    $svganim
                </div>
            </div>
            </div>
            <div class=\"card_movie_info\">
                <p data-position=\"top\" data-tooltip=\"$title_alt\" class=\"card_movie_title tooltipped\">$title</p>
                <div>
                    <p class=\"card_movie_year\"><i style=\"color: #D32F2F\" class=\"material-icons left\">movie</i>$date</p>
                    <p class=\"rate\"><i style=\"color: #ffab00\" class=\"material-icons left\">stars</i>$note / 10</p>
                </div>
            </div>
        </a>
        </div>
        ";
}
?>
<script>
    $(document).ready(function(){
        $('.tooltipped').tooltip();
    });
</script>
