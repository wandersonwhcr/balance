# Contribuindo

Então você quer contribuir com este projeto? Muito obrigado pelo seu interesse! Aqui estão descritos alguns padrões que são utilizados durante o meu desenvolvimento e que eu gostaria que você também seguisse. Então, vamos lá!

## Estrutura do Repositório

Este projeto é controlado com _tags_ no formato de versionamento semântico e 2 _branches_ principais: `master` e `develop`. Todas as _tags_ representam ambientes estáveis e a última _tag_ sempre aponta para o _branch_ `master`. Todos os _branches_ saem do _branch_ `master` e retornam para o _branch_ `develop`. Quando uma _tag_ é finalizada, todos os _branches_ que não retornaram deverão sofrer um _rebase_ para o _branch_ `master`.

Certo, mas o que isso quer dizer?

Eu tento sempre manter a melhor árvore Git possível neste projeto. Você pode visualizá-la acessando a [linha de desenvolvimento](//github.com/wandersonwhcr/balance/network) do projeto. Conforme visualização, todos os _branches_ saem da última _tag_ e retornam antes da próxima _tag_. Assim, eu consigo ter uma linha de desenvolvimento concisa e de ótima visualização em _softwares_ como o Gitk.

Lembre-se dessa estrutura! Ela é muito importante!

## Nomenclatura de Referências

Somente 2 _branches_ possuem nomes fora do padrão: o `master` e o `develop`. Todos os outros _branches_ devem possuir o formato `issue-[1-9][0-9]*`, representando o número da _issue_ que gerou aquele _branch_. Portanto, todos os _branches_ criados devem ser resultantes da abertura de uma _issue_ no projeto. Descrevendo melhor, todos os _branches_ devem iniciar com `issue-` e finalizar com o número da _issue_ que gerou aquele _branch_.

Por exemplo, se existe uma _issue_ 42 que irá gerar uma alteração de código, o _branch_ que será criado terá o nome `issue-42`.

Como eu não utilizo um _fork_ do projeto e efetuo _commits_ diretamente, em alguns momentos você poderá encontrar _branches_ neste padrão. Ignore, são os meus _branches_. Não utilize-os, eles são extremamente instáveis e podem causar uma fissão nuclear.

As _tags_ obedecem ao [versionamento semântico](http://semver.org/) comum aos projetos _opensource_ disponíveis. A última _tag_ sempre será o projeto mais estável e todas elas apontarão para o _branch_ `master`.
