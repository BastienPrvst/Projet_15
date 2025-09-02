# Contribuer au projet InaZaoui

## Marche à suivre

- Cloner le projet de votre côté en local `git clone https://github.com/BastienPrvst/Projet_15.git`
- Mettez-vous sur la branche "dev", la branche "main" est celle qui est par la suite déployée.

### Si vos modifications sont mineures (ex : modification CSS, etc.)

- Commitez vos changements sur la branche "dev" directement, puis faites un pull request sur la branche main. Celui-ci déclenchera la CI/CD et effectuera des tests phpUnit et phpStan.
- Si ceux-ci échouent, corrigez-les et exécutez de nouveau les instructions ci-dessus.

### Si vos modifications sont majeures (ex : ajout d'une fonctionnalité)

- Générez une nouvelle branche, avec un nom explicite et concis.
- Faites vos modifications sur cette branche.
- Une fois ces modifications terminées, effectuez un pull request sur la branche **dev**.
- Si des problèmes de fusion apparaissent, corrigez-les.
- Une fois ceci fait, vous pouvez effectuer un pull request sur la branche main. Celui-ci déclenchera la CI/CD et effectuera des tests phpUnit et phpStan.

### La validation des PR

Avant de les valider, pensez à bien les faire relire, ou à vous relire si vous êtes seul sur le projet. Vérifiez vos tests et vos analyses de code.

### De manière générale : 

- Pensez à utiliser des noms de commits conventionnels, concis et explicites, du type "refactor login method".  
  Ma méthode personnelle est de toujours commencer avec le verbe de l'action effectuée.

- À noter que la CI de vérification peut aussi être utilisée sans PR et sur n'importe quelle branche.  
Pour faire ceci, allez dans "Actions", puis cliquez sur "Symfony" à gauche et enfin "Run Workflow" à droite. Choisissez la branche que vous voulez tester et lancez le workflow.

- Durant toute votre phase de développement, vous pouvez (et c'est recommandé) mettre à jour les tests et en ajouter selon vos changements.

- Pour lancer les tests phpUnit :  
  `php vendor/bin/phpunit --coverage-html public/test-coverage`  
  Ceci générera un rapport de code en HTML situé dans `public/code-coverage`

- Pour l'analyse phpstan :  
  `php vendor/bin/phpstan analyse src test`  
  Ceci analyse votre code en simulant son utilisation (statique).  

À noter que si certaines erreurs n'en sont pas selon vos critères, vous pouvez régénérer la baseline pour ne plus afficher ces erreurs lors de prochains tests :  
`php vendor/bin/phpstan analyse src test --generate-baseline` 
