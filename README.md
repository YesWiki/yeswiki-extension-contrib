# yeswiki-extension-contrib

> Des bonnes idées d'actions et de templates à réutiliser dans vos projets. La maintenance est effectuée par la communauté YesWiki.

# Actions

## `{{ sonogramme }}`

Visualisation interactive réalisée à partir d'un fichier audio.

![Exemple de sonogramme généré depuis un fichier audio](presentation/action-sonogramme.png)

## `{{ limitentries }}`

Limite la saisie de fiches a un certain nombre indiqué (attention: ne marche qu'a travers cette action, on peut toujours ajouter des fiches par l'admin de bazar, par exemple..).

Les parametres `id` et `limit` sont obligatoires, `message_max` et `message_count` optionnels

Exemple complet
```
{{limitentries id="1" limit="30" message_max="Il n'y a plus de places disponible, la limite était %{limit}, désolé..." message_count="Réservation %{nb} sur %{limit}"}}
```

# Templates

 - compteur_simple.tpl.html : indique uniquement le nombre de fiches trouvées
 - photoswipe.twig : galerie photo moderne