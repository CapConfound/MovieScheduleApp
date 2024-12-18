<?php

namespace App\Helper;

class KinomaxSelectorDictionary
{

    const string OUTER_MOVIE_DIV = 'div.XGEM9bNiZvg0iY5iYWVg';

    const string INNER_MOVIE_DIV = 'div.ZUq0AhDQjrcS6th1rYOS';

    /*
     * <a class="sBTfpRQClyiwMhw2E1K1" href="/filmdata/8031">
     *      <img class="IS6dGChmFO4oMx7A7MAj" src="https://images.kinomax.ru/550/films/8/8031/p_90a8bc7.ebp" alt="Гарри Поттер и Орден Феникса (предсеанс. обсл) &amp; Краски 1" loading="lazy">
     * </a>
     */
    const string IMAGE_LINK = 'a.sBTfpRQClyiwMhw2E1K1';

    /*
     * <img class="IS6dGChmFO4oMx7A7MAj" src="https://images.kinomax.ru/550/films/8/8031/p_90a8bc7.ebp" alt="Гарри Поттер и Орден Феникса (предсеанс. обсл) &amp; Краски 1" loading="lazy">
     */
    const string IMAGE_TAG = 'img.IS6dGChmFO4oMx7A7MAj';

    const string SESSIONS_DIV = 'div.esp_aX1LgupqcBj40KpO';

    const string SESSION_DIV = 'a.a1DGomhf4lH5LTLg921s';

    const string SESSION_TIME = 'span.r_gzS2BkVe5yHxcYwfkK';

    const string SESSION_PRICE = 'span.J52YROaNVTJ8YnB4LPeK';

    // 2d|3d
    const string SESSION_FORMAT = 'span.bKz7Y2h6HySrp2rT6052';
}
