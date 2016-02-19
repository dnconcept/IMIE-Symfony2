
##  Remplacer les templates d'autres Bundle

Exemple : On remplace les templates d'erreurs 404 par notre page d'erreur<br>
[TwigBundle]    ./vendor/symfony/symfony/src/Symfony/Bundle/<b>TwigBundle/Resources/views/Exception/error.html.twig</b><br>
[Application]   ./app/<b>Resources/TwigBundle/views/Exception/error.html.twig</b><br>

## Installer les assets d'un bundle

Exécuter `php app/console assets:install --symlink` en tant qu'administrateur.

## Resources

### Vidéos & tutoriaux

* Dependency injection<br>
<a href="http://www.youtube.com/watch?feature=player_embedded&v=DcNtg4_i-2w" target="_blank"><img src="http://img.youtube.com/vi/DcNtg4_i-2w/0.jpg" alt="Dependency Injection" width="300" height="220" border="10" /></a>

### Plugins PHPStorm à installer

* Install the plugin by going to Settings -> Plugins -> Browse repositories and then search for Symfony
- [1] : https://github.com/Haehnchen/idea-php-symfony2-plugin

### Linux - Configuration Virtual hosts

Les hôtes virtuels (virtual hosts) avec Apache2     https://doc.ubuntu-fr.org/tutoriel/virtualhosts_avec_apache2

### Bundles à voir

CoreSphereConsoleBundle

- [CoreSphereConsoleBundle] : https://github.com/CoreSphere/ConsoleBundle
- [FOSUserBundle]           : https://github.com/FriendsOfSymfony/FOSUserBundle

https://packagist.org/packages/symfony/twig-bridge


### Problème de cache

Suppression total du cache : php app/console cache:clear --no-warmup
Rafraichissement du cache  : php app/console cache:warmup

https://github.com/symfony/symfony/issues/12893

