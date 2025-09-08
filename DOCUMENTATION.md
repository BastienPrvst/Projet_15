# Documentation

## La sécurité

La sécurité des routes concernant les roles est dans le fichier security.yaml, j'ai préféré dissocier les controller de la sécurité (on aurait pu faire des IS_GRANTED).
Pensez à bien mettre à jour ce fichier si vous ajoutez des pages ou des rôles.

## Les routes 

Concernant le nom des route, veillez à bien respecter le type d'entité dans le path:  
Ex : `#[Route(path: '/guest/{user} <- ici', name: 'guest')]`  
Et vous utilisez un ID pour générer le lien.  
`<a href="{{ path('guest', {user: guest.id}) `

Pour ce qui est du nom des routes dans la partie admin, veillez à ce qu'il commence bien par "admin_"  
Ex :   
`#[Route('/admin/user', name: 'admin_user_index')]`  
`#[Route(path: '/admin/user/add', name: 'admin_user_add')]`

## Les tests

Concernant les tests fonctionnels, j'ai utilisé le crawler de Symfony, permettant de parcourir le HTML sans avoir besoin de generer de visuel.  
Ce n'est peut être pas le meilleur outil, si vous n'êtes pas à l'aise avec, vous pouvez aussi bien utiliser DOMXPath ou Panther. Veillez juste à ce que les test restent cohérents dans leurs logique finale.

L'utilisation de tests fonctionnels est fortement conseillé dans le cas ou vous ajouteriez une ou plusieurs fonctionnalités.  
L'ajout de DAMA Doctrine (https://github.com/dmaicher/doctrine-test-bundle) vous permet de ne pas vous soucier de supprimer, de modifier ou d'ajouter des entités. Il n'y a rien de particulier à faire, l'outil se charge de ça.
Essayez de garder une couverture de code aux alentours de 70%+, même si cela prends du temps vous le récupererez par la suite grâce à ça.

## Les fixtures

Les fixtures sont modifiables en fonction de vos besoins, n'hesitez pas à les modifier si nécéssaire (augmenter ou réduire le nombre d'entité etc).  
De plus, j'ai mis en place un systeme d'image qui pourrait certainement être amélioré mais qui pour l'instant fonctionne. Il s'agit de 30 image qui sont donc utilisées pour donner leurs chemin aux médias, évitant ainsi les centaines d'images différentes.  
Elles ne seront pas supprimées réelement en dev et en test grâce à ces lignes présentent dans le MediaController:  
<pre lang="md">if (in_array($_SERVER['APP_ENV'], ['dev', 'test'], true)) { 
			if (file_exists($media->getPath()) && !str_contains($media->getPath(), 'fix-')) {
				unlink($media->getPath());
			}
		} elseif (file_exists($media->getPath())) {
			unlink($media->getPath());
		}`
</pre>
Pensez cependant à bien nommer vos fichier "fix-nom_de_l_image" pour que ceci fonctionne.

## Conclusion 

Essayez de garder la même forme de logique dans le projet, tout en gardant bien sur un oeil critique sur de potentielles améliorations.
Le projet est sain et permets de nombreuses améliorations futures, vous allez vous en sortir!
