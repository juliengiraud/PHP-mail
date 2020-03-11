<?php


// Fonction de vérification d'email
function sanitize_my_email($field) {
    $field = filter_var($field, FILTER_SANITIZE_EMAIL);
    return filter_var($field, FILTER_VALIDATE_EMAIL);
}

// Récupération des données à envoyer
if (@$_POST['email_expediteur'] && @$_POST['email_destinataire']) {
    $email_expediteur = $_POST['email_expediteur'];
    $nom_expediteur = @$_POST['nom_expediteur'] ? wordwrap($_POST['nom_expediteur'], 70, "\r\n") : '';
    $email_destinataire = $_POST['email_destinataire'];
    $email_cc = @$_POST['email_cc'] ? $_POST['email_cc'] : '';
    $email_bcc = @$_POST['email_bcc'] ? $_POST['email_bcc'] : '';
    $objet = @$_POST['objet'] ? wordwrap($_POST['objet'], 70, "\r\n") : '';
    $contenu = @$_POST['contenu'] ? wordwrap($_POST['contenu'], 70, "\r\n") : '';

    $headers = array(
        'MIME-Version' => '1.0',
        'Content-type' => 'text/html;charset=UTF-8',
        'From' => $nom_expediteur . '<' . $email_expediteur . '>',
        'Cc' => $email_cc,
        'Bcc' => $email_bcc,
        'X-Mailer' => 'PHP/' . phpversion()
    );

    // Envoi du mail
    if (
        sanitize_my_email($email_expediteur)
        && sanitize_my_email($email_destinataire)
        && ($email_cc == '' || sanitize_my_email($email_cc))
        && ($email_bcc == '' || sanitize_my_email($email_bcc))
    ) {
        if (mail($email_destinataire, $objet, $contenu, $headers))
            $msg = 'Mail envoyé sans problème';
        else
            $msg = 'Erreur dans l\'envoi du mail';
    }
    else
        $msg = 'Erreur dans les données du mail';

    // Sauvegarde de la copie du mail
    $log = date("Y-m-d H:i:s") . "\n" . var_export($_POST, true) . "\n\n";
    $log_file = fopen('log/log.txt', 'a+');
    fwrite($log_file, $log);
}

$adresse = 'https://www.mail.julien-giraud.fr';
$title = 'Messagerie personnalisable';
$noindex = false;
$description = 'Envoyez des emails depuis n\'importe quelle adresse mail';
$keywords = 'messagerie, mail, email, courriel, adresse';
$canonical = '';
$lastupdate = '11/03/2020';

?>

