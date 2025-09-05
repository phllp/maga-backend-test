# Considerações

Destino esse arquivo para algumas considerações pessoais a respeito do desenvolvimento do projeto, estas incluem declarações sobre minha experiência e possíveis divergências com os requisitos que podem ser identificadas no sistema.

## Divergências

**1. Tipo do campo `tipo`, na entidade Contato**

A modelagem apresentada na imagem no github apresenta o campo como sendo um boolean, porém optei por implementar o mesmo como um Enum, pois o tipo boolean estava adicionando complexidade na implementação e além do mais o tipo Enum permite mais flexibilidade, caso um novo tipo de Contato precise ser adicionado, a alteração é fácil e além disso o tipo boolean só permite dois valores.

**2. Tela de consulta de Contatos**

Lendo os requisitos não entendi se a tela de consulta era para listar todos os contatos de todas as pessoas ou todos os contatos de uma determinada pessoa, seguindo minha intuição segui pela segunda opção.

**3. Ação de Visualizar Pessoa**

Optei por remover a ação de "Visualizar Pessoa" levando em consideração o fato de que ela seria redundante para essa aplicação, sendo basicamente uma cópia da ação de alterar com os campos readonly. Os dados das pessoas são visíveis na tabela e a visualização dos contatos (que poderia justificar uma implementação desta ação de Visualização) é feita perfeitamente a partir da ação "Contatos", com uma UI agradável e com uma boa experiência de usuário.

A mesma lógica se aplica para a visualização de Contatos individuais.

## Experiência

Nunca havia trabalhado com php em tão baixo nível, sem a assistência de nenhum framework, e a experiência foi muito interessante e divertida. Comparar o desenvolvimento dessa aplicação com o que estou acostumado a fazer com React, Typescript e Node foi muito enriquecedor, as vezes tomamos certas coisas como garantidas e é bom relembrar como as coisas funcionam quando abrimos mão de frameworks e libs.

Tentei deixar tudo o mais baixo nível possível, a única biblioteca externa que utilizo é o TailwindCSS para me ajudar com a estilização. Levei algum tempo para encontrar forma de aplicar o MVC com as limitações presentes, acredito que o resultado final ficou satisfatório, embora a tentação de criar uma SPA com React e realizar chamadas para o backend diretamente foi grande.
