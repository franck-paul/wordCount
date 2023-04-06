# Plugin wordCount pour Dotclear 2

[![Release](https://img.shields.io/github/v/release/franck-paul/wordCount)](https://github.com/franck-paul/wordCount/releases)
[![Date](https://img.shields.io/github/release-date/franck-paul/wordCount)](https://github.com/franck-paul/wordCount/releases)
[![Issues](https://img.shields.io/github/issues/franck-paul/wordCount)](https://github.com/franck-paul/wordCount/issues)
[![Dotclear](https://img.shields.io/badge/dotclear-v2.24-blue.svg)](https://fr.dotclear.org/download)
[![Dotaddict](https://img.shields.io/badge/dotaddict-official-green.svg)](https://plugins.dotaddict.org/dc2/details/wordCount)
[![License](https://img.shields.io/github/license/franck-paul/wordCount)](https://github.com/franck-paul/wordCount/blob/master/LICENSE)

Affiche le nombre de caractères, de mots, de folios et la durée estimée de lecture d'un billet sur la base d'une moyenne de 230 mots lu par minute (valeur réglable pour le blog dans les options du plugin).

Ces informations sont affichées sous le billet en cours d'édition côté administration, si le plugin est actif.
De plus, si l'option détail est activée, ces informations sont données également pour l'extrait et pour le contenu.

Une option (active par défaut) permet de mettre à jour à la volée les compteurs pendant l'édition des billets/pages.

Une balise template est disponible pour l'intégration de ces informations dans le thème :

```html
{{tpl:WordCount [attributs]}}
```

les attributs pouvant être :

- chars="0|1" : affiche le nombre de caractères (0 = défaut)
- words="0|1" : affiche le nombre de mots (1 = défaut)
- folios="0|1" : affiche le nombre de folios (0 = défaut), 1 folio étant égal à 750 signes (espaces et ponctuations comprises)
- time="0|1" : affiche le temps de lecture estimé en minutes (0 = défaut)
- wpm="nnn" : nombre de mots lu en moyenne par minute (utilise les réglages du plugin par défaut)
- list="0|1" : utilise une liste non ordonnée pour afficher les compteurs (0 = défaut)

Example pour afficher le temps de lecture du billet (en minutes), avec une vitesse de 300 mots / minute :

```html
<p><strong>{{tpl:lang reading time:}}</strong> {{tpl:WordCount words="0" time="1" wpm="300"}}</p>
```

Notez que les informations affichées par la balise concernent l'intégralité du billet (extrait + contenu), et que cette balise reste active même si l'affichage est désactivé côté administration.

Un widget est également disponible et permet cet affichage pour le billet et/ou la page en cours d'affichage.