<html lang="fr">
    <head>
        <!-- Site -->
        <meta charset="utf-8">
        <meta name="author" content="Julien Giraud">
        <meta name="copyright" content="<?= $adresse ?>">
        <meta name="language" content="fr">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" href="<?= $adresse ?>/images/favicon.png">

        <!-- CSS -->
        <link rel="stylesheet" type="text/css" href="<?= $adresse ?>/css/normalize.css">
        <link rel="stylesheet" type="text/css" href="<?= $adresse ?>/css/icons.css">
        <link rel="stylesheet" type="text/css" href="<?= $adresse ?>/css/fonts.css">
        <link rel="stylesheet" type="text/css" href="<?= $adresse ?>/css/variables.css">
        <link rel="stylesheet" type="text/css" href="<?= $adresse ?>/css/global.css">
        <link rel="stylesheet" type="text/css" href="<?= $adresse ?>/css/styles.css">

        <!-- Page -->
        <title><?= $title ?></title>
        <?= ($noindex ? '<meta name="robots" content="noindex">' : '') . "\n" ?>
        <meta name="description" content="<?= $description ?>">
        <meta name="keywords" content="<?= $keywords ?>">
        <link rel="canonical" href="<?= $adresse . $canonical ?>">
    </head>

    <body>
        <header>
            <nav id="navbar" class="navbar up">
                <div class="navbar-container">
                    <a class="brand" href="<?= $adresse ?>" title="Messagerie personnalisable">
                        <img src="<?= $adresse ?>/images/favicon.png" alt="icone accueil" class="icon mr-4">
                        Accueil
                    </a>
                </div>
            </nav>
        </header>

        <section>
            <div class="container">
                <h1>Messagerie personnalisable</h1>

                <?php if(@$msg): ?>
                    <p><?= $msg ?></p>
                    <p class="activator"><i class="fa fa-angle-down fa-lg bold mr-2"></i>Afficher le détail du mail envoyé (avancé)</p>
                    <pre class="to-active"><?= var_dump($_POST) ?></pre>
                    <a class="mt-4" href="<?= $adresse ?>" title="Nouveau mail">Envoyer un nouveau mail</a>

                <?php else: ?>
                    <p>Il faut savoir que le langage PHP permet d'envoyer des emails. On peut se dire que ce n'est pas plus pratique d'une boite mail à première vue... sauf que la fonction <a href="https://www.php.net/manual/fr/function.mail.php" title="Documentation PHP mail()">mail</a> de PHP nous permet de personnaliser tous les champs qui composent un mail. <strong>Y compris l'adresse de l'expéditeur.</strong><br/>
                    En théorie on peut supposer que les boites mails se rendent compte quand un email ne vient pas du serveur habituel. En pratique il semble que pas du tout ! Et pire encore : lorsqu'une photo de profil est associée à une adresse email, la photo s'affiche peut importe le serveur d'où vient le mail.</p>
                    <p>Ce site vous permet de tester le fonctionnement de la fonction mail. Vous pouvez entrer le contenu de votre mail dans le formulaire ci-dessous.</p>
                    <form action="<?= $adresse ?>" method="post">
                        <p>
                            <label for="email_expediteur"><strong>Adresse email de l'expéditeur *</strong></label><br/>
                            <input id="email_expediteur" type="email" name="email_expediteur" required="required" />
                        </p>
                        <p>
                            <label for="nom_expediteur"><strong>Nom de l'expéditeur</strong></label><br/>
                            <input id="nom_expediteur" autocomplete="nom_expediteur" type="text" name="nom_expediteur" />
                        </p>
                        <p>
                            <label for="email_destinataire"><strong>Adresse email du destinataire *</strong></label><br/>
                            <input id="email_destinataire" autocomplete="off" type="email" name="email_destinataire" required="required" />
                        </p>
                        <input class="hidden-input" autocomplete="nope" type="email" />
                        <p class="mt-0">
                            <label for="email_cc"><strong>Adresse email cc (copie)</strong></label><br/>
                            <input id="email_cc" type="email" name="email_cc" />
                        </p>
                        <input class="hidden-input" autocomplete="nope" type="email" />
                        <p class="mt-0">
                            <label for="email_bcc"><strong>Adresse email bcc (copie cachée)</strong></label><br/>
                            <input id="email_bcc" type="email" name="email_bcc" />
                        </p>
                        <p>
                            <label for="objet"><strong>Objet du mail</strong></label><br/>
                            <input id="objet" autocomplete="off" type="text" name="objet" />
                        </p>
                        <p>
                            <label for="contenu"><strong>Contenu du mail (en HTML) *</strong></label><br/>
                            <textarea id="contenu" autocomplete="off" class="mt-2" name="contenu" style="height:5em" required="required"></textarea>
                        </p>
                        <p><input class="button" type="submit" value="Envoyer"></p>
                    </form>
                <?php endif ?>

                <p class="mt-10" style="font-size:12px">*Ce site permet d'utiliser l'envoi des emails en PHP. N'importe qui possédant un serveur web (PHP), même local, peut faire de même depuis son serveur. Il suffit de suivre la documentation officiel de la fonction "mail" de PHP. Ce site ne souhaite pas être utilisé à des fins malveillantes. Afin d'éviter toute utilisation abusive, un historique des mails envoyés a été mis en place. Si vous souhaitez que votre adresse email ne soit pas utilisable comme adresse source depuis ce site, merci d'envoyez un email à l'adresse <a href="mailto:contact@julien-giraud.fr" title="Envoyer un email à contact@julien-giraud.fr">contact@julien-giraud.fr</a></p>

            </div>
        </section>

        <footer>
            <div class="footer-container">
                <div>
                    <span class=''>Julien Giraud | Étudiant développeur lyonnais</span>
                </div>
                <div class="footer-logos">
                    <a href="https://github.com/juliengiraud" title="GitHub - Julien Giraud">
                        <i class="fa fa-github-square fa-2x"></i>
                    </a>
                    <a href="https://www.julien-giraud.fr" title="Accueil - Julien Giraud Développeur">
                        <img src="<?= $adresse ?>/images/julien-giraud-developpeur-rubiks-cube.svg" alt="Logo Rubik's Cube Développeur" class="logo-footer">
                    </a>
                    <a href="https://www.linkedin.com/in/julien-giraud" title="LinkedIn - Julien Giraud">
                        <i class="fa fa-linkedin-square fa-2x"></i>
                    </a>
                </div>
                <div class="footer-copyright">
                    <span class="tiret-footer">© Tous droits réservés</span>
                    <span title="Dernière mise à jour : <?= $lastupdate ?>">2019 / 2020</span>
                </div>
            </div>
        </footer>

        <!-- footer JS -->
        <script src="<?= $adresse ?>/javascript/main.js"></script>
    </body>
</html>
