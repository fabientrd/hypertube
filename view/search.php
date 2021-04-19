<?php
/**
 * Created by PhpStorm.
 * User: pimaglio
 * Date: 2019-02-12
 * Time: 11:59
 */
if (!isset($_SESSION))
    session_start();

if (!isset($_SESSION['loggued_on_user']))
    header("Location: ../index.php");

include('header_connect.php');


?>

<div class="container">
    <div class="search_container row">
        <form method="GET" action="./search.php">
            <div class="input-field col s12">
                <p class="fw100"><i class="fas fa-film icon_spacing2"></i><?php echo $titlemovie ?></p>
                <input id="film" type="text" class="validate" pattern="[A-Za-z0-9\séèâêëçû -]+" name="film"
                       value="<?php if (isset($_GET['film'])) echo $_GET['film'] ?>">
                <span class=\"helper-text\" data-error=\"fewfefewf\" data-success=\"$formnameSuccess\"></span>
            </div>
            <div class="col s3">
                <p class="fw100"><i class="fas fa-calendar-minus icon_spacing2"></i><?php echo $rangedatemin ?></p>
                <p class="range-field">
                    <input name="datemin" type="range" id="datemin"
                           value="<?php if (isset($_GET['datemin'])) echo $_GET['datemin']; else echo '1940' ?>"
                           min="1940" max="2019"/>
                </p>
            </div>

            <div class="col s3">
                <p class="fw100"><i class="fas fa-calendar-plus icon_spacing2"></i><?php echo $rangedatemax ?></p>
                <p class="range-field">
                    <input name="datemax" type="range" id="datemax"
                           value="<?php if (isset($_GET['datemax'])) echo $_GET['datemax']; else echo '2019' ?>"
                           min="1940" max="2019"/>
                </p>
            </div>

            <div class="col s3">
                <p class="fw100"><i class="fas fa-sort-amount-down icon_spacing2"></i><?php echo $scoremin ?></p>
                <p class="range-field">
                    <input name="notemin" type="range" id="test5"
                           value="<?php if (isset($_GET['notemin'])) echo $_GET['notemin']; else echo '0' ?>" min="0"
                           max="10"/>
                </p>
            </div>

            <div class="col s3">
                <p class="fw100"><i class="fas fa-sort-amount-up icon_spacing2"></i><?php echo $scoremax ?></p>
                <p class="range-field">
                    <input name="notemax" type="range" id="test5"
                           value="<?php if (isset($_GET['notemax'])) echo $_GET['notemax']; else echo '10' ?>" min="0"
                           max="10"/>
                </p>
            </div>

            <?php
            echo "
            <div class=\"input-field col s12\">
            <p class=\"fw100\"><i class=\"fas fa-tape icon_spacing2\"></i>Genre(s)</p>
                <select name='genre'>
                    <option value='' disabled selected>$titleselect</option>
                    <option value='action'>$genreAction</option>
                    <option value='adventure'>$genreAdventure</option>
                    <option value='animation'>$genreAnimation</option>
                    <option value='comedy'>$genreComedy</option>
                    <option value='crime'>$genreCrime</option>
                    <option value='documentary'>$genreDocumentary</option>
                    <option value='drama'>$genreDrama</option>
                    <option value='family'>$genreFamily</option>
                    <option value='fantasy'>$genreFantasy</option>
                    <option value='history'>$genreHistory</option>
                    <option value='horror'>$genreHorror</option>
                    <option value='music'>$genreMusic</option>
                    <option value='mystery'>$genreMystery</option>
                    <option value='romance'>$genreRomance</option>
                    <option value='science fiction'>$genreSciencefi</option>
                    <option value='tv movie'>$genreTvMovie</option>
                    <option value='thriller'>$genreThriller</option>
                    <option value='war'>$genreWar</option>
                    <option value='western'>$genreWestern</option>
                </select>
            </div>
                <div class=\"row\">
                    <div class=\"col s3\">
                        <p>
                            <label>
                                <input value=\"nosort\" class=\"with-gap pulse\" name=\"sort\" type=\"radio\" checked/>
                                <span>$trino</span>
                            </label>
                        </p>
                    </div>
                    <div class=\"col s3\">
                        <p>
                            <label>
                                <input value=\"sortnote\" class=\"with-gap pulse\" name=\"sort\" type=\"radio\"/>
                                <span>$trinote</span>
                            </label>
                        </p>
                    </div>
                    <div class=\"col s3\">
                        <p>
                            <label>
                                <input value=\"sortrecent\" class=\"with-gap pulse\" name=\"sort\" type=\"radio\"/>
                                <span>$trirecent</span>
                            </label>
                        </p>
                    </div>
                </div>
                <div >
                    <button class=\" btn_search btn-large waves-effect waves-light pink accent-3 fade-in two\">$srcbtn
                        <i style=\"position: absolute\" class=\"fab fa-searchengin icon_spacing3\"></i>
                    </button>
                </div>
            </div>
            ";
            ?>

        </form>
    </div>


