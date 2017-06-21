<?php

/**
 * Created by PhpStorm.
 * User: bruno
 * Date: 16/08/16
 * Time: 09:31
 */
class ConfigClass {

    //const bdHost = 'dev';
    const bdHost = 'localhost';

    const bdPort = '3306';

    const bdName = 'acp';

    //const bdUser = 'acp';
    const bdUser = 'root';

    const bdCharset = 'utf8';

    //const bdPasswd = '12345';
    const bdPasswd = '';

    const imagesDirectory = 'images/';

    const pw_iter = 30000;

    const pw_algo = 'sha256';

    const pw_saltLength = 32;

    const pw_outputLength = 64;

}