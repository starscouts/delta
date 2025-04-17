<?php

$title = "lang_home_title";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/header.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/navigation.php";
global $_PROFILE; global $_USER;

$version = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/version");

?>

<div class="container">
    <br><br>
    <h1><?= l("lang_home_greeting") ?> <?= $_PROFILE["nick_name"] ?? $_PROFILE["first_name"] . " " . $_PROFILE["last_name"] ?></h1>

    <details class="alert alert-danger">
        <summary>
            <b>Arrêt progressif de service pour la plate-forme Delta et les services associés</b>
        </summary>

        <p style="margin-top: 1rem;">Nous avons le regret de vous annoncer la fermeture de Delta le 16 juin 2024, avec une désactivation progressive des fonctionnalités sur les prochains mois à raison d'une fonctionnalité par semaine, ainsi que la migration du contenu vers une plate-forme plus économe (avec cependant moins de contrôle).</p>
        <ul>
            <li style="opacity: .25;">31 mars : Retrait de l'offre de support technique gratuit pour les utilisateurs Delta Ultra. Le prix de l'abonnement Delta Ultra sera abaissé à 6 pièces par mois (soit 1,50€ par mois).</li>
            <li style="opacity: .25;">7 avril : Désactivation de Delta Studio. Le prix de l'abonnement Delta Ultra sera abaissé à 5 pièces par mois (soit 1,25€ par mois).</li>
            <li style="opacity: .25;">14 avril : Retrait de Delta Ultra (et par conséquent de l'option de surnom). Les abonnements Delta Ultra en cours de validité seront convertis en abonnements Delta Plus.</li>
            <li style="opacity: .25;">21 avril : Désactivation de Delta Beta. Étant donné qu'il n'y a, à l'heure actuelle, aucune fonctionnalité expérimentale, la page correspondante est simplement retirée. Le prix de l'abonnement Delta Plus sera abaissé à 3 pièces par mois (soit 0,75€ par mois).</li>
            <li style="opacity: .25;">28 avril : Activation de l'option d'impression des pages pour tous les utilisateurs de Delta. Le prix de l'abonnement Delta Plus sera abaissé à 2 pièces par mois (soit 0,50€ par mois).</li>
            <li style="opacity: .25;">5 mai : Retrait de Delta Plus (et par conséquent du badge de profil et des relectures privilégiées). Les abonnements Delta Plus en cours de validité expireront.</li>
            <li style="opacity: .25;">12 mai : Désactivation de l'option d'aide intégrée. À partir de ce jour, aucun support technique ne sera fourni aux utilisateurs de Delta.</li>
            <li style="opacity: .25;">19 mai : Retrait du Pass Événements. Les intégrations dans des applications tierces utilisant le Pass Événements cesseront de fonctionner.</li>
            <li style="opacity: .25;">26 mai : Retrait du système de recherche.</li>
            <li style="opacity: .25;">2 juin : Désactivation des albums de galerie. Étant donné qu'aucun album n'existe actuellement, la page est simplement retirée.</li>
            <li style="font-weight: bold;">9 juin : Désactivation de toutes les fonctionnalités de modification de Delta. Pour modifier du contenu après cette date, contactez les administrateurs.</li>
            <li>16 juin : Fermeture définitive de Delta</li>
        </ul>
        <div>Si vous avez la moindre question ou le moindre renseignement à nous faire parvenir, utilisez l'adresse <a href="mailto:delta@equestria.dev">delta@equestria.dev</a>.</div>
    </details>

    <?php if (str_contains($version, "rc") ||
              str_contains($version, "eap") ||
              str_contains($version, "beta") ||
              str_contains($version, "dev")): ?>
    <div class="alert alert-secondary">
        <b><?= l("lang_experimental_title") ?></b> <?= str_replace("%2", '</a>', str_replace("%1", '<a href="/support">', l("lang_experimental_message"))) ?>
    </div>
    <?php endif; ?>

    <div id="homepage-content"></div>

    <div style="display: flex; align-items: center; justify-content: center; margin-top: 30px;" id="homepage-loader">
        <svg class="spinner" width="32px" height="32px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
            <circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
        </svg>
    </div>

    <br><br><br>

    <script>
        window.list = [
            "birthday.php",
            "family.php",
            "history.php",
            "recent.php"
        ];

        function nextItem() {
            let item = list[0];

            try {
                window.fetch("/home/" + item).then((res) => {
                    res.text().then((data) => {
                        document.getElementById("homepage-content").innerHTML += data;
                        list.shift();
                        if (list.length > 0) {
                            nextItem();
                        } else {
                            document.getElementById("homepage-loader").style.display = "none";
                        }
                    });
                })
            } catch (e) {
                list.shift();
                if (list.length > 0) {
                    nextItem();
                } else {
                    document.getElementById("homepage-loader").style.display = "none";
                }
            }
        }

        window.addEventListener("load", () => {
            nextItem();
        });
    </script>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>