</div>

<div class="flexgallery" id="post-data">

    <?php
    if (isset($_GET['film']) && !empty($_GET['film']) && isset($_GET['datemin']) && isset($_GET['datemax']) && isset($_GET['notemin'])
        && isset($_GET['notemax']) && isset($_GET['sort'])) {
        if (htmlspecialchars($_GET['film']) != $_GET['film'] || $_GET['datemin'] != htmlspecialchars($_GET['datemin']) || $_GET['datemax'] != htmlspecialchars($_GET['datemax'])
            || $_GET['notemin'] != htmlspecialchars($_GET['notemin']) || $_GET['notemax'] != htmlspecialchars($_GET['notemax'])) {
            return ;
        } else {
            $film = $_GET['film'];
            $datemin = $_GET['datemin'];
            $datemax = $_GET['datemax'];
            $notemin = $_GET['notemin'];
            $notemax = $_GET['notemax'];
            $sort = $_GET['sort'];

            if (!isset($_GET['last_id']))
                $idf = 0;
            else
                $idf = $_GET['last_id'];
            $data = recup_search($film, $datemin, $datemax, $notemin, $notemax, $sort);
            $_SESSION['search'] = $data;
            $count = count($_SESSION['search']);
            echo "<h6 class=\"homesuggest center\">$count $titleresult</h6>";
            include('DataSearch.php');
        }
    }

    if (isset($_GET['film']) && empty($_GET['film']) && isset($_GET['datemin']) && isset($_GET['datemax']) && isset($_GET['notemin'])
        && isset($_GET['notemax']) && isset($_GET['sort']) && isset($_GET['genre'])) {
        if (htmlspecialchars($_GET['film']) != $_GET['film'] || $_GET['datemin'] != htmlspecialchars($_GET['datemin']) || $_GET['datemax'] != htmlspecialchars($_GET['datemax'])
            || $_GET['notemin'] != htmlspecialchars($_GET['notemin']) || $_GET['notemax'] != htmlspecialchars($_GET['notemax']) || htmlspecialchars($_GET['genre']) != $_GET['genre']) {
            return ;
        } else {
            $datemin = $_GET['datemin'];
            $datemax = $_GET['datemax'];
            $notemin = $_GET['notemin'];
            $notemax = $_GET['notemax'];
            $sort = $_GET['sort'];
            $genre = $_GET['genre'];

            if (!isset($_GET['last_id']))
                $idf = 0;
            else
                $idf = $_GET['last_id'];
            $data = recup_genre($genre, $datemin, $datemax, $notemin, $notemax, $sort);
            $_SESSION['search'] = $data;
            $count = count($_SESSION['search']);
            echo "<h6 class=\"homesuggest center\"> $count $titleresult</h6>";
            include('DataSearch.php');
        }
    }

    ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var win = $(window);

        // Each time the user scrolls
        win.scroll(function () {
            // End of the document reached?
            if ($(document).height() - win.height() === win.scrollTop()) {
                var last_id = $(".post-id:last").attr("id");

                $.ajax({
                    url: '/view/SearchMoreData.php?last_id=' + last_id,
                    dataType: 'html',
                    type: "GET",
                    success: function (html) {
                        $('#post-data').append(html);
                    }
                });
            }
        });
    });

    $(document).ready(function () {
        $('.tooltipped').tooltip();
    });

</script>

<script>
    $(document).ready(function () {
        $("select").formSelect();
    });
</script>

<script src="assets/js/materialize.js"></script>
</body>